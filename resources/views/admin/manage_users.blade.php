@extends('admin.admin_default')

@section('contents')
<h3 class="mb-3">Manage Users</h3>

<!-- Search Box -->
<div class="mb-3">
    <input type="text" id="user-search" class="form-control" placeholder="Search by ID, Name, or Email">
</div>

<!-- Table container -->
<div id="users-table">
    @include('admin.users_table', ['users' => $users])
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("user-search");
    const tableDiv = document.getElementById("users-table");
    let timer;

    input.addEventListener("keyup", () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = encodeURIComponent(input.value);

            fetch(`{{ route('admin.users.ajax') }}?search=${query}`)
                .then(res => res.text())
                .then(html => {
                    tableDiv.innerHTML = html;
                })
                .catch(err => console.error(err));
        }, 300); // debounce
    });
});
</script>
@endsection
