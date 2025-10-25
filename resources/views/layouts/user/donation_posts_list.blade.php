<div class="row">
    @forelse($posts as $post)
@php
    $userRequest = $post->approvals->where('requester_id', Auth::id())->first();
    $acceptedRequest = $post->acceptedRequests()->first();
    $approvalsCount = $post->activeRequests()->count();
@endphp


        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-4">
                        {{-- Post Info --}}
                        <h5 class="card-title mb-1">#{{ $post->id }} {{ $post->donation_title }}</h5>
                        <p class="mb-1">
                            <small class="text-muted">Posted by:
                                <strong>{{ $post->user->username }}</strong>
                            </small>
                        </p>
                        <small class="mb-2 d-block">
                            <i class="fa-solid fa-coins me-1"></i>{{ $post->points }}
                            <i class="fa-regular fa-calendar-days ms-3 me-1"></i>{{ $post->created_at->format('M d, Y') }}
                            <i class="fa-solid fa-location-dot ms-3 me-1"></i>{{ $post->location }}
                        </small>

                        <div class="d-flex gap-2 mt-2">
                            {{-- View Details (Always Visible) --}}
                            <a href="{{ route('donation_post_details', ['post_id' => $post->id]) }}"
                                class="btn btn-sm btn-secondary border fw-semibold">
                                View Details
                            </a>

                            {{-- Completed Status (for both owner & requester) --}}
                            @if($post->status === 'completed')
                                <button class="btn btn-sm btn-success fw-semibold" disabled>
                                    Completed
                                </button>

                                {{-- Owner Actions --}}
                            @elseif($post->user_id === Auth::id())
                                @if($acceptedRequest)
                                    {{-- Accepted Request exists → in-progress --}}
                                    <a href="{{ route('donation.requests', $post->id) }}" class="btn btn-sm bg-warning fw-bold">
                                        In Progress ({{ $approvalsCount }})
                                    </a>
                                    <!-- Owner can cancel while in-progress -->
                                    <form action="{{ route('donation.cancel_request', $acceptedRequest->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Cancelling this accepted request will deduct 10 points. Proceed?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-info fw-bold">Cancel Donation</button>
                                    </form>
                                    <form action="{{ route('donation.delete', $post->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Deleting this in-progress post will deduct 10 points. Proceed?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">Delete</button>
                                    </form>
                                @else
                                    {{-- Normal Edit & Delete --}}
                                    <a href="{{ route('donationpost_edit', ['post_id' => $post->id]) }}"
                                        class="btn btn-sm btn-primary border fw-semibold">Edit</a>
                                    <form action="{{ route('donation.delete', $post->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-danger border fw-semibold">Delete</button>
                                    </form>

                                    {{-- Show Request Status --}}
                                    @php
                                        $pendingCount = $post->pendingRequests()->count();
                                    @endphp

                                    @if($pendingCount === 0)
                                        <button class="btn btn-sm btn-outline-secondary fw-bold" disabled>
                                            <i class="fas fa-hand-holding-heart me-1"></i> Awaiting Requests
                                        </button>
                                    @else
                                        <a href="{{ route('donation.requests', $post->id) }}"
                                            class="btn btn-sm btn-outline-info fw-bold">
                                            See requests ({{ $pendingCount }})
                                        </a>
                                    @endif
                                @endif

                                {{-- Requester Actions (non-owner) --}}
                            @else
                                @if($userRequest)
                                    @if($userRequest->status === 'pending')
                                        <button class="btn btn-sm btn-warning fw-semibold" disabled>Pending</button>
                                        <form action="{{ route('donation.cancel_request', $userRequest->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Cancel this pending request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">Cancel
                                                Request</button>
                                        </form>
                                    @elseif($userRequest->status === 'accepted')
                                        <button class="btn btn-sm btn-success fw-semibold" disabled>Accepted</button>
                                        <form action="{{ route('donation.confirm', $userRequest->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm fw-semibold btn-outline-primary" type="submit">Confirm
                                                Donation</button>
                                        </form>
                                        <form action="{{ route('donation.cancel_request', $userRequest->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Cancelling this accepted request will deduct 10 points. Proceed?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">Cancel
                                                Request</button>
                                        </form>
                                    @elseif($userRequest->status === 'declined')
                                        <button class="btn btn-sm btn-danger fw-semibold" disabled>Declined</button>
                                    @endif
                                @else
                                    {{-- No request yet --}}
                                    <form action="{{ route('request.item', ['post_id' => $post->id]) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary border fw-semibold">
                                            Request for Item
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning">No posts found.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $posts->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
</div>