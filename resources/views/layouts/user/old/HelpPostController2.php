<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelpPost;

class HelpPostController2 extends Controller
{
public function index()
{
    // You can pass user data here if needed
    return view('layouts.user.help_post');
}   

//string data
public function store(Request $request)
{
    $request->validate([
        'post_category' => 'required|string|max:255',
        'post_title' => 'required|string|max:255',
        'post_description' => 'required|string',
        'location' => 'nullable|string',
    ]);

HelpPost::create([
    'user_id' => Auth::id(),
    'post_category' => $request->post_category,
    'post_title' => $request->post_title,
    'post_description' => $request->post_description,
    'post_location' => $request->location, // make sure the form input is named "location"
    'post_creation_time' => now(),
]);

    return redirect()->route('posts.show', ['type' => 'my'])->with('success', 'Post created successfully.');
}


// //showing all post
// public function show_allPosts()
// {
//     $currentUserId = Auth::id();

//     $helpPosts = HelpPost::with('user') // Eager load related user data
//         ->where('user_id', '!=', $currentUserId)
//         ->orderBy('post_creation_time', 'desc')
//         ->paginate(15);

//     return view('layouts.user.all_post', compact('helpPosts'));
// }

// //showing details
// public function show_details($post_id)
// {
//     $helpPosts = HelpPost::findOrFail($post_id);
//     return view('layouts.user.post_details', compact('helpPosts'));
// }

// //showing my posts
// public function show_mypost()
// {
//     $userId = Auth::id(); // Get logged-in user ID
//    $helpPosts = HelpPost::where('user_id', $userId)->orderBy('post_creation_time', 'desc')->paginate(15);
// return view('layouts.user.my_post', compact('helpPosts'));
// }

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
        'location' => 'nullable|string',
    ]);

    $helpPosts = HelpPost::where('user_id', Auth::id())->findOrFail($post_id);
    $helpPosts->post_title = $request->post_title;
    $helpPosts->post_description = $request->post_description;
    $helpPosts->post_category = $request->post_category;
    $helpPosts->post_location = $request->location;
    $helpPosts->save();
return redirect()->route('post_edit', ['post_id' => $helpPosts->post_id])
                     ->with('success', 'Post updated successfully.');
}

//deleting post
public function destroy($post_id)
{
    $post = HelpPost::where('user_id', Auth::id())->findOrFail($post_id);
    $post->delete();
   return redirect()->route('posts.show', ['type' => 'my'])->with('danger', 'Post deleted successfully.');
}

//Preferrence post
// public function show_preferrence_post()
// {
//     $user = Auth::user();

//     $preferredCategories = $user->help_areas; // Casted as array in User model
//     $userLocation = $user->location;
//     $userVolunteering = $user->is_volunteer;

//     $helpPosts = HelpPost::where(function ($query) use ($preferredCategories, $userLocation, $userVolunteering) {
//         // Match any of the conditions
//         $query->whereIn('post_category', $preferredCategories)
//               ->orWhere('post_location', $userLocation);

//         if ($userVolunteering) {
//             $query->orWhere('post_category', 'Volunteer');
//         }
//     })
//     ->orderBy('post_creation_time', 'desc')
//     ->paginate(15);

//     return view('layouts.user.dashboard', compact('helpPosts'));
// }

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
  public function showPosts($type)
{
    $currentUserId = Auth::id();

    switch ($type) {
        case 'my':
            $postsQuery = HelpPost::where('user_id', $currentUserId)
                ->orderBy('post_creation_time', 'desc');
            break;

        case 'preference':
            $user = Auth::user();
            $preferredCategories = $user->help_areas;
            $userLocation = $user->location;
            $userVolunteering = $user->is_volunteer;

            $postsQuery = HelpPost::where(function ($query) use ($preferredCategories, $userLocation, $userVolunteering) {
                $query->whereIn('post_category', $preferredCategories)
                      ->orWhere('post_location', $userLocation);

                if ($userVolunteering) {
                    $query->orWhere('post_category', 'Volunteer');
                }
            })->orderBy('post_creation_time', 'desc');
            break;

        case 'all':
            $postsQuery = HelpPost::with('user')
                ->where('user_id', '!=', $currentUserId)
                ->orderBy('post_creation_time', 'desc');
            break;

        default:
            abort(404);
    }

    $helpPosts = $postsQuery->paginate(15);

    return view('layouts.user.show_post', compact('helpPosts', 'type'));
}
}