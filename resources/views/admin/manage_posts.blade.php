@extends('admin.admin_default')

@section('contents')
<h3 class="mb-3">Manage Posts</h3>

<div class="row mb-3">
    <div class="col-md">
        <input type="text" id="searchQuery" class="form-control" placeholder="Search by ID, title, user, type or status...">
    </div>
</div>

<div id="postsTable">
    @include('admin.posts_table', ['posts' => $posts])
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchQuery');
    let timer;

    input.addEventListener('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = encodeURIComponent(input.value);

            fetch(`{{ route('admin.posts.ajax') }}?search=${query}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('postsTable').innerHTML = html;
                })
                .catch(err => console.error(err));
        }, 300);
    });
});
</script>
@endsection
