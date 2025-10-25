@extends("layouts.user.user_default")

@section("contents")

@php
    $allCategories = ['book', 'medical', 'blood', 'clothes','volunteer'];
    $selectedCategory = old('donation_category', $helpPosts->donation_category); // Prioritize old input if available
@endphp


<!-- Post Help Section -->

<section id="donation_help" class="contact section d-flex justify-content-center">
  <div class="container col-md-8" data-aos="fade-up">
    <div class="card shadow-lg  border-1 rounded-4">
      <div class="card-header text-black text-center rounded-top-4">
        <h3 class="mb-0">
          <i class="fa-solid fa-hand-holding-heart me-2"></i>Donate
        </h3>
      </div>

      <form action="{{ route('donationpost_update', ['post_id' => $helpPosts->id]) }} }}" method="post" class="p-4 php-email-form" data-aos="fade-up" enctype="multipart/form-data"
        data-aos-delay="200">
        @csrf
        
        <!-- Help Category Field -->
        <div class="row mb-4">
          <label for="donation_category" class="form-label fw-semibold">Category</label>
<select name="donation_category" id="donation_category" class="form-select" required>
    <option value="" disabled {{ $selectedCategory ? '' : 'selected' }}>Select a category</option>

    @foreach ($allCategories as $category)
        <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>
            {{ ucfirst($category) }}
        </option>
    @endforeach
</select>

    @if ($errors->has("donation_category"))
        <span class="text-danger small">{{ $errors->first("donation_category") }}</span>
    @endif
</div>

<div class="row mb-4">
  <label for="location" class="form-label fw-semibold">Location</label>
  <select id="location" name="location" class="form-control" style="width: 100%;">
    @if($helpPosts->location)
      <option value="{{ $helpPosts->location }}" selected>{{ $helpPosts->location }}</option>
    @endif
  </select>
<input type="hidden" id="latitude" name="latitude" value="{{ $helpPosts->latitude }}">
<input type="hidden" id="longitude" name="longitude" value="{{ $helpPosts->longitude }}">

  @if ($errors->has("location"))
    <span class="text-danger">
      {{ $errors->first("location") }}
    </span>           
  @endif
</div>

        <!-- Help Title Field -->
        <div class="mb-4">
          <label for="donation_title" class="form-label fw-semibold">Title</label>
          <input type="text" name="donation_title" id="donation_title" class="form-control" value="{{ $helpPosts->donation_title }}" required>
          @if ($errors->has("donation_title"))
        <span class="text-danger small">{{ $errors->first("donation_title") }}</span>
      @endif
        </div>

        <!-- Help Description Field -->
        <div class="mb-4">
          <label for="donation_description" class="form-label fw-semibold"> Description </label>
          <textarea name="donation_description" id="donation_description" class="form-control" rows="5" required> {{ $helpPosts->donation_description }} </textarea>
          @if ($errors->has("donation_description"))
        <span class="text-danger small">{{ $errors->first("donation_description") }}</span>
      @endif
        </div>
        <!-- Current Images -->
@if(!empty($helpPosts->donation_images))
    <div class="mb-4">
        <label class="form-label fw-semibold">Current Images</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach(json_decode($helpPosts->donation_images, true) ?? [] as $img)
                <div class="position-relative">
                    <img src="{{ asset($img) }}" alt="Donation Image" class="img-thumbnail" style="width:120px; height:120px; object-fit:cover;">
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Upload New Images -->
<div class="mb-4">
    <label for="donation_images" class="form-label fw-semibold">Upload New Images (Leave empty if you don’t want to change images)</label>
    <input type="file" name="donation_images[]" id="donation_images" class="form-control" multiple>
</div>
<!-- Submit and Back Buttons -->
<div class="text-center mt-4">
    <a href="{{ url()->previous() }}" class="btn btn-secondary px-4 me-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>

    <button type="submit" class="btn btn-primary px-5">
        <i class="fas fa-paper-plane me-1"></i> Update
    </button>
</div>
      </form>
    </div>
  </div>
</section>
<!-- JavaScript to handle occupation toggle -->
<!-- jQuery (needed for Select2) -->

<script>
 document.addEventListener("DOMContentLoaded", function () {
  const $select = $('#location');
  const oldLocation = "{{ $helpPosts->location }}";

  fetch("/bd_districts_areas.json")
    .then(response => response.json())
    .then(data => {
      data.districts.forEach(district => {
        if (district.name !== oldLocation) { // prevent duplicate of saved location
          const option = new Option(district.name, district.name, false, false);
          $select.append(option);
        }
      });

      $select.select2({
        placeholder: "Select your area",
        allowClear: true,
        width: '100%'
      });

      $select.on('select2:select', function (e) {
        const selectedName = e.params.data.id;
        const district = data.districts.find(d => d.name === selectedName);
        if (district) {
          $('#latitude').val(district.lat);
          $('#longitude').val(district.long);
        }
      });
    })
    .catch(error => console.error("Error loading JSON:", error));
});

</script>


@endsection
