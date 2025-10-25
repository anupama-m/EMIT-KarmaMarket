<?php

namespace App\Http\Controllers;

use App\Models\HelpPostApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HelpPost;
use App\Models\Notification;

class HelpPostApprovalController extends Controller
{
    public function store($post_id)
    {
        $user = Auth::user();

        $post = HelpPost::findOrFail($post_id);
        if ($post->user_id == $user->user_id) {
            return redirect()->back()->with('error', 'You cannot help your own post.');
        }

        $exists = HelpPostApproval::where('post_id', $post_id)
            ->where('helper_id', $user->user_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'You already offered help.');
        }

        HelpPostApproval::create([
            'post_id' => $post_id,
            'helper_id' => $user->user_id,
        ]);

        return redirect()->back()->with('success', 'Your help offer has been sent!');
    }

public function viewApprovals($post_id)
{
    $post = HelpPost::with('user')->findOrFail($post_id);

    if (Auth::id() != $post->user_id) {
        abort(403, 'Unauthorized');
    }

    $lat = $post->latitude;
    $lng = $post->longitude;

    $requests = HelpPostApproval::with('helper')
        ->where('post_id', $post_id)
        ->select('help_post_approvals.*')
        ->join('users', 'users.user_id', '=', 'help_post_approvals.helper_id')
        ->orderByRaw("(6371 * acos(
            cos(radians(?)) * cos(radians(users.latitude)) * cos(radians(users.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(users.latitude))
        )) ASC", [$lat, $lng, $lat])
        ->get();

    return view('layouts.user.approvals', compact('post', 'requests'));
}


    public function accept($approval_id)
{
    $approval = HelpPostApproval::findOrFail($approval_id);

    // Ensure the logged-in user is the post owner
    if (Auth::id() != $approval->post->user_id) {
        abort(403, 'Unauthorized');
    }

    $post = $approval->post;

    // Accept the selected helper
    $approval->status = 'accepted';
    $approval->save();

    // Update post status to in-progress
    $post->status = 'in-progress';
    $post->save();

    // Notify the accepted helper
    Notification::create([
        'user_id' => $approval->helper_id,
        'type' => 'help_accepted',
        'message' => 'Your help offer for "' . $post->post_title . '" was accepted!',
        'read' => false,
    ]);

    // Reject all other helpers
    $rejectedApprovals = HelpPostApproval::where('post_id', $approval->post_id)
        ->where('approval_id', '!=', $approval_id)
        ->get();

    foreach ($rejectedApprovals as $reject) {
        $reject->status = 'rejected';
        $reject->save();

        // Notify the rejected helpers
        Notification::create([
            'user_id' => $reject->helper_id,
            'type' => 'help_rejected',
            'message' => 'Sorry, your help offer for "' . $post->post_title . '" was not accepted.',
            'read' => false,
        ]);
    }

    return redirect()->back()->with('success', 'You have accepted a helper and the post is now in-progress.');
}



public function cancelRequest($post_id)
{
    $user = Auth::user();

    $post = HelpPost::with('user', 'approvals')->find($post_id);
    if (!$post) {
        return back()->with('error', 'Post not found.');
    }

    $owner = $post->user;

   $acceptedApproval = $post->approvals->where('status', 'accepted')->first();

   // if own post and Check if any helper was accepted
if ($user->user_id === $owner->user_id) {

    if ($acceptedApproval) {
        // Case 1: Owner cancels accepted helper
        $owner->points -= 10;
        $owner->save();

        // Notify helper
        Notification::create([
            'user_id' => $acceptedApproval->helper_id,
            'type' => 'help_cancelled',
            'message' => 'The help request on "' . $post->post_title . '" was cancelled by the post owner.',
            'read' => false,
        ]);

        $acceptedApproval->delete();

        $post->status = 'open';
        $post->save();

        return redirect()->route('help_posts.show')
            ->with('success', 'Help request cancelled. 10 points deducted because it was in-progress.');
    } else {
        // Case 2: No one accepted yet
        $post->status = 'open';
        $post->approvals()->delete(); // delete any pending approvals if needed
        $post->delete(); // delete the post

        return redirect()->route('help_posts.show')
            ->with('success', 'Help post deleted successfully.');
    }

}
    // Case 2: Helper cancels their own request (pending or accepted)
    $userApproval = $post->approvals->where('helper_id', $user->user_id)->first();
    if ($userApproval) {
        if ($userApproval->status === 'accepted') {
            $user->points -= 10;
            $user->save();

            $post->status = 'open';
            $post->save();

            $userApproval->delete();

            Notification::create([
                'user_id' => $owner->user_id,
                'type' => 'help_cancelled',
                'message' => $user->username . ' has cancelled their help on your post "' . $post->post_title . '".',
                'read' => false,
            ]);

            return redirect()->route('help_posts.show')
                ->with('success', 'Help cancelled. 10 points deducted because it was in-progress.');
        } else {
            $userApproval->delete();

            return redirect()->route('help_posts.show')
                ->with('success', 'Help request cancelled successfully.');
        }
    }

    return back()->with('error', 'Unauthorized action.');
}



public function confirm($approval_id)
{
    $approval = HelpPostApproval::findOrFail($approval_id);

    if (Auth::id() != $approval->post->user_id) {
        abort(403, 'Unauthorized');
    }

    if ($approval->status !== 'accepted') {
        return back()->with('error', 'Only accepted help can be confirmed.');
    }

    // Update approval status
    $approval->is_confirmed = true;
    $approval->status = 'completed';
    $approval->save();

    // Update the help post status to 'completed'
    $post = $approval->post;
    $post->status = 'completed';
    $post->save();

    // Reward points to helper
    $helper = $approval->helper;
    $helper->points += $post->points;
    $helper->save();

    // Notify helper
    Notification::create([
        'user_id' => $helper->user_id,
        'type' => 'help_confirmed',
        'message' => 'Your help for "' . $post->post_title . '" has been confirmed! You earned ' . $post->points . ' Karma.',
        'read' => false,
    ]);

    return redirect()->route('help_posts.show', ['type' => 'my'])
        ->with('success', 'Help confirmed successfully.')
        ->with('from_confirmation', true);
}

    public function confirmCancelRequest($approval_id)
{
    $approval = HelpPostApproval::findOrFail($approval_id);

    if (Auth::id() !== $approval->post->user_id) {
        abort(403, 'Unauthorized');
    }

    if ($approval->status !== 'cancel_requested') {
        return back()->with('error', 'No cancellation request to process.');
    }

    $approval->delete();

    // Notify helper
    Notification::create([
        'user_id' => $approval->helper_id,
        'type' => 'cancel_confirmed',
        'message' => 'Your cancellation request has been approved for "' . $approval->post->post_title . '" and (5 debucted).',
        'read' => false,
    ]);

         // Debuct points to helper
        $helper = $approval->helper;
        $helper->points -= 5;
        $helper->save();

    return back()->with('success', 'The cancellation request has been confirmed.');

}

}
