@extends('admin.admin_default')

@section('contents')
<h3 class="mt-5 mb-3">Manage Categories</h3>

<!-- Add Category -->
<form action="{{ route('admin.categories.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="row g-2">
        <div class="col-md-5">
            <input type="text" name="name" class="form-control" placeholder="Category Name" required>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-control" required>
                <option value="help">Help</option>
                <option value="donation">Donation</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="points" class="form-control" placeholder="Points" required>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Add</button>
        </div>
    </div>
</form>

<div class="row ">
    <!-- Help Categories -->
    <div class="col-md-6 mt-4">
        <h5 class="mb-3">Help Categories</h5>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Name</th><th>Points</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($helpCategories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->points }}</td>
                    <td>
                        <form action="{{ route('admin.categories.destroy',$cat->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted text-center">No help categories</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Donation Categories -->
    <div class="col-md-6 mt-4">
        <h5 class="mb-3">Donation Categories</h5>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Name</th><th>Points</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donationCategories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->points }}</td>
                    <td>
                        <form action="{{ route('admin.categories.destroy',$cat->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted text-center">No donation categories</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
