@extends("layouts.user.user_default")

@section("contents")

<h3>Donation Requests for: #{{ $donation->id }} {{ $donation->donation_title }}</h3>

@php
    // Get accepted request if exists (from sorted approvals)
    $acceptedRequest = $approvals->firstWhere('status', 'accepted');

    // If no accepted, get all pending requests (already sorted by distance)
    $pendingRequests = $acceptedRequest ? collect() : $approvals->where('status', 'pending');
@endphp

@if($acceptedRequest)
    <!-- Show only the accepted request -->
    <div class="card mb-2">
        <div class="card-body d-flex justify-content-between">
            <div>
                <strong>{{ $acceptedRequest->requester->username }}</strong>
                <small>
                    <i class="fa-solid fa-envelope ms-2 me-2 text-muted"></i>{{ $acceptedRequest->requester->email }}
                    <i class="fa-solid fa-phone ms-2 me-2 text-muted"></i>{{ $acceptedRequest->requester->phone }}
                </small>
                <br>
                Status: <span class="badge bg-success">Accepted</span>
            </div>
            <div>
                <form action="{{ route('donation.cancel_request', $acceptedRequest->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Cancelling this accepted request will deduct 10 points. Proceed?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger fw-bold">Cancel Donation</button>
                </form>
            </div>
        </div>
    </div>

@elseif($pendingRequests->isNotEmpty())
    <!-- Show only pending requests -->
    @foreach($pendingRequests as $req)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <strong>{{ $req->requester->username }}</strong>
                    <small>
                        <i class="fa-solid fa-envelope ms-2 me-2 text-muted"></i>{{ $req->requester->email }}
                        <i class="fa-solid fa-phone ms-2 me-2 text-muted"></i>{{ $req->requester->phone }}
                        <i class="fas fa-coins ms-2 me-2 text-muted"></i>{{ $req->requester->points }}
                        <i class="fa-solid fa-location-dot ms-2 me-2 text-muted"></i>{{ $req->requester->location }}
                    </small>
                    <br>
                    Status: <span class="badge bg-secondary">Pending</span>
                </div>
                <div>
                    <form action="{{ route('donation.accept', ['approval_id' => $req->id]) }}" method="POST" style="display:inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Accept</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>

@endsection
