@auth
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - Karma Market</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
   <div class="d-flex admin-layout">
    <!-- Sidebar -->
    <nav class="sidebar">
        <h4 class="fw-bold mb-5 mt-2">Karma Market</h4>
                    <div class="user-icon mx-auto mb-2">
                <i class="fa-solid fa-user fa-2x"></i>
            </div>
        <h4 class="fw-bold">Admin Panel</h4>

        <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <i class="fas fa-tags me-2"></i>Categories
        </a>

        <a href="{{ route('admin.posts') }}" class="{{ request()->routeIs('admin.posts') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list me-2"></i>Posts
        </a>

<a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
    <i class="fas fa-users me-2"></i>Users
</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
           class="logout-link">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper p-4 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('contents')
    </div>
</div>

    @stack('scripts')
</body>
</html>
@endauth
