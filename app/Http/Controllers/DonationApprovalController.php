<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationApproval;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationApprovalController extends Controller
{

public function index($post_id)
{
    $donation = Donation::where('id', $post_id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $lat = $donation->latitude;
    $lng = $donation->longitude;

    $approvals = DonationApproval::join('users', 'users.user_id', '=', 'donation_approvals.requester_id')
        ->where('donation_approvals.donation_id', $donation->id)
        ->select('donation_approvals.*', 'users.username', 'users.email', 'users.phone', 'users.location', 'users.latitude', 'users.longitude')
        ->selectRaw("(6371 * acos(
            cos(radians(?)) * cos(radians(users.latitude)) *
            cos(radians(users.longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(users.latitude))
        )) AS distance", [$lat, $lng, $lat])
        ->orderBy('distance', 'asc')
        ->get();

  return view('layouts.user.requests', compact('donation', 'approvals'));
}




    
/*storing request*/
   public function store($donation_id)
{
    $user = Auth::user();
    $donation = Donation::findOrFail($donation_id);

    // Cannot request own donation
    if ($donation->user_id == $user->user_id) {
        return redirect()->back()->with('error', 'You cannot request your own donation.');
    }

    // Check if user already requested
    $exists = DonationApproval::where('donation_id', $donation_id)
        ->where('requester_id', $user->user_id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('info', 'You already requested this donation.');
    }

    // Check if user has enough points
    if ($user->points < $donation->points) {
        return redirect()->back()->with('error', 'You do not have enough points to request this item.');
    }

    // Proceed with request
    DonationApproval::create([
        'donation_id' => $donation_id,
        'requester_id' => $user->user_id,
        'status' => 'pending'
    ]);

    return redirect()->back()->with('success', 'Your request has been sent!');
}

public function accept($approval_id)
{
    $approval = DonationApproval::findOrFail($approval_id);

    // Ensure logged-in user is the donation owner
    if (Auth::id() != $approval->donation->user_id) {
        abort(403, 'Unauthorized');
    }

    // Accept the selected requester
    $approval->status = 'accepted';
    $approval->save();

    // Update the donation post status to 'in-progress'
    $donation = $approval->donation;
    $donation->status = 'in-progress';
    $donation->save();

    // Notify the accepted requester
    Notification::create([
        'user_id' => $approval->requester_id,
        'type' => 'donation_accepted',
        'message' => 'Your request for "' . $donation->donation_title . '" was accepted!',
        'read' => false,
    ]);

    // Automatically reject all other requests
    $rejectedApprovals = DonationApproval::where('donation_id', $donation->id)
        ->where('id', '!=', $approval_id)
        ->get();

    foreach ($rejectedApprovals as $reject) {
        $reject->status = 'rejected';
        $reject->save();

        // Notify the rejected requesters
        Notification::create([
            'user_id' => $reject->requester_id,
            'type' => 'donation_rejected',
            'message' => 'Sorry, your request for "' . $donation->donation_title . '" was not accepted.',
            'read' => false,
        ]);
    }

    return redirect()->back()->with('success', 'You have accepted a requester.');
}

public function confirmDonation($approval_id)
{
    $approval = DonationApproval::findOrFail($approval_id);

    // Get related donation
    $donation = $approval->donation;

    // Ensure logged-in user is the requester
    if (Auth::id() != $approval->requester_id) {
        abort(403, 'Unauthorized');
    }

    if ($approval->status === 'completed') {
        return redirect()->back()->with('info', 'Donation already confirmed.');
    }

    // Update status to 'completed'
    $approval->status = 'completed';
    $approval->save();

    // Update points
    $requester = $approval->requester;
    $owner = $donation->user;

    $requester->points -= $donation->points;
    $requester->save();

    $owner->points += $donation->points;
    $owner->save();

    $donation->status = 'completed';
    $donation->save(); // <-- don’t forget to save this change

    // Optional: notify owner
    Notification::create([
        'user_id' => $owner->user_id,
        'type' => 'donation_confirmed',
        'message' => '#' . $donation->id . ' Donation confirmed. ' . $donation->points . ' points!',
        'read' => false,
    ]);

    return redirect()->back()->with('success', 'Donation confirmed! ' . $donation->points . ' points deducted!');
}


public function cancelRequest(DonationApproval $approval)
{
    $user = Auth::user(); 
    $donation = $approval->donation;
    $owner = $donation->user;

    // Case 1: Owner cancels when the post is in-progress (accepted request)
    if ($user->user_id === $owner->user_id && $approval->status === 'accepted') {
        // Deduct 10 points from owner
        $owner->points -= 10;
        $owner->save();

        // Notify the accepted requester
        Notification::create([
            'user_id' => $approval->requester_id,
            'type' => 'request_cancelled',
            'message' => 'The donation post "' . $donation->donation_title . '" request was cancelled by the owner.',
            'read' => false,
        ]);

        // Delete the accepted request
        $approval->delete();

        // Set donation back to "open"
        $donation->status = 'open';
        $donation->save();

        return redirect()->route('UserDonationPosts.show')->with('success', 'Request cancelled. 10 points deducted because it was in-progress.');
    }

    // Case 2: Requester cancels their own request (pending or accepted)
    if ($approval->requester_id === $user->user_id) {
        if ($approval->status === 'accepted') {
            // Deduct 10 points for cancelling in-progress request
            $user->points -= 10;
            $user->save();

            // Reset donation back to open
            $donation->status = 'open';
            $donation->save();

            // Delete approval
            $approval->delete();

            // Notify owner
            Notification::create([
                'user_id' => $owner->user_id, // <-- FIX: it should be $owner->id not $owner->user_id
                'type' => 'request_cancelled',
                'message' => $user->username . ' has cancelled their request for your post "' . $donation->donation_title . '".',
                'read' => false,
            ]);

        return redirect()
        ->route('UserDonationPosts.show')->with('success', 'Request cancelled. 10 points deducted because it was in-progress.');
        } else {
            // Pending request cancellation
            $approval->delete();
        return redirect()
        ->route('UserDonationPosts.show')->with('success', 'Request cancelled successfully.');
        }
    }

    // Unauthorized action
    return back()->with('error', 'Unauthorized action.');
}



}


