@extends("layouts.user.user_default")

@section("contents")

<!-- Search Bar -->
<div class="mb-4">
    <div class="input-group">
        <input 
            type="text" 
            id="search-input" 
            class="form-control" 
            placeholder="Search donation posts.."
        >
    </div>
</div>

<!-- Posts Container -->
<div id="posts-container">
    @include('layouts.user.buy_karma2', ['posts' => $posts])
</div>


@push('scripts')

<script>
$(document).ready(function () {
    console.log("Live search with filters loaded");

    let activeType = '';
    let activeStatus = '';

    function fetchPosts(query = '') {
        $.ajax({
            url: "{{ route('donations.search') }}",
            method: 'GET',
            data: { 
                search: query,
                type: activeType,
                status: activeStatus
            },
            success: function (data) {
                $('#posts-container').html(data);
            },
            error: function(xhr) {
                console.error("AJAX error", xhr.responseText);
            }
        });
    }

    // Debounced live search
    let debounceTimer;
    $('#search-input').on('keyup', function() {
        clearTimeout(debounceTimer);
        let query = $(this).val();
        debounceTimer = setTimeout(() => fetchPosts(query), 300);
    });


    // Pagination click
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href');
        $.get(page, { 
            search: $('#search-input').val(),
            type: activeType,
            status: activeStatus
        }, function(data) {
            $('#posts-container').html(data);
        });
    });
});
</script>
@endpush
@endsection
