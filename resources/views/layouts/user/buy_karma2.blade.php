<div class="row g-4">
    @forelse($posts as $post)
    @if($post->status==='open')
        @php
            $images = is_array($post->donation_images)
                ? $post->donation_images
                : json_decode($post->donation_images, true);
            $images = $images ?? [];

            // Check if current user has requested this donation
            $userRequest = $post->approvals->firstWhere('requester_id', Auth::id());

            // Skip this post if user was rejected or request is in-progress
            $skip = $userRequest && in_array($userRequest->status, ['rejected', 'in-progress']);
        @endphp

        @if(!$skip)
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0">

                    <!-- Product Image -->
                    @if (!empty($images) && is_array($images))
                        <img src="{{ asset($images[0]) }}" class="card-img-top" alt="donation_img"
                            style="height: 180px; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/dummy.jpg') }}" class="card-img-top" alt="donation_img"
                            style="height: 180px; object-fit: cover;">
                    @endif

                    <!-- Card Body -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate" title="{{ $post->donation_title }}">
                            #{{ $post->id }} {{ $post->donation_title }}
                        </h5>

                        <p class="mb-1">
                            <small class="text-muted">
                                Requested by: <strong>{{ $post->user->username }}</strong>
                            </small>
                        </p>

                        <small class="mb-2 d-block fst-italic">
                            <i class="fa-solid fa-coins me-1"></i>{{ $post->points }}
                            <i class="fa-regular fa-calendar-days ms-2 me-1"></i>
                            {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
                            <i class="fa-solid fa-location-dot ms-2 me-1"></i>{{ $post->location }}
                        </small>

                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('donation_post_details', ['post_id' => $post->id]) }}"
                                class="btn btn-sm btn-outline-primary fw-semibold">View Details</a>

                            @if ($userRequest && $userRequest->status != 'open')
                                <button class="btn btn-sm btn-secondary fw-semibold" disabled>
                                    Request Pending
                                </button>
                            @else
                                <form action="{{ route('request.item', ['post_id' => $post->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success fw-semibold">
                                        Request the Item
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
         @endif
    @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">No posts found.</div>
        </div>
       
    @endforelse
</div>

<div class="mt-4">
    {{ $posts->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
</div>
