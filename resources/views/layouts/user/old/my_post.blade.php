@extends("layouts.user.user_default")

@section("contents")

    <!-- All Posts -->
    <section id="all_post">
        <h4 class="mb-4"><i class="fas fa-star me-2 text-primary"></i>Your Posts</h4>

            <div class="row">

                @if (session()->has("success"))
                    <div class="alert alert-success">
                        {{ session()->get("success") }}
                    </div>
                @endif
                @if (session()->has("error"))
                    <div class="alert alert-danger">
                        {{ session()->get("error") }}
                    </div>
                @endif

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
                                            class="btn btn-sm btn-outline-primary fw-semibold">View Details</a>
                                        <a href="{{ route('post_edit', ['post_id' => $post->post_id]) }}"
                                            class="btn btn-sm btn-outline-secondary fw-semibold">Edit</a>
                                        <form action="{{ route('post_delete', ['post_id' => $post->post_id]) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Delete</button>
                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning">No help posts found.</div>
                </div>
            @endforelse

        </div>
    </section>
    <div class="mt-4">
        {{ $helpPosts->links('pagination::bootstrap-5') }}
    </div>
@endsection