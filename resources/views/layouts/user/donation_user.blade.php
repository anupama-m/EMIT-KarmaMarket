@extends("layouts.user.user_default")

@section("contents")
<div class="row">
    <div class="d-flex gap-4 mb-4 flex-wrap">

        <!-- Your Donation Posts Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-table-list me-2"></i>Your Donation Posts
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-btn" data-type="my" data-status="all">All</a></li>
                <li><a class="dropdown-item filter-btn" data-type="my" data-status="open">Open</a></li>
                <li><a class="dropdown-item filter-btn" data-type="my" data-status="in-progress">In Progress</a></li>
                <li><a class="dropdown-item filter-btn" data-type="my" data-status="completed">Completed</a></li>
            </ul>
        </div>

        <!-- Your Requests to Others Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-list me-2"></i>Your Requests
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-btn" data-type="helped" data-status="completed">Completed</a></li>
                <li><a class="dropdown-item filter-btn" data-type="helped" data-status="accepted">Accepted</a></li>
                <li><a class="dropdown-item filter-btn" data-type="helped" data-status="pending">Pending</a></li>
            </ul>
        </div>

    </div>
</div>

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
    @include('layouts.user.donation_posts_list', ['posts' => $posts])
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const $container = $('#posts-container');
    let activeType = 'my';
    let activeStatus = 'all';

    // Function to load posts via AJAX
    function loadPosts(search = '') {
        $.ajax({
            url: "{{ route('donation_posts.ajax') }}",
            type: "GET",
            data: { type: activeType, status: activeStatus, search: search },
            success: function(html) {
                $container.html(html);
            },
            error: function(xhr) {
                console.error("AJAX error:", xhr.responseText);
            }
        });
    }

    // Default load on page entry
    loadPosts();

    //  Filter dropdown clicks
    $(document).on('click', '.filter-btn', function(e) {
        e.preventDefault();
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        activeType = $(this).data('type') || 'my';
        activeStatus = $(this).data('status') || 'all';
        const search = $('#search-input').val() || '';

        loadPosts(search);
    });

    // Live search
    let debounceTimer;
    $('#search-input').on('keyup', function() {
        clearTimeout(debounceTimer);
        const search = $(this).val();
        debounceTimer = setTimeout(() => {
            loadPosts(search);
        }, 300);
    });

    // Pagination links
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const page = $(this).attr('href');
        $.get(page, { type: activeType, status: activeStatus, search: $('#search-input').val() }, function(html) {
            $container.html(html);
        });
    });
});
</script>
@endpush
@endsection
