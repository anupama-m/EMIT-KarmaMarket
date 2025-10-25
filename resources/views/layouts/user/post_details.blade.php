@extends("layouts.user.user_default")

@section("contents")

    <section class="container mt-3">

        <div class="card shadow border-0 rounded-4 overflow-hidden" style="background: #f9fafb;">

            <!-- Card Header -->
            <div class="px-4 py-3 border-bottom" style="background: #f0f9ff;">
                <h3 class="mb-1 fw-bold">#{{$helpPosts->post_id}} {{ $helpPosts->post_title }}</h3>
                <div class="d-flex flex-wrap text-muted small align-items-center gap-3">
                    <div><i class="fa-solid fa-coins me-1"></i>{{ $helpPosts->points }}</div>
                    <div>
                        <i class="fa-regular fa-calendar-days me-1"></i>
                        {{ \Carbon\Carbon::parse($helpPosts->post_creation_time)->format('M d, Y') }}
                    </div>
                    <div>
                        <i class="fa-solid fa-location-dot me-1"></i>
                        {{ $helpPosts->post_location }}
                    </div>
                    <div>
                        <i class="fa-solid fa-tag me-1"></i>
                        {{ ucfirst($helpPosts->post_category) }}
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body d-flex flex-column flex-lg-row gap-4">

                <!-- Left Column: Description -->
                <div class="flex-grow-1">
                    <h5 class="mb-3 fw-semibold">Description</h5>
                    <p class="lh-lg" style="color: #374151;">{{ $helpPosts->post_description }}</p>

                    @if($helpPosts->post_category === 'blood')
                        <div class="mt-4 p-3 rounded shadow-sm border" style="background: #fce4e4;">
                            <p class="mb-2"><strong>Blood Group:</strong> {{ $helpPosts->blood_group ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Hospital Name:</strong> {{ $helpPosts->hospital_name ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Requester & Helper Info -->
                <div class="d-flex flex-column gap-4" style="min-width: 280px;">

                    {{-- Requester info hidden if own post --}}
                    @if(Auth::id() !== $helpPosts->user_id)
                        <div class="p-3 rounded shadow-sm border" style="background: #d6e9dfff;">
                            <h6 class="fw-semibold mb-3"><i class="fa-solid fa-circle-user me-2"></i>Requester Info</h6>
                            <p class="mb-2"><i class="fa-solid fa-user me-2"></i> {{ $helpPosts->user->username }}</p>
                            <p class="mb-2"><i class="fa-solid fa-envelope me-2"></i> {{ $helpPosts->user->email }}</p>
                            <p class="mb-0"><i class="fa-solid fa-phone me-2"></i> {{ $helpPosts->user->phone }}</p>
                        </div>
                    @endif

                    @php
                        $acceptedRequest = $helpPosts->approvals->firstWhere('status', 'accepted');
                        $completedRequest = $helpPosts->approvals->firstWhere('status', 'completed');
                        $userId = Auth::id();
                    @endphp

                    {{-- Show only accepted or completed helper info --}}
                    @if(($acceptedRequest || $completedRequest) && ($userId == $helpPosts->user_id || ($completedRequest && $userId == $completedRequest->helper->user_id) || ($acceptedRequest && $userId == $acceptedRequest->helper->user_id)))
                        @php
                            $helperRequest = $completedRequest ?? $acceptedRequest;
                        @endphp
                        @if($helperRequest && $helperRequest->helper)
                            <div class="p-3 rounded shadow-sm border" style="background: #ede9fe;">
                                <h6 class="fw-semibold mb-3"><i class="fa-solid fa-handshake me-2"></i>Helper Info</h6>
                                <p class="mb-2"><i class="fa-solid fa-user me-2"></i> {{ $helperRequest->helper->username }}</p>
                                <p class="mb-2"><i class="fa-solid fa-envelope me-2"></i> {{ $helperRequest->helper->email }}</p>
                                <p class="mb-0"><i class="fa-solid fa-phone me-2"></i> {{ $helperRequest->helper->phone }}</p>
                            </div>
                        @endif
                    @endif

                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2"
                style="background: #f0f9ff;">

                <a href="{{ url()->previous() }}" class="btn btn-secondary d-flex align-items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>

                @php
                    $acceptedRequest = $helpPosts->approvals->firstWhere('status', 'accepted');
                @endphp

                @if($helpPosts->status === 'completed')
                    {{-- Completed --}}
                    <button class="btn btn-success fw-semibold" disabled>
                        <i class="fa-solid fa-check"></i> Completed
                    </button>

                @elseif(Auth::id() === $helpPosts->user_id)
                    {{-- Owner --}}
                    @if($helpPosts->status === 'open')
                        {{-- Open: edit/delete --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('post_edit', ['post_id' => $helpPosts->post_id]) }}"
                                class="btn btn-sm btn-primary fw-semibold">Edit</a>

                            <form action="{{ route('post_delete', ['post_id' => $helpPosts->post_id]) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this donation post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger fw-semibold">Delete</button>
                            </form>
                        </div>

                    @endif
                @endif
            </div>

        </div>


      {{-- Related Section --}}
@if($isOwner)
    {{-- OWNER POSTS: show category-specific recommendations --}}
    @if($helpPosts->post_category === 'blood' && $similarBloodUsers->isNotEmpty())
        <section class="mt-5">
            <h5 class="mb-3 fw-semibold">Nearby {{ $helpPosts->blood_group ?? 'N/A' }} Blood Donors</h5>
            <div class="row g-3">
                @foreach($similarBloodUsers as $user)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3" style="background: #f0fff4;">
                            <h6 class="mb-1 fw-bold">{{ $user->username }}</h6>
                            <small class="d-block text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $user->location }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    @elseif($helpPosts->post_category === 'book' && ($similarBookUsers_student->isNotEmpty() || $similarBookUsers_other->isNotEmpty()) )
    @if($similarBookUsers_student->isNotEmpty())
        <section class="mt-5">
            <h5 class="mb-3 fw-semibold">People from your Institution</h5>
            <div class="row g-3">
                @foreach($similarBookUsers_student as $user)
                        <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3" style="background: #f0fff4;">
                            <h6 class="mb-1 fw-bold">{{ $user->username }} #{{ $user->year }}y</h6>
                            <small class="d-block text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $user->location }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <h5 class="mt-5 mb-3 fw-semibold">Recommended Donation Posts</h5>
        <div class="row g-3 ">
            @foreach($similarBookUsers_other as $donation_post)
                @php $user = $donation_post->user; @endphp
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3" style="background: #f0fff4;">
                        <h6 class="mb-1 fw-bold">#{{ $donation_post->id }} {{ $donation_post->donation_title }}</h6>
                        <small class="d-block text-muted mt-1">
                            <i class="fa-solid fa-coins me-1"></i>{{ $donation_post->points }}
                            <i class="fa-regular fa-calendar-days ms-2 me-1"></i>{{ $donation_post->created_at->format('M d, Y') }}
                            <i class="fa-solid fa-location-dot ms-2 me-1"></i>{{ $donation_post->location }}
                            
                        </small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-user me-1"></i>{{ $user->username }}</small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
                        <small class="d-block text-muted mt-1">
                            <a href="{{ route('donation_post_details', ['post_id' => $donation_post->id]) }}" class="btn btn-sm btn-success fw-semibold">View Details</a>
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @elseif($helpPosts->post_category === 'volunteer' && $similarVolunteerUsers->isNotEmpty())
        <section class="mt-5">
            <h5 class="mb-3 fw-semibold">Volunteer Leaderboard</h5>
            <div class="row g-3">
                @foreach($similarVolunteerUsers as $user)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3" style="background: #f0fff4;">
                            <h6 class="mb-1 fw-bold">{{ $user->username }}</h6>
                            <small class="d-block text-muted mt-1">
                                <i class="fa-solid fa-hand-holding-heart me-1"></i> Volunteered {{ $user->volunteer_count }} times
                            </small>
                            <small class="d-block text-muted"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
                            <small class="d-block text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $user->location }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    @elseif(($helpPosts->post_category === 'medical' || $helpPosts->post_category === 'clothes') && $recommendations_medical_cloth->isNotEmpty())
        <h5 class="mt-5 mb-3 fw-semibold">Recommended Donation Posts</h5>
        <div class="row g-3">
            @foreach($recommendations_medical_cloth as $donation_post)
                @php $user = $donation_post->user; @endphp
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3" style="background: #f0fff4;">
                        <h6 class="mb-1 fw-bold">#{{ $donation_post->id }} {{ $donation_post->donation_title }}</h6>
                        <small class="d-block text-muted mt-1">
                            <i class="fa-solid fa-coins me-1"></i>{{ $donation_post->points }}
                            <i class="fa-solid fa-location-dot ms-3 me-1"></i>{{ $donation_post->location }}
                        </small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-user me-1"></i>{{ $user->username }}</small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</small>
                        <small class="d-block text-muted mt-1"><i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}</small>
                        <small class="d-block text-muted mt-1">
                            <a href="{{ route('donation_post_details', ['post_id' => $donation_post->id]) }}" class="btn btn-sm btn-success fw-semibold">View Details</a>
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@else
    {{-- NOT OWNER: show similar help posts by category --}}
    @if($similarHelpPosts->isNotEmpty())
        <section class="mt-5">
            <h5 class="fw-bold mb-4">You May Also Like</h5>
            <div class="row g-4">
                @foreach($similarHelpPosts as $related)
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: #f0fff4;">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">
                                    #{{ $related->post_id }} {{ Str::limit($related->post_title, 40) }}
                                </h6>
                                <small class="text-muted d-block mb-2">
                                    <i class="fa-solid fa-coins me-1"></i>{{ $related->points }}
                                    <i class="fa-regular fa-calendar-days ms-2 me-1"></i>{{ \Carbon\Carbon::parse($related->post_creation_time)->format('M d, Y') }}
                                    <i class="fa-solid fa-location-dot ms-2 me-1"></i>{{ $related->post_location }}
                                </small>
                                <a href="{{ route('post_details', parameters: ['post_id' => $related->post_id]) }}" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endif

    </section>

@endsection