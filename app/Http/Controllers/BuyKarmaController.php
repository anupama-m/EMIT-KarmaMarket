<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelpPost;
use App\Models\User;
use App\Models\Donation; // change to your actual model name

class BuyKarmaController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();
        // Fetch posts with pagination
        $postsQuery = Donation::with(['user'])
                ->where('user_id', '!=', $currentUserId)
                ->orderBy('created_at', 'desc');
        // 8 per page, adjust as you like
$posts = $postsQuery->paginate(15);
        return view('layouts.user.buy_karma', compact('posts'));
    }

  public function search_buyKarma(Request $request)
{
    $query = Donation::with('user')
        ->where('user_id', '!=', Auth::id()); // exclude own posts

    // Search
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('donation_title', 'like', "%$search%")
              ->orWhere('donation_category', 'like', "%$search%");
        });
    }

    $posts = $query->orderBy('created_at', 'desc')->paginate(15);

    return view('layouts.user.buy_karma2', compact('posts'))->render();
}
}
