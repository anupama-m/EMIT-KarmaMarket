@extends("layouts.user.user_default")

@section("contents")

    <div class="row">
        <div class="d-flex gap-4 mb-4">
            <!-- Tab Buttons -->
            <a href="{{ route('posts.show', ['type' => 'my']) }}"
                class="btn btn-outline-dark {{ request('type') == 'my' ? 'active bg-dark text-white' : '' }}">
                <i class="fa-solid fa-table-list me-2"></i>Your Posts
            </a>

            <a href="{{ route('posts.show', ['type' => 'preference']) }}"
                class="btn btn-outline-dark {{ request('type') == 'preference' ? 'active bg-dark text-white' : '' }}">
                <i class="fas fa-star me-2"></i>Preferred Posts
            </a>

            <div class="dropdown">
                <button
                    class="btn btn-outline-dark dropdown-toggle {{ request('type') == 'helped' ? 'active bg-dark text-white' : '' }}"
                    type="button" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-handshake me-2"></i>Help Offered
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item"
                            href="{{ route('posts.show', ['type' => 'helped', 'status' => 'completed']) }}">Completed</a>
                    </li>
                    <li><a class="dropdown-item"
                            href="{{ route('posts.show', ['type' => 'helped', 'status' => 'accepted']) }}">Accepted</a></li>
                    <li><a class="dropdown-item"
                            href="{{ route('posts.show', ['type' => 'helped', 'status' => 'pending']) }}">Pending</a></li>
                    <li><a class="dropdown-item"
                            href="{{ route('posts.show', ['type' => 'helped', 'status' => 'rejected']) }}">Rejected</a></li>
                </ul>
            </div>

            <a href="{{ route('posts.show', ['type' => 'all']) }}"
                class="btn btn-outline-dark {{ request('type') == 'all' ? 'active bg-dark text-white' : '' }}">
                <i class="fas fa-list me-2"></i>All Posts
            </a>
        </div>
    </div>


    <div class="row">
        @forelse($helpPosts as $post)
            @php
                $myApproval = $post->approvals->firstWhere('helper_id', Auth::id());
                $acceptedHelper = $post->approvals
                    ->filter(fn($a) => in_array($a->status, ['accepted', 'completed']))
                    ->first();
                $isHelperSelected = !is_null($acceptedHelper); //true only if someone is accepted
                $isConfirmed = $acceptedHelper?->is_confirmed ?? false;
                $cancelRequested = $myApproval?->status === 'cancel_requested';
                $totalOffers = $post->approvals->count();
            @endphp


            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <!-- Post Details -->
                        <div class="flex-grow-1 pe-4">
                            <h5 class="card-title mb-1">#{{$post->post_id}} {{$post->post_title }}</h5>
                            <p class="mb-1"><small class="text-muted">Requested by:
                                    <strong>{{ $post->user->username }}</strong></small></p>
                            <small class="mb-2 d-block">
                                <i class="fa-solid fa-coins me-1"></i>{{ $post->points }}
                                <i class="fa-regular fa-calendar-days ms-3 me-1"></i>{{ \Carbon\Carbon::parse($post->post_creation_time)->format('M d, Y') }}
                                <i class="fa-solid fa-location-dot ms-3 me-1"></i>{{ $post->post_location }}
                            </small>
 <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('post_details', ['post_id' => $post->post_id]) }}"
                                    class="btn btn-sm btn-secondary border fw-semibold">View Details</a>
                                {{-- Action Buttons --}}
                                @if($type === 'preference' || $type === 'all')
                                 @if($isConfirmed)
                                            <span class="btn btn-sm btn-success fw-semibold disabled">Completed</span>
                                            @endif

                                    @if(is_null($myApproval) && !$acceptedHelper)
                                        <form action="{{ route('post.help', ['post_id' => $post->post_id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Help</button>
                                        </form>
                                    @elseif($myApproval?->status === 'pending')
                                        <form action="{{ route('post.help.cancel', ['post_id' => $post->post_id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-info">Cancel Help</button>
                                        </form>
                                    @elseif($myApproval?->status === 'accepted')
                                        @if($cancelRequested)
                                            <span class="badge bg-warning">Cancel request sent</span>
                                        @else
                                            <form action="#"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Request Cancel</button>
                                            </form>
                                        @endif
                                       @elseif($myApproval?->status === 'rejected')
                                        <span class="btn btn-sm btn-danger fw-semibold disabled">Rejected</span>
                                      
                              
                                    @endif

                                @elseif($type === 'my')

                                    @if($isHelperSelected)
                                        @if($isConfirmed)
                                            <span class="btn btn-sm btn-success fw-semibold disabled">Completed</span>
                                        @else
                                            <a href="{{ route('post.approvals', ['post_id' => $post->post_id]) }}"
                                                class="btn btn-sm btn-outline-success fw-semibold">
                                                View Accepted Helper
                                            </a>
                                        @endif
                                    @elseif($totalOffers > 0)
                                        <a href="{{ route('post_edit', ['post_id' => $post->post_id]) }}"
                                            class="btn btn-sm btn-outline-secondary fw-semibold">Edit</a>

                                        <form action="{{ route('post_delete', ['post_id' => $post->post_id]) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Delete</button>
                                        </form>
                                        <a href="{{ route('post.approvals', ['post_id' => $post->post_id]) }}"
                                            class="btn btn-sm btn-outline-dark fw-semibold">
                                            See Requests ({{ $totalOffers }})
                                        </a>
                                    @else
                                        <a href="{{ route('post_edit', ['post_id' => $post->post_id]) }}"
                                            class="btn btn-sm btn-outline-secondary fw-semibold">Edit</a>

                                        <form action="{{ route('post_delete', ['post_id' => $post->post_id]) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Delete</button>
                                        </form>
                                       <span class="badge bg-light text-muted rounded-pill px-3 py-2">
    <i class="fas fa-hand-holding-heart me-1"></i> Awaiting help offers
</span>
                                    @endif

                                @endif
                            </div>
                        </div>

                        <!-- Vertical Divider -->
                        <div class="vr mx-4" style="height: auto;"></div>

                        <!-- Requester Info -->
                        <div style="min-width: 180px;">
                            <p class="mb-3"><strong>Requester Info</strong></p>
                            <p class="mb-1"><i class="fa-solid fa-user me-1"></i>{{ $post->user->username }}</p>
                            <p class="mb-1"><i class="fa-solid fa-envelope me-1"></i>{{ $post->user->email }}</p>
                            <p class="mb-0"><i class="fa-solid fa-phone me-1"></i>{{ $post->user->phone }}</p>
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
        {{ $helpPosts->links('pagination::bootstrap-5') }}
    </div>

@endsection