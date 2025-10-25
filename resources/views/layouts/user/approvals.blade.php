@extends("layouts.user.user_default")

@section("contents")

<h3>Help Requests for: {{ $post->post_title }}</h3>

@php
    $acceptedRequest = $requests->firstWhere('status', 'accepted');
@endphp

@if($acceptedRequest)
    <!-- Show only the accepted request -->
    <div class="card mb-2">
        <div class="card-body d-flex justify-content-between">
            <div>
                <strong>{{ $acceptedRequest->helper->username }}</strong>
                <small>
                    <i class="fa-solid fa-envelope ms-2 me-2 text-muted"></i>{{ $acceptedRequest->helper->email }}
                    <i class="fa-solid fa-phone ms-2 me-2 text-muted"></i>{{ $acceptedRequest->helper->phone }}
                </small>
                <br>
                Status: <span class="badge bg-success">Accepted</span>
            </div>
            <div>
                @if(!$acceptedRequest->is_confirmed)

                        <form action="{{ route('post.confirm.help', ['approval_id' => $acceptedRequest->approval_id]) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-primary">Help Completed</button>
                    </form>
                @else
                    <span class="badge bg-info">Help Confirmed</span>
                @endif
            </div>
        </div>
    </div>
@else
    <!-- Show all pending requests if no one accepted yet -->
    @foreach($requests as $req)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <strong>{{ $req->helper->username }}</strong>
                    <small>
                        <i class="fa-solid fa-envelope ms-2 me-2 text-muted"></i>{{ $req->helper->email }}
                        <i class="fa-solid fa-phone ms-2 me-2 text-muted"></i>{{ $req->helper->phone }}
                        <i class="fas fa-coins ms-2 me-2 text-muted"></i>{{ $req->helper->points }}
                        <i class="fa-solid fa-location-dot ms-2 me-2 text-muted"></i>{{ $req->helper->location }}
                    </small>
                    <br>
                    Status: <span class="badge bg-{{ $req->status == 'pending' ? 'secondary' : 'danger' }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </div>
                <div>
                    @if($req->status === 'pending')
                        <form action="{{ route('post.approvals.accept', ['approval_id' => $req->approval_id]) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Accept</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif

<a href="{{ session('from_confirmation') ? route('posts.show', ['type' => 'my']) : url()->previous() }}"
   class="btn btn-secondary mt-3">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>
@endsection
