<table class="table table-striped">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
    </thead>
    <tbody>
        @forelse($users as $u)
        <tr>
            <td>{{ $u->user_id }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->email }}</td>
            <td>
                <form action="{{ route('admin.users.destroy',$u->user_id) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No users found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
