@extends("layouts.user.user_default")

@section("contents")

    <!-- All Posts -->
    <section id="all_post">
        <h4 class="mb-4"><i class="fas fa-star me-2 text-primary"></i>All Posts</h4>
        <div class="row">

            @forelse($helpPosts as $post)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start">

                            <!-- Left Side: Post Content -->
                            <div class="flex-grow-1 pe-4">
                                <h5 class="card-title mb-1">{{ $post->post_title }}</h5>
                                <p class="mb-1"><small class="text-muted">Requested by:
                                        <strong>{{ $post->user->username }}</strong></small></p>
                                    <small class="mb-2">
                                        <i class="fa-solid fa-coins me-1"></i>40
                                    <i class="fa-regular fa-calendar-days ms-3 me-1"></i>
                                        {{\Carbon\Carbon::parse($post->post_creation_time)->format('M d, Y') }} 
                                    <i class="fa-solid fa-location-dot ms-3 me-1"></i>{{ $post->post_location }}</small>

                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('post_details', ['post_id' => $post->post_id]) }}"
                                        class="btn btn-sm btn-primary">View Details</a>

                                    <form action="#" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Help</button>
                                    </form>
                                </div>
                            </div>
{{-- Right side: Vertical line and user info --}}
            <div class="vr mx-4" style="height: auto;"></div>

                            <!-- Right Side: User Info -->
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
    </section>

    <!-- Pagination links -->
    <div class="mt-4">
        {{ $helpPosts->links('pagination::bootstrap-5') }}
    </div>
@endsection