<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationApproval;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationApprovalController_old extends Controller
{
    /**
     * Request a donation (send request)
     */
    public function store($donation_id)
    {
        $userId = Auth::id();
        $donation = Donation::findOrFail($donation_id);

        // Cannot request own donation
        if ($donation->user_id == $userId) {
            return redirect()->back()->with('error', 'You cannot request your own donation.');
        }

        // Check if user already requested
        $exists = DonationApproval::where('donation_id', $donation_id)
            ->where('requester_id', $userId)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'You already requested this donation.');
        }

        DonationApproval::create([
            'donation_id' => $donation_id,
            'requester_id' => $userId,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Your request has been sent!');
    }

    /**
     * View all requests for a donation (only donor can view)
     */
    public function viewApprovals($donation_id)
    {
        $donation = Donation::with('user')->findOrFail($donation_id);

        if (Auth::id() != $donation->user_id) {
            abort(403, 'Unauthorized');
        }

        $requests = DonationApproval::where('donation_id', $donation_id)
            ->with('requester')
            ->get();

        return view('layouts.user.donation_approvals', compact('donation', 'requests'));
    }

    /**
     * Accept a request (only donor can accept)
     */
    public function accept($approval_id)
    {
        $approval = DonationApproval::findOrFail($approval_id);

        if (Auth::id() != $approval->donation->user_id) {
            abort(403, 'Unauthorized');
        }

        // Accept this request
        $approval->status = 'accepted';
        $approval->save();

        // Reject all other requests
        DonationApproval::where('donation_id', $approval->donation_id)
            ->where('id', '!=', $approval_id)
            ->update(['status' => 'rejected']);

        // Optional: Notify requester (if using notifications)
        Notification::create([
            'user_id' => $approval->requester_id,
            'type' => 'donation_accepted',
            'message' => 'Your request for "' . $approval->donation->donation_title . '" was accepted!',
            'read' => false,
        ]);

        return redirect()->back()->with('success', 'Request accepted.');
    }

    /**
     * Confirm donation completion and give points
     */
    public function confirm($approval_id)
    {
        $approval = DonationApproval::findOrFail($approval_id);

        if (Auth::id() != $approval->donation->user_id) {
            abort(403, 'Unauthorized');
        }

        if ($approval->status !== 'accepted') {
            return back()->with('error', 'Only accepted requests can be confirmed.');
        }

        // Mark as completed
        $approval->status = 'completed';
        $approval->is_confirmed = true;
        $approval->save();

        // Reward points to requester
        $requester = $approval->requester;
        $requester->points += $approval->donation->points;
        $requester->save();

        // Optional: Notify requester
        Notification::create([
            'user_id' => $requester->id,
            'type' => 'donation_completed',
            'message' => 'Your request for "' . $approval->donation->donation_title . '" has been completed! You earned ' . $approval->donation->points . ' Karma.',
            'read' => false,
        ]);

        return redirect()->back()->with('success', 'Donation completed and points awarded.');
    }

    /**
     * Cancel request (by requester)
     */
    public function cancel($donation_id)
    {
        $userId = Auth::id();

        $approval = DonationApproval::where('donation_id', $donation_id)
            ->where('requester_id', $userId)
            ->first();

        if (!$approval) {
            return redirect()->back()->with('info', 'No request found.');
        }

        if ($approval->status === 'pending') {
            $approval->delete();
            return redirect()->back()->with('success', 'Your request has been canceled.');
        }

        if ($approval->status === 'accepted') {
            // Mark as cancel requested
            $approval->status = 'cancel_requested';
            $approval->save();

            // Optional: Notify donor
            Notification::create([
                'user_id' => $approval->donation->user_id,
                'type' => 'donation_cancel_requested',
                'message' => 'The accepted requester has asked to cancel the donation "' . $approval->donation->donation_title . '".',
                'read' => false,
            ]);

            return redirect()->back()->with('success', 'Cancellation request sent to the donor.');
        }

        return redirect()->back()->with('info', 'Unable to cancel at this stage.');
    }

    /**
     * Confirm cancel request (by donor)
     */
    public function confirmCancelRequest($approval_id)
    {
        $approval = DonationApproval::findOrFail($approval_id);

        if (Auth::id() != $approval->donation->user_id) {
            abort(403, 'Unauthorized');
        }

        if ($approval->status !== 'cancel_requested') {
            return back()->with('error', 'No cancellation request to process.');
        }

        // Remove approval and penalize requester if needed
        $approval->delete();

        // Optional: Deduct points from requester
        $requester = $approval->requester;
        $requester->points -= 5;
        $requester->save();

        // Notify requester
        Notification::create([
            'user_id' => $requester->id,
            'type' => 'donation_cancel_confirmed',
            'message' => 'Your cancellation request for "' . $approval->donation->donation_title . '" has been approved. (-5 points)',
            'read' => false,
        ]);

        return back()->with('success', 'The cancellation request has been confirmed.');
    }
}
