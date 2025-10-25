@extends("layouts.user.user_default")

@section("contents")

    <!-- Preferred Posts -->
    <section id="preferred_post">
        <h4 class="mb-4"><i class="fas fa-star me-2 text-primary"></i>Preferred Posts</h4>
        <div class="row">

            @forelse($helpPosts as $post)
                    <div class="col-md-12 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $post->post_title }}</h5>
                                <small class="text-muted">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    {{ \Carbon\Carbon::parse($post->post_creation_time)->format('M d, Y') }}
                                </small>
                                <p class="card-text mb-2 mt-2">{{ $post->post_description }}</p>


                                <div class=" d-flex gap-2 mt-2">
                                    <a href="{{ route('post_details', ['post_id' => $post->post_id]) }}"
                                        class="btn btn-sm btn-primary">View Details</a>

                                    <form action="#" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Help</button>
                                    </form>
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
        <!-- Pagination links -->

    </section>
    <div class="mt-4">
        {{ $helpPosts->links('pagination::bootstrap-5') }}
    </div>
@endsection