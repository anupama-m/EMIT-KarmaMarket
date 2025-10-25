@extends("layouts.user.user_default")

@section("contents")

@php
    $allCategories = ['book', 'medical', 'blood', 'clothes','volunteer'];
    $selectedCategory = old('post_category', $helpPosts->post_category);
@endphp

<section id="post_help" class="contact section d-flex justify-content-center">
  <div class="container col-md-8" data-aos="fade-up">
    <div class="card shadow-lg border-1 rounded-4">
      <div class="card-header text-black text-center rounded-top-4">
        <h3 class="mb-0"><i class="fa-solid fa-handshake-angle"></i> Edit Help Post</h3>
      </div>

      <form action="{{ route('post_update', ['post_id' => $helpPosts->post_id]) }}" method="post" class="p-4 php-email-form" data-aos="fade-up"
        data-aos-delay="200">
        @csrf

        <!-- Help Category Field -->
        <div class="row mb-4">
          <div class="col-md-6">
            <label for="post_category" class="form-label fw-semibold">Category</label>
            <select name="post_category" id="post_category" class="form-select" required>
                <option value="" disabled {{ $selectedCategory ? '' : 'selected' }}>Select a category</option>
                @foreach ($allCategories as $category)
                    <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>
                        {{ ucfirst($category) }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has("post_category"))
                <span class="text-danger small">{{ $errors->first("post_category") }}</span>
            @endif
          </div>

          <!-- Location Field -->
          <div class="col-md-6">
            <label for="location" class="form-label fw-semibold">Area</label>
            <select id="location" name="post_location" class="form-control" style="width: 100%;">
                @if($helpPosts->post_location)
                  <option value="{{ $helpPosts->post_location }}" selected>{{ $helpPosts->post_location }}</option>
                @endif
            </select>
            @if ($errors->has("post_location"))
                <span class="text-danger">{{ $errors->first("post_location") }}</span>
            @endif
            <input type="hidden" id="latitude" name="latitude" value="{{ $helpPosts->latitude ?? '' }}" />
            <input type="hidden" id="longitude" name="longitude" value="{{ $helpPosts->longitude ?? '' }}" />
          </div>
        </div>

        <!-- Blood-specific fields (hidden by default, show if category=blood) -->
        <div id="bloodFields" class="mb-4">
          <div class="row">
            <div class="col-md-6">
              <label for="blood_group" class="form-label fw-semibold">Blood Group</label>
              <select name="blood_group" id="blood_group" class="form-select">
                <option value="" disabled {{ $helpPosts->blood_group ? '' : 'selected' }}>Select Blood Group</option>
                @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                    <option value="{{ $bg }}" {{ $helpPosts->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
              </select>
              @if ($errors->has("blood_group"))
                <span class="text-danger small">{{ $errors->first("blood_group") }}</span>
              @endif
            </div>

            <div class="col-md-6">
              <label for="hospital_name" class="form-label fw-semibold">Hospital Name</label>
              <input type="text" name="hospital_name" id="hospital_name" class="form-control"
                     placeholder="Enter hospital name" value="{{ $helpPosts->hospital_name ?? '' }}">
              @if ($errors->has("hospital_name"))
                <span class="text-danger small">{{ $errors->first("hospital_name") }}</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Help Title Field -->
        <div class="mb-4">
          <label for="post_title" class="form-label fw-semibold">Title</label>
          <input type="text" name="post_title" id="post_title" class="form-control" value="{{ $helpPosts->post_title }}" required>
          @if ($errors->has("post_title"))
            <span class="text-danger small">{{ $errors->first("post_title") }}</span>
          @endif
        </div>

        <!-- Help Description Field -->
        <div class="mb-4">
          <label for="post_description" class="form-label fw-semibold">Description</label>
          <textarea name="post_description" id="post_description" class="form-control" rows="5" required>{{ $helpPosts->post_description }}</textarea>
          @if ($errors->has("post_description"))
            <span class="text-danger small">{{ $errors->first("post_description") }}</span>
          @endif
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

<script>
document.addEventListener("DOMContentLoaded", function () {
  const $select = $('#location');
  const oldLocation = "{{ $helpPosts->post_location ?? '' }}";

  // Load districts from JSON
  fetch("/bd_districts_areas.json")
    .then(response => response.json())
    .then(data => {
      data.districts.forEach(district => {
        if(district.name !== oldLocation){
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

  // Toggle blood fields when category changes
  const postCategory = document.getElementById("post_category");
  const bloodFields = document.getElementById("bloodFields");

  const toggleBloodFields = () => {
    if (postCategory.value === "blood") {
      bloodFields.style.display = "block";
    } else {
      bloodFields.style.display = "none";
      document.getElementById("blood_group").value = "";
      document.getElementById("hospital_name").value = "";
    }
  }

  // Run on change
  postCategory.addEventListener("change", toggleBloodFields);

  // Run once on page load
  toggleBloodFields();
});

</script>

@endsection
