<div class="row">
    
    @forelse($helpPosts as $post)
@php
    $user = auth()->user();
    $userRequest = $post->approvals->where('helper_id', $user->user_id)->first();
    $acceptedCount = $post->approvals->where('status', 'accepted')->count();
    $pendingCount = $post->approvals->where('status', 'pending')->count();
@endphp

        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-4">
                        {{-- Post Info --}}
                        <h5 class="card-title mb-1">#{{ $post->post_id }} {{ $post->post_title }}</h5>
                        <p class="mb-1">
                            <small class="text-muted">
                                Posted by: <strong>{{ $post->user->username }}</strong>
                            </small>
                        </p>
                        <small class="mb-2 d-block">
                            <i class="fa-solid fa-coins me-1"></i>{{ $post->points }}
                            <i class="fa-regular fa-calendar-days ms-3 me-1"></i>{{ \Carbon\Carbon::parse($post->post_creation_time)->format('M d, Y') }}
                            <i class="fa-solid fa-location-dot ms-3 me-1"></i>{{ $post->post_location }}
                        </small>

                        <div class="d-flex gap-2 mt-2">
    {{-- View Details --}}
    <a href="{{ route('post_details', ['post_id' => $post->post_id]) }}"
       class="btn btn-sm btn-secondary border fw-semibold">View Details</a>

    {{-- Owner's posts --}}
    @if($post->user_id === $user->user_id)
        @if($post->status === 'open')
            @if($pendingCount > 0)
                <a href="{{ route('post.approvals', $post->post_id) }}"
                   class="btn btn-sm btn-info fw-semibold">
                   See Requests ({{ $pendingCount }})
                </a>
                @else
                <button class="btn btn-sm btn-outline-secondary fw-bold" disabled>
                                            <i class="fas fa-hand-holding-heart me-1"></i> Awaiting Helps
                                        </button>
            @endif

            <a href="{{ route('post_edit', $post->post_id) }}" class="btn btn-sm btn-primary border fw-semibold">Edit</a>
            <form action="{{ route('post_delete', $post->post_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this post?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger border fw-semibold">Delete</button>
            </form>

        @elseif($post->status === 'in-progress')
            <a href="{{ route('post.approvals', $post->post_id) }}" class="btn btn-sm bg-warning fw-bold">
                In Progress ({{ $acceptedCount }})
            </a>

            {{-- Confirm Help Button --}}
            @if($userRequest)
                <form action="{{ route('post.confirm.help', ['approval_id' => $userRequest->approval_id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm fw-semibold btn-outline-primary" type="submit">Confirm Help</button>
                </form>
            @endif

            <form action="{{ route('post.help.cancel', $post->post_id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Cancelling this accepted request will deduct 10 points. Proceed?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-info fw-bold">Cancel Help</button>
            </form>

            <form action="{{ route('post_delete', $post->post_id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Delete this post?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger border fw-semibold">Delete</button>
            </form>
        @elseif($post->status === 'completed')
            <button class="btn btn-sm btn-success fw-semibold" disabled>Completed</button>
        @endif

 {{-- Preference / Other --}}
@elseif(! $userRequest && in_array($type, ['preference', 'other']))
    @if($post->status === 'open')
        <form action="{{ route('post.help', $post->post_id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success fw-bold">
                <i class="fa-solid fa-hand-holding-hand"></i> Help
            </button>
        </form>
    @endif

    {{-- Help offered --}}
    @elseif(isset($type) && $type === 'helped' && $userRequest)
        @if($userRequest->status === 'pending')
            <button class="btn btn-sm btn-warning fw-semibold" disabled>Pending</button>
            <form action="{{ route('post.help.cancel', $post->post_id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure you want to delete this help request?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger fw-semibold">Cancel Help</button>
            </form>
        @elseif($userRequest->status === 'accepted')
            <button class="btn btn-sm btn-success fw-semibold" disabled>Accepted</button>
            <form action="{{ route('post.help.cancel', $post->post_id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Cancelling this accepted request will deduct 10 points. Proceed?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger fw-semibold">Cancel Help</button>
            </form>
        @elseif($userRequest->status === 'completed')
            <button class="btn btn-sm btn-success fw-semibold" disabled>Completed</button>
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
    {{ $helpPosts->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
</div>
