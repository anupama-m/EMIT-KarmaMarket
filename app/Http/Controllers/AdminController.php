<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\HelpPost;
use App\Models\Donation;
use App\Models\User;

class AdminController extends Controller
{
    // Categories

    public function index()
    {
        return view('admin.categories');
    }
    public function categories()
    {
        $helpCategories = Category::where('type', 'help')->get();
        $donationCategories = Category::where('type', 'donation')->get();

        return view('admin.manage_categories', compact('helpCategories', 'donationCategories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:help,donation',
            'points' => 'required|integer|min:0',
        ]);

        Category::create($request->only('name', 'type', 'points'));
        return back()->with('success', 'Category added successfully.');
    }

    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Category deleted.');
    }

    // Show posts page
    public function posts()
    {
        $posts = HelpPost::with('user')->get()
            ->map(fn($post) => tap($post, fn($p) => $p->type = 'help'))
            ->merge(
                Donation::with('user')->get()->map(fn($post) => tap($post, fn($p) => $p->type = 'donation'))
            );

        return view('admin.manage_posts', ['posts' => $posts]);
    }


    // AJAX: fetch filtered/searched posts
    public function ajaxPosts(Request $request)
    {
        $search = trim($request->query('search', ''));

       // HELP POSTS
// HELP POSTS
$helpPosts = HelpPost::with('user')
    ->where(function ($query) use ($search) {
        $query->where('post_title', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('post_id', 'like', "%{$search}%")
              ->orWhereRelation('user', 'username', 'like', "%{$search}%");
    })
    ->get()
    ->each(function ($p) { $p->type = 'help'; });

// DONATION POSTS
$donationPosts = Donation::with('user')
    ->where(function ($query) use ($search) {
        $query->where('donation_title', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%")
              ->orWhereRelation('user', 'username', 'like', "%{$search}%");
    })
    ->get()
    ->each(function ($p) { $p->type = 'donation'; });

        $posts = $helpPosts->merge($donationPosts);

        // return JSON if you want to debug:
        // return response()->json($posts);

        return view('admin.posts_table', compact('posts'));
    }


    // Delete post safely
    public function destroyPost($id)
    {
        if ($post = HelpPost::find($id)) {
            $post->delete();
        } elseif ($post = Donation::find($id)) {
            $post->delete();
        } else {
            return back()->with('error', 'Post not found.');
        }

        return back()->with('success', 'Post deleted.');
    }

    // Show users
    public function manageUsers()
    {
        $users = User::where('role', '!=', 'admin')->get(); // exclude admins
        return view('admin.manage_users', compact('users'));
    }

    // AJAX search
    public function ajaxUsers(Request $request)
    {
        $search = strtolower($request->search ?? '');

        $users = User::where('role', '!=', 'admin') // exclude admins
            ->get()
            ->filter(function ($u) use ($search) {
                return str_contains((string) $u->user_id, $search)
                    || str_contains(strtolower($u->username), $search)
                    || str_contains(strtolower($u->email), $search);
            });

        return view('admin.users_table', compact('users'));
    }

    // Delete user
    public function destroyUser($id)
    {
        $user = User::where('role', '!=', 'admin')->find($id); // ensure only non-admin can be deleted
        if ($user) {
            $user->delete();
            return back()->with('success', 'User deleted.');
        }
        return back()->with('error', 'User not found or cannot delete admin.');
    }

}