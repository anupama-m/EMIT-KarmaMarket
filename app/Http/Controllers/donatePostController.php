<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelpPost;
use App\Models\User;
use App\Models\Donation;
use App\Models\DonationApproval;
use App\Models\Notification;

class donatePostController extends Controller
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function index()
    {
$categories = \App\Models\Category::where('type', 'donation')->get();
        return view('layouts.user.donate_post', compact('categories'));
    }
    public function store(Request $request)
    {
    // Fetch category from DB
    $category = \App\Models\Category::where('name', $request->donation_category)
                                     ->where('type', 'donation') // ensure it's a help category
                                     ->first();

                                         if (!$category) {
        return redirect()->back()->with('error', 'Invalid category selected.');
    }

        $request->validate([
            'donation_title' => 'required|string|max:255',
            'donation_category' => 'required|string|max:50',
            'donation_description' => 'required|string',
            'donation_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location' => 'required|string',  // make mandatory
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',

        ]);

    $points = $category->points ?? 10;

        $imagePaths = [];

        if ($request->hasFile('donation_images')) {
            foreach ($request->file('donation_images') as $image) {
                $filename = $image->getClientOriginalName(); // original name only
                $image->move(public_path('img'), $filename); // save in public/img
                $imagePaths[] = 'img/' . $filename; // relative path for asset()
            }
        }

        $post = Donation::create([
            'user_id' => Auth::id(),
            'donation_title' => $request->donation_title,
            'donation_category' => $request->donation_category,
            'donation_description' => $request->donation_description,
            'location' => $request->location,
            'latitude' => $request->latitude,      // added latitude
            'longitude' => $request->longitude,    // added longitude
            'points' => $points,
            'donation_images' => $imagePaths ? json_encode($imagePaths, JSON_UNESCAPED_SLASHES) : null,
        ]);

        return redirect()->route('donation_post_details', ['post_id' => $post->id])->with('success', 'Donation post submitted successfully!');
    }
    public function showPosts()
    {
        $currentUserId = Auth::id();

        $posts = Donation::with('user')
            ->where('user_id', $currentUserId)
            ->withCount('approvals') // count total requests
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('layouts.user.donation_user', compact('posts'));
    }


    public function ajaxPosts(Request $request)
    {
        $query = Donation::with(['user', 'approvals']); // Only show current user's posts or requests depending on type 
        if ($request->type === 'my') { // User's own donation posts 
            $query->where('user_id', Auth::id());
            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
        } elseif ($request->type === 'helped') { // Posts the user requested from others 
            $query = Donation::whereHas('approvals', function ($q) use ($request) {
                $q->where('requester_id', Auth::id());
                if ($request->status === 'pending') {
                    $q->where('status', 'pending');
                } elseif ($request->status === 'accepted') {
                    $q->where('status', 'accepted');
                } elseif ($request->status === 'completed') {
                    $q->where('status', 'completed');
                }
            })->with(['user', 'approvals']);
        } // Search filter 
        if ($request->search) {
            $query->where('donation_title', 'like', '%' . $request->search . '%');
        }
        $posts = $query->withCount('approvals')->latest()->paginate(15);
        return view('layouts.user.donation_posts_list', compact('posts'))->render();
    }


    // Show the edit form
    public function edit($post_id)
    {
        $helpPosts = Donation::where('user_id', Auth::id())->findOrFail($post_id);
        return view('layouts.user.edit_donation', compact('helpPosts'));
    }

    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        $request->validate([
            'donation_title' => 'required|string|max:255',
            'donation_description' => 'required|string',
            'donation_category' => 'required|string',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'donation_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // update text fields
        $donation->donation_title = $request->donation_title;
        $donation->donation_description = $request->donation_description;
        $donation->donation_category = $request->donation_category;
        $donation->location = $request->location ?? $donation->location;

        // update lat/long if provided
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $donation->latitude = $request->latitude;
            $donation->longitude = $request->longitude;
        }

        // handle images
        if ($request->hasFile('donation_images')) {
            $imagePaths = [];

            foreach ($request->file('donation_images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('img'), $filename);
                $imagePaths[] = 'img/' . $filename;
            }

            // Replace old images with new ones
            $donation->donation_images = json_encode($imagePaths);
        }

        $donation->save();

        return redirect()
            ->route('donation_post_details', ['post_id' => $donation->id])
            ->with('success', 'Post Updated successfully!');
    }


    public function show_details($post_id)
    {
        $post = Donation::with(['user', 'approvals.requester'])->findOrFail($post_id);

        $currentUserId = auth()->id();
        $latitude = $post->latitude;
        $longitude = $post->longitude;

        // Find accepted or completed request
        $acceptedApproval = $post->approvals()
            ->whereIn('status', ['accepted', 'completed'])
            ->with('requester')
            ->first();

        $relatedPosts = collect(); // default empty

         //  only if current post is open
            if ($post->user_id === $currentUserId) {
                // Owner: show HelpPosts with same category, closest to farthest (only open)
                $relatedPosts = HelpPost::where('post_category', $post->donation_category)
                    ->where('user_id', '!=', $currentUserId)
                    ->where('status', 'open') //  only open
                    ->select('*')
                    ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
                    ->orderBy('distance', 'asc')
                    ->get();
            } else {
                // Not owner: show Donation posts with same category, closest to farthest (only open)
                $relatedPosts = Donation::where('donation_category', $post->donation_category)
                    ->where('user_id', '!=', $currentUserId)
                    ->where('id', '!=', $post->id)
                    ->where('status', 'open') // only open
                    ->select('*')
                    ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
                    ->orderBy('distance', 'asc')
                    ->take(5)
                    ->get();
            }
        

        return view('layouts.user.donation_post_details', compact('post', 'relatedPosts', 'acceptedApproval'));
    }



    public function deletePost(Donation $post)
    {
        $user = Auth::user();

        // Find the accepted approval directly
        $acceptedRequest = $post->approvals()->where('status', 'accepted')->first();


        if ($acceptedRequest) {
            // Deduct 10 points from post owner
            $user->points -= 10;
            $user->save();

            // Notify the requester (thanks to requester() in DonationApproval)
            Notification::create([
                'user_id' => $acceptedRequest->requester->user_id,
                'type' => 'post_deleted',
                'message' => 'The post "' . $post->donation_title . '" you were accepted for has been deleted by the owner.',
                'read' => false,
            ]);
        }
        // Delete the post (approvals should cascade if foreign key set)
        $post->delete();

        return redirect()->route('UserDonationPosts.show')
            ->with('success', $acceptedRequest
                ? 'Post deleted. 10 points deducted because it was in-progress.'
                : 'Post deleted successfully.');
    }


}
