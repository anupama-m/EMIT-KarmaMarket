<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Status</th>
            <th>User</th>
            <th>User ID</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($posts as $post)
            <tr>
                <td>{{ $post->post_id ?? $post->id }}</td>
                <td>

                        {{ $post->post_title ?? $post->donation_title }}

                </td>
                <td>{{ ucfirst($post->type) }}</td>
                <td>{{ $post->status }}</td>
                <td>{{ $post->user->username ?? 'N/A' }}</td>
                <td>{{ $post->user->user_id ?? 'N/A' }}</td>
                <td>
                    <form action="{{ route('admin.posts.destroy', $post->post_id ?? $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-muted">No posts found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
