@extends("layouts.user.user_default")

@section("contents")
  <section id="post_help" class="contact section d-flex justify-content-center">
    <div class="container col-md-8" data-aos="fade-up">
    <div class="card shadow-lg border-1 rounded-4">
      <div class="card-header text-black text-center rounded-top-4">
      <h3 class="mb-0">
        <i class="fas fa-hands-helping me-2"></i>Request for Help
      </h3>
      </div>

      <form action="{{ route('help_post.store') }}" method="post" class="p-4 php-email-form" data-aos="fade-up"
      data-aos-delay="200">
      @csrf


      <!-- Help Category Field -->
      <div class="row mb-4">
<div class="col-md-6">
    <label for="post_category" class="form-label fw-semibold">Category</label>
    <select name="post_category" id="post_category" class="form-select" required>
        <option value="" disabled selected>Select a Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->name }}" {{ old('post_category') == $category->name ? 'selected' : '' }}>
                {{ ucfirst($category->name) }}
            </option>
        @endforeach
    </select>
    @if ($errors->has("post_category"))
        <span class="text-danger small">{{ $errors->first("post_category") }}</span>
    @endif
</div>

        <div class="col-md-6">
        <label for="location" class="form-label fw-semibold">Area</label>
        <select id="location" name="location" class="form-control" style="width: 100%;">
          <option value="" disabled selected>Select your area</option>
        </select>
        @if ($errors->has("location"))
      <span class="text-danger">{{ $errors->first("location") }}</span>
      @endif
        <input type="hidden" id="latitude" name="latitude" />
        <input type="hidden" id="longitude" name="longitude" />
        </div>
      </div>

      <!-- Blood-specific fields (hidden by default) -->
      <div id="bloodFields" class="mb-4" style="display: none;">
        <div class="row">
        <div class="col-md-6">
          <label for="blood_group" class="form-label fw-semibold">Blood Group</label>
          <select name="blood_group" id="blood_group" class="form-select">
          <option value="" disabled selected>Select Blood Group</option>
          <option value="A+">A+</option>
          <option value="A-">A-</option>
          <option value="B+">B+</option>
          <option value="B-">B-</option>
          <option value="O+">O+</option>
          <option value="O-">O-</option>
          <option value="AB+">AB+</option>
          <option value="AB-">AB-</option>
          </select>
          @if ($errors->has("blood_group"))
        <span class="text-danger small">{{ $errors->first("blood_group") }}</span>
      @endif
        </div>

        <div class="col-md-6">
          <label for="hospital_name" class="form-label fw-semibold">Hospital Name</label>
          <input type="text" name="hospital_name" id="hospital_name" class="form-control"
          placeholder="Enter hospital name">
          @if ($errors->has("hospital_name"))
        <span class="text-danger small">{{ $errors->first("hospital_name") }}</span>
      @endif
        </div>
        </div>
      </div>

      <!-- Help Title Field -->
      <div class="mb-4">
        <label for="post_title" class="form-label fw-semibold">Title</label>
        <input type="text" name="post_title" id="post_title" class="form-control"
        placeholder="Enter the title of your request" required>
        @if ($errors->has("post_title"))
      <span class="text-danger small">{{ $errors->first("post_title") }}</span>
      @endif
      </div>

      <!-- Help Description Field -->
      <div class="mb-4">
        <label for="post_description" class="form-label fw-semibold">Description</label>
        <textarea name="post_description" id="post_description" class="form-control" rows="5"
        placeholder="Describe the help you need..." required></textarea>
        @if ($errors->has("post_description"))
      <span class="text-danger small">{{ $errors->first("post_description") }}</span>
      @endif
      </div>

      <!-- Submit Button -->
      <div class="text-center">
        <button type="submit" class="btn btn-primary mt-3 px-5">
        <i class="fas fa-paper-plane me-1"></i> Submit Request
        </button>
      </div>
      </form>
    </div>
    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function () {

    const $select = $('#location');

    fetch("/bd_districts_areas.json")
      .then(response => response.json())
      .then(data => {
      // data.districts is an array of district objects
      data.districts.forEach(district => {
        // Use district.name for both option text and value
        const option = new Option(district.name, district.name, false, false);
        $select.append(option);
      });

      $select.select2({
        placeholder: "Select your area",
        allowClear: true,
        width: '100%'
      });

      // Add event listener here
      $select.on('select2:select', function (e) {
        const selectedName = e.params.data.id; // selected district name
        const district = data.districts.find(d => d.name === selectedName);
        if (district) {
        $('#latitude').val(district.lat);
        $('#longitude').val(district.long);
        }
      });
      })
      .catch(error => console.error("Error loading JSON:", error));


    // Toggle blood fields
    const postCategory = document.getElementById("post_category");
    const bloodFields = document.getElementById("bloodFields");

    postCategory.addEventListener("change", function () {
      if (this.value === "blood") {
      bloodFields.style.display = "block";
      } else {
      bloodFields.style.display = "none";
      document.getElementById("blood_group").value = "";
      document.getElementById("hospital_name").value = "";
      }
    });
    });
  </script>
@endsection