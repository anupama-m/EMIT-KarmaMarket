<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelpPost;
use App\Models\User;
use App\Models\Donation;

class HelpPostController extends Controller
{
    
public function index()
{
  $categories = \App\Models\Category::where('type', 'help')->get();
    return view('layouts.user.help_post', compact('categories'));
}   

//storing data
public function store(Request $request)
{
    // Fetch category from DB
    $category = \App\Models\Category::where('name', $request->post_category)
                                     ->where('type', 'help') // ensure it's a help category
                                     ->first();

    if (!$category) {
        return redirect()->back()->with('error', 'Invalid category selected.');
    }

    // Validation
    $rules = [
        'post_category' => 'required|string|max:255|exists:categories,name',
        'post_title' => 'required|string|max:255',
        'post_description' => 'required|string',
        'location' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ];

    // Extra rules for blood category
    if ($request->post_category === 'blood') {
        $rules['blood_group'] = 'required|string|max:3';
        $rules['hospital_name'] = 'required|string|max:255';
    }

    $request->validate($rules);

    // Use points from category table
    $points = $category->points ?? 10;

    // Create post
    $post = HelpPost::create([
        'user_id' => Auth::id(),
        'post_category' => $request->post_category,
        'post_title' => $request->post_title,
        'post_description' => $request->post_description,
        'post_location' => $request->location,
        'latitude'=> $request->latitude,
        'longitude' => $request->longitude,
        'post_creation_time' => now(),
        'blood_group' => $request->input('blood_group'),
        'hospital_name' => $request->input('hospital_name'),
        'points' => $points,
        'status' => 'open'
    ]);

    return redirect()->route('post_details', ['post_id' => $post->post_id])
                     ->with('success', 'Post created successfully');
}


// Show the edit form
public function edit($post_id)
{
    $helpPosts = HelpPost::where('user_id', Auth::id())->findOrFail($post_id);
    return view('layouts.user.edit_helppost', compact('helpPosts'));
}

// Handle update
public function update(Request $request, $post_id)
{
    $request->validate([
        'post_category' => 'required|string|max:255',
        'post_title' => 'required|string|max:255',
        'post_description' => 'required|string',
        'post_location' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric'
    ]);

    $helpPosts = HelpPost::where('user_id', Auth::id())->findOrFail($post_id);
    $helpPosts->post_title = $request->post_title;
    $helpPosts->post_description = $request->post_description;
    $helpPosts->post_category = $request->post_category;

     // update lat/long if provided
    if ($request->filled('latitude') && $request->filled('longitude')) {
        $helpPosts->latitude = $request->latitude;
        $helpPosts->longitude = $request->longitude;
    }
    // keep old location if empty
    $helpPosts->post_location = $request->post_location ?? $helpPosts->post_location;

    $helpPosts->save();

    return redirect()->route('post_edit', ['post_id' => $helpPosts->post_id])
                     ->with('success', 'Post updated successfully.');
}


//deleting post
public function destroy($post_id)
{
    $post = HelpPost::where('user_id', Auth::id())->findOrFail($post_id);
    $post->delete();
   return redirect()->route('help_posts.show', ['type' => 'my'])->with('danger', 'Post deleted successfully.');
}

 // Get posts by current user
    private function getUserPosts()
    {
        $userId = Auth::id();
        return HelpPost::where('user_id', $userId)
            ->orderBy('post_creation_time', 'desc');
    }

    // Get preference posts based on user preferences
    private function getPreferencePosts()
    {
        $user = Auth::user();
        $preferredCategories = $user->help_areas;
        $userLocation = $user->location;
        $userVolunteering = $user->is_volunteer;

        return HelpPost::where(function ($query) use ($preferredCategories, $userLocation, $userVolunteering) {
            $query->whereIn('post_category', $preferredCategories)
                  ->orWhere('post_location', $userLocation);

            if ($userVolunteering) {
                $query->orWhere('post_category', 'Volunteer');
            }
        })->orderBy('post_creation_time', 'desc');
    }

    // Show posts by type (my, preference, all, etc.)
// public function showPosts($type)
// {
//     $currentUserId = Auth::id();

//     switch ($type) {
//         case 'my':
//             $postsQuery = HelpPost::with(['user', 'approvals', 'pendingRequests'])
//                 ->where('user_id', $currentUserId)
//                 ->orderBy('post_creation_time', 'desc');
//             break;

// case 'preference':
//     $user = Auth::user();
//     $preferredCategories = $user->help_areas; // assumed to be an array
//     $userVolunteering = $user->is_volunteer;

//     $postsQuery = HelpPost::with(['user', 'approvals', 'pendingRequests'])
//         ->where('user_id', '!=', $user->user_id)
//         ->where(function ($query) use ($preferredCategories, $userVolunteering) {
//             // Match preferred help categories
//             $query->whereIn('post_category', $preferredCategories);

//             // If user is a volunteer, include volunteer category too
//             if ($userVolunteering) {
//                 $query->orWhere('post_category', 'volunteer');
//             }
//         })
//         // Exclude posts that already have an accepted or completed approval
//         ->whereDoesntHave('approvals', function ($q) {
//             $q->whereIn('status', ['accepted', 'completed']);
//         })
//         ->orderBy('post_creation_time', 'desc');
//     break;



//         case 'helped':
//             $status = request('status'); // pending, accepted, rejected, completed

//             $postsQuery = HelpPost::with(['user', 'approvals', 'pendingRequests'])
//                 ->whereHas('approvals', function ($query) use ($currentUserId, $status) {
//                     $query->where('helper_id', $currentUserId);
//                     if ($status) {
//                         $query->where('status', $status);
//                     }
//                 })
//                 ->orderBy('post_creation_time', 'desc');
//             break;

//         case 'all':
//             $postsQuery = HelpPost::with(['user', 'approvals', 'pendingRequests'])
//                 ->where('user_id', '!=', $currentUserId)
//                 ->orderBy('post_creation_time', 'desc');
//             break;

//         default:
//             abort(404);
//     }

//     $helpPosts = $postsQuery->paginate(15);

//     return view('layouts.user.show_post', compact('helpPosts', 'type'));
// }


public function helper()
{
    return $this->belongsTo(User::class, 'helper_id');
}
public function show_details($post_id)
{
    $helpPosts = HelpPost::with(['user', 'approvals.helper'])->findOrFail($post_id);
    $userId = auth()->id();
    $isOwner = $helpPosts->user_id === $userId;

    $similarBloodUsers = collect();
    $similarBookUsers_other = collect();
     $similarBookUsers_student = collect();
    $similarVolunteerUsers = collect();
    $recommendations_medical_cloth = collect();
    $similarHelpPosts = collect();

    $requester = $helpPosts->user;
    $lat = $helpPosts->latitude;
    $lng = $helpPosts->longitude;

    if ($isOwner) {
        switch ($helpPosts->post_category) {
            case 'blood':
$similarBloodUsers = User::select('*')
    ->selectRaw("(6371 * acos(
        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
        sin(radians(?)) * sin(radians(latitude))
    )) AS distance", [$lat, $lng, $lat])
    ->where('blood_group', $helpPosts->blood_group)
    ->whereJsonContains('help_areas', 'Blood Donation') // single string, not array
    ->whereNot('user_id', $userId)
    ->orderBy('distance', 'asc')
    ->get();

                break;

            case 'book':
                if ($requester->occupation === 'student') {
                    $similarBookUsers_student = User::where('institution_name', $requester->institution_name)
                        ->where('occupation', 'student')
                        ->where('user_id', '!=', $userId)
                        ->orderByRaw('CAST(year AS UNSIGNED) DESC')
                        ->get();
                } else {
                    // Show closest donation posts with category book
                    $similarBookUsers_other = Donation::select('*')
                        ->selectRaw("(6371 * acos(
                            cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                            sin(radians(?)) * sin(radians(latitude))
                        )) AS distance", [$lat, $lng, $lat])
                        ->where('donation_category', 'book')
                        ->where('status', 'open')
                        ->where('user_id', '!=', $userId)
                        ->orderBy('distance', 'asc')
                        ->get();
                }
                break;

            case 'medical':
            case 'clothes':
                $recommendations_medical_cloth = Donation::select('*')
                    ->selectRaw("(6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )) AS distance", [$lat, $lng, $lat])
                    ->where('donation_category', $helpPosts->post_category)
                    ->where('status', 'open')
                    ->where('user_id', '!=', $userId)
                    ->orderBy('distance', 'asc')
                    ->get();
                break;

            case 'volunteer':
    $similarVolunteerUsers = User::where('is_volunteer', 1)
        ->where('user_id', '!=', $userId)
        ->withCount(['approvals as volunteer_count' => function ($query) {
            $query->where('status', 'completed')
                  ->whereHas('post', function ($q) {
                      $q->where('post_category', 'volunteer');
                  });
        }])
        ->orderByRaw("(6371 * acos(
            cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude))
        )) ASC", [$lat, $lng, $lat])
        ->get();
    break;

        }
    } else {
        // For non-owner posts: show other help posts in same category, open, closest to farthest
        $similarHelpPosts = HelpPost::where('post_category', $helpPosts->post_category)
            ->where('status', 'open')
            ->where('user_id', '!=', $userId)
            ->where('post_id', '!=', $helpPosts->post_id)
            ->select('*')
            ->selectRaw("(6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$lat, $lng, $lat])
            ->orderBy('distance', 'asc')
            ->get();
    }

    return view('layouts.user.post_details', compact(
        'helpPosts',
        'similarBloodUsers',
        'similarBookUsers_student', 'similarBookUsers_other',
        'similarVolunteerUsers',
        'recommendations_medical_cloth',
        'similarHelpPosts',
        'isOwner'
    ));
}


// For initial page load
// In HelpPostController
public function showHelpPosts(Request $request)
{
    $user = auth()->user();

    $helpPosts = HelpPost::with(['user', 'approvals'])
                    ->where('user_id', $user->user_id)
                    ->orderBy('post_creation_time', 'desc') // use your column here
                    ->paginate(5);

    return view('layouts.user.show_post', compact('helpPosts'));
}


// AJAX filtering/search
public function ajaxHelpPosts(Request $request)
{
    $user = auth()->user();
    $type = $request->get('type', 'my');
    $status = $request->get('status', 'all');
    $search = $request->get('search', '');

    $query = HelpPost::with(['user', 'approvals']);

    switch ($type) {
        case 'my':
            $query->where('user_id', $user->user_id);
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            break;

        case 'preference':
    $helpAreas = $user->help_areas ?? [];

    if (!empty($helpAreas)) {
        $query->where('user_id', '!=', $user->user_id)
              ->where('status', 'open')
              ->where(function ($q) use ($helpAreas, $user) {
                  $q->whereIn('post_category', $helpAreas);

                  // If user is volunteer, also include 'Volunteer' posts
                  if ($user->is_volunteer) {
                      $q->orWhere('post_category', 'Volunteer');
                  }
              })
              ->whereDoesntHave('approvals', function ($q) use ($user) {
                  // Exclude posts where user already has a pending approval
                  $q->where('helper_id', $user->user_id)
                    ->where('status', 'pending');
              });
    } else {
        // if no preferred areas, return empty
        $query->whereRaw('0 = 1');
    }
    break;

        case 'helped':
            $query->whereHas('approvals', function($q) use ($user, $status) {
                $q->where('helper_id', $user->user_id);
                if ($status && $status !== 'all') {
                    $q->where('status', $status);
                }
            });
            break;

case 'other':
    $preferred = $user->help_areas ?? [];

    $query->where('user_id', '!=', $user->user_id) // Not user's own
          ->where('status', 'open')               // Only open posts
          ->where(function ($q) use ($preferred, $user) {
              // Exclude preferred areas
              if (!empty($preferred)) {
                  $q->whereNotIn('post_category', $preferred);
              }

              // Exclude 'Volunteer' posts if user IS a volunteer
              if ($user->is_volunteer) {
                  $q->where('post_category', '!=', 'Volunteer');
              }
              // if NOT a volunteer, we do nothing (so Volunteer posts are included)
          })
          ->with(['approvals' => function ($q) use ($user) {
              $q->where('helper_id', $user->user_id); // Load current user's approvals
          }])
          ->whereDoesntHave('approvals', function ($q) use ($user) {
              // Exclude posts where user already has a pending approval
              $q->where('helper_id', $user->user_id)
                ->where('status', 'pending');
          });
    break;

    }

    // Search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('post_title', 'like', "%$search%")
              ->orWhere('post_id', 'like', "%$search%")
              ->orWhere('post_category', 'like', "%$search%");
        });
    }

    $helpPosts = $query->orderBy('post_creation_time', 'desc')->paginate(10);

    return view('layouts.user.help_post_items', [
        'helpPosts' => $helpPosts,
        'type' => $type,
        'status' => $status,
    ]);
}


}
