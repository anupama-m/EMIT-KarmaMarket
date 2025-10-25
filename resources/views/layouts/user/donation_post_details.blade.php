@extends("layouts.user.user_default")

@section("contents")
<section class="container my-3">

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-light px-4 py-3 border-bottom" >
            <h4 class="mb-2 fw-bold">#{{ $post->id }} {{ $post->donation_title }}</h4>
            <small class="d-block opacity-75">
                <i class="fa-solid fa-coins me-1"></i>{{ $post->points }} Points

                <span class="ms-3">
                    <i class="fa-regular fa-calendar-days me-1"></i>
                    {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
                </span>

                <span class="ms-3">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    {{ $post->location }}
                </span>
            </small>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <div class="row g-4 align-items-start">
                <!-- Left: Image -->
                <div class="col-md-4 text-center">
                    @php
                        $images = json_decode($post->donation_images, true) ?? [];
                        $imageSrc = !empty($images) && is_array($images) 
                                    ? asset($images[0]) 
                                    : asset('img/dummy.jpg');
                    @endphp
                    <img src="{{ $imageSrc }}" class="img-fluid rounded shadow-sm border" 
                         style="max-height: 280px; object-fit: contain;" />
                </div>

                <!-- Middle: Description -->
                <div class="col-md-5">
                    <h6 class="fw-bold text-secondary mb-2">
                        <i class="fa-solid fa-info-circle me-1"></i> Description
                    </h6>
                    <p class="text-dark lh-lg">{{ $post->donation_description }}</p>
                </div>

<!-- Right Column -->
<div class="col-md-3">
    {{-- Donor Info (only show if not the owner) --}}
    @if($post->user_id !== Auth::id())
        <h6 class="fw-bold text-secondary mb-3">
            <i class="fa-solid fa-circle-user me-2"></i> Donor Info
        </h6>
        <div class="p-3 bg-light rounded border shadow-sm mb-4">
            <p class="mb-2">
                <i class="fa-solid fa-user me-2 text-muted"></i>{{ $post->user->username }}
            </p>
            <p class="mb-2">
                <i class="fa-solid fa-envelope me-2 text-muted"></i>{{ $post->user->email }}
            </p>
            <p class="mb-0">
                <i class="fa-solid fa-phone me-2 text-muted"></i>{{ $post->user->phone }}
            </p>
        </div>
    @endif

    {{-- Requester Info (only if accepted or completed) --}}

    @if(in_array($post->status, ['completed', 'in-progress']))
        <h6 class="fw-bold text-secondary mb-3">
            <i class="fa-solid fa-circle-user me-2"></i> Requester Info
        </h6>
        <div class="p-3 bg-light rounded border shadow-sm">
            <p class="mb-2">
                <i class="fa-solid fa-user me-2 text-muted"></i>{{ $acceptedApproval->requester->username }}
            </p>
            <p class="mb-2">
                <i class="fa-solid fa-envelope me-2 text-muted"></i>{{ $acceptedApproval->requester->email }}
            </p>
            <p class="mb-0">
                <i class="fa-solid fa-phone me-2 text-muted"></i>{{ $acceptedApproval->requester->phone }}
            </p>
        </div>
    @endif
</div>

            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
 {{-- Owner Actions --}}
                            @if($post->user_id === Auth::id())
                               @if(in_array($post->status, ['open', 'in-progress']))
{{-- Open & In-Progress: Show Edit + Delete --}}
<div class="d-inline-flex gap-2">
    <a href="{{ route('donationpost_edit', ['post_id' => $post->id]) }}" 
       class="btn btn-sm btn-primary border fw-semibold py-2 px-2"><i class="fa-solid fa-pen-to-square"></i> Edit</a> 

    <form action="{{ route('donation.delete', $post->id) }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to delete this donation post?');">
        @csrf 
        @method('DELETE') 
        <button type="submit" class="btn btn-sm btn-danger border fw-semibold py-2 px-2"><i class="fa-regular fa-trash-can"></i> Delete</button> 
    </form>
</div>

    @if($post->status === 'in-progress')
        {{-- Only for In-Progress: See Requests --}}
        <a href="{{ route('donation.requests', ['post_id' => $post->id]) }}"
           class="btn btn-sm btn-outline-dark fw-semibold">
            See Requests ({{ $post->approvals->count() }})
        </a>
    @endif

@elseif($post->status === 'completed')
    {{-- Completed: Show Completed Button --}}
    <button class="btn btn-sm btn-success fw-semibold" disabled>
        {{ ucfirst($post->status) }}
    </button>
@endif

                            @else
                                {{-- Not Owner: Request / Confirm --}}
                                @php
                                    $userRequest = $post->approvals->where('requester_id', Auth::id())->first();
                                @endphp

                                @if($userRequest)
                                    @if($userRequest->status === 'accepted')
                                        <button class="btn btn-sm btn-secondary fw-semibold py-2 px-2" disabled>
                                            {{ ucfirst($userRequest->status) }}
                                        </button>
                                        <form action="{{ route('donation.confirm', ['approval_id' => $userRequest->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm fw-semibold btn-outline-primary" type="submit">Confirm Donation</button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-success fw-semibold" disabled>
                                            {{ ucfirst($userRequest->status) }}
                                        </button>
                                    @endif
                                @else
                                    <form action="{{ route('request.item', ['post_id' => $post->id]) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary border fw-semibold py-2 px-2"><i class="fa-solid fa-cart-shopping"></i>
                                            Request for Item
                                        </button>
                                    </form>
                                @endif
                            @endif
        </div>
    </div>

  <!-- Related Posts -->
   <div class="mt-5" >
@if($relatedPosts->count())

    {{-- Help Posts --}}
    @php
        $helpPosts = $relatedPosts->filter(fn($p) => $p instanceof \App\Models\HelpPost);
    @endphp
    @if($helpPosts->count())
        <h5 class="fw-bold mb-4">Help Posts You Might Support</h5>
        <div class="row g-4 mb-5">
            @foreach($helpPosts as $related)
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100" style="background: #f0fff4;">
                        <div class="card-body">             
                            <h6 class="fw-bold mb-2">#{{ $related->post_id }} {{ Str::limit($related->post_title, 40) }}</h6>
                            <small class="text-muted d-block mb-2">
                                <i class="fa-solid fa-user"></i> {{ $related->user->username }} <br>
                                <i class="fa-solid fa-envelope me-1"></i> {{ $related->user->email }} <br>
                                <i class="fa-solid fa-phone me-1"></i> {{ $related->user->phone }} <br>
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $related->post_location }}
                            </small>
                            <a href="{{ route('post_details', ['post_id' => $related->post_id]) }}" 
                               class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Donation Posts --}}
    @php
        $donationPosts = $relatedPosts->filter(fn($p) => !$p instanceof \App\Models\HelpPost);
    @endphp
    @if($donationPosts->count())
        <h5 class="fw-bold mb-4">You May Also Like</h5>
        <div class="row g-4">
            @foreach($donationPosts as $related)
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100" style="background: #f0fff4;">
                        @php 
                            $relatedImages = json_decode($related->donation_images, true) ?? []; 
                            $relatedImageSrc = !empty($relatedImages) && is_array($relatedImages) ? asset($relatedImages[0]) : asset('img/dummy.jpg'); 
                        @endphp 
                        <img src="{{ $relatedImageSrc }}" class="card-img-top rounded-top-4" style="height: 180px; object-fit: cover;"> 
                        <div class="card-body"> 
                            <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">
                                #{{ $related->id }} {{ Str::limit($related->donation_title, 40) }}
                            </h6> 
                            <small class="text-muted d-block mb-2">
                                <i class="fa-solid fa-coins me-1"></i>{{ $related->points }}
                                <i class="fa-regular fa-calendar-days ms-2 me-1"></i>{{ \Carbon\Carbon::parse($related->created_at)->format('M d, Y') }}
                                <i class="fa-solid fa-location-dot ms-2 me-1"></i>{{ $related->location }}
                            </small>
                            <a href="{{ route('donation_post_details', ['post_id' => $related->id]) }}" 
                               class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                      </div>
                    </div>
            
            @endforeach
        </div>
    @endif

@endif
 </div>



</section>
@endsection
