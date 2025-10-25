@extends("layouts.user.user_default")

@section("contents")

  <!-- Donate Section -->

  <section id="donate" class="contact section d-flex justify-content-center">
    <div class="container col-md-8" data-aos="fade-up">
    <div class="card shadow-lg  border-1 rounded-4">
      <div class="card-header text-black text-center rounded-top-4">
      <h3 class="mb-0">
        <i class="fa-solid fa-hand-holding-heart me-2"></i>Donate
      </h3>
      </div>

      <form action="{{ route('donation.store') }}" method="post" enctype="multipart/form-data"
      class="p-4 php-email-form" data-aos="fade-up" data-aos-delay="200">
      @csrf

      <!-- Help Title Field -->
      <div class="mb-4">
        <label for="donation_title" class="form-label fw-semibold">
        Title
        </label>
        <input type="text" name="donation_title" id="donation_title" class="form-control"
        placeholder="Enter the title of your post" required>
        @if ($errors->has("donation_title"))
      <span class="text-danger small">{{ $errors->first("donation_title") }}</span>
      @endif
      </div>

      <!-- Help Category Field -->
      <div class="row mb-4">
        <div class="col-md-6">
        <label for="donation_category" class="form-label fw-semibold">
          Category
        </label>
        <select name="donation_category" id="donation_category" class="form-select" required>
          <option value="" disabled selected>Select a Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->name }}" {{ old('post_category') == $category->name ? 'selected' : '' }}>
                {{ ucfirst($category->name) }}
            </option>
        @endforeach
    </select>

        @if ($errors->has("donation_category"))
      <span class="text-danger small">{{ $errors->first("donation_category") }}</span>
      @endif
        </div>
        
        <div class="col-md-6">
        <label for="location" class="form-label fw-semibold">Area</label>
        <select id="location" name="location" class="form-control" style="width: 100%;" required>
          <option value="" disabled selected>Select your area</option>
        </select>
        @if ($errors->has("location"))
      <span class="text-danger">{{ $errors->first("location") }}</span>
      @endif
        <input type="hidden" id="latitude" name="latitude" />
        <input type="hidden" id="longitude" name="longitude" />
        </div>
      </div>
      <!-- Help Description Field -->
      <div class="mb-4">
        <label for="donation_description" class="form-label fw-semibold">
        Description
        </label>
        <textarea name="donation_description" id="donation_description" class="form-control" rows="5"
        placeholder="Describe your donated item..." required></textarea>
        @if ($errors->has("donation_description"))
      <span class="text-danger small">{{ $errors->first("donation_description") }}</span>
      @endif
      </div>

      <!-- Help Image Upload Field -->
      <div class="mb-4">
        <label for="donation_images" class="form-label fw-semibold">
        Upload Images (optional)
        </label>
        <input type="file" name="donation_images[]" id="donation_images" class="form-control" accept="image/*"
        multiple>
        @if ($errors->has("donation_images"))
      <span class="text-danger small">{{ $errors->first("donation_images") }}</span>
      @endif
      </div>

      <!-- Submit Button -->
      <div class="text-center">
        <button type="submit" class="btn btn-primary mt-3 px-5">
        <i class="fas fa-paper-plane me-1"></i> Submit
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
    });
  </script>

@endsection