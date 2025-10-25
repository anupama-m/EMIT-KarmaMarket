@extends("layouts.default") @section("contents") <!-- signup Section -->
  <section id="signup" class="contact section">
    <div class="container card section-title p-4 shadow" data-aos="fade-up"> @if (session()->has("success"))
    <div class="alert alert-success"> {{ session()->get("success") }} </div> @endif @if (session()->has("error"))
      <div class="alert alert-danger"> {{ session()->get("error") }} </div> @endif <h3 class="mb-4">Sign Up</h3>
      <form action="{{ route("register.post") }}" method="post" class="php-email-form" data-aos="fade-up"
        data-aos-delay="200"> @csrf <!-- Username & email -->
        <div class="row">
          <div class="col-md-6 mb-4"> <input type="text" name="username" class="form-control" placeholder="User Name"
              required> @if ($errors->has("username")) <span class="text-danger"> {{ $errors->first("username") }} </span>
              @endif </div>
          <div class="col-md-6 mb-4"> <input type="email" name="email" class="form-control" placeholder="Email" required>
            @if ($errors->has("email")) <span class="text-danger"> {{ $errors->first("email") }} </span> @endif </div>
        </div> <!-- phone number & Location -->
        <div class="row">
          <div class="col-md-6 mb-4"> <input type="number" name="phone" class="form-control" placeholder="Phone Number"
              required> @if ($errors->has("phone")) <span class="text-danger"> {{ $errors->first("phone") }} </span>
              @endif </div>
          <div class="col-md-6 mb-4 pb-2"> <select id="location" name="location" class="form-control"
              style="width: 100%;">
              <option value="" disabled selected>Select your area</option>
            </select>
             <input type="hidden" id="latitude" name="latitude" />
              <input type="hidden" id="longitude" name="longitude" /> 
              @if ($errors->has("location")) <span class="text-danger">
              {{ $errors->first("location") }} </span> @endif </div>
        </div> <!-- password -->
        <div class="row">
          <div class="col-md-6 mb-4 pb-2"> <input type="password" name="password1" class="form-control"
              placeholder="Password" required> @if ($errors->has("password1")) <span class="text-danger">
              {{ $errors->first("password1") }} </span> @endif </div>
          <div class="col-md-6 mb-4 pb-2"> <input type="password" name="password2" class="form-control"
              placeholder="Confirm Password" required> @if ($errors->has("password2")) <span class="text-danger">
              {{ $errors->first("password2") }} </span> @endif </div>
        </div> <!-- Blood Group Field (hidden by default) -->
        <div class="row mb-4" id="bloodGroupField" style="display: none;">
          <div class="col-md-6"> <select name="blood_group" id="blood_group" class="form-control">
              <option value="" disabled selected>Select Blood Group</option>
              <option value="A+">A+</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B-">B-</option>
              <option value="O+">O+</option>
              <option value="O-">O-</option>
              <option value="AB+">AB+</option>
              <option value="AB-">AB-</option>
            </select> @if ($errors->has("blood_group")) <span
            class="text-danger">{{ $errors->first("blood_group") }}</span> @endif </div>
        </div> <!-- Occupation and Preferred area -->
        <div class="row">
          <div class="col-md-6 mb-4">
            <h6 class="mb-2 pb-1">Occupation: </h6>
            <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="occupation"
                id="studentRadio" value="student"> <label class="form-check-label" for="studentRadio">Student</label>
            </div>
            <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="occupation"
                id="jobRadio" value="job"> <label class="form-check-label" for="jobRadio">Job Holder</label> </div>
            <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="occupation"
                id="otherRadio" value="other" checked> <label class="form-check-label" for="otherRadio">Other</label>
            </div>
          </div> <!-- Preferred Areas to Help (Multi-select Buttons) -->
          <div class="col-md-6 mb-4 pb-2"> <label class="form-label d-block mb-2">Preferred Area to Help:</label>
            <div class="btn-group" role="group" aria-label="Preferred Help Areas"> <input type="checkbox"
                class="btn-check" id="area1" name="preferred_area[]" value="Book" autocomplete="off"> <label
                class="btn btn-outline-primary me-2 mb-2" for="area1">Book</label> <input type="checkbox"
                class="btn-check" id="area2" name="preferred_area[]" value="Medical" autocomplete="off"> <label
                class="btn btn-outline-primary me-2 mb-2" for="area2">Medical</label> <input type="checkbox"
                class="btn-check" id="area3" name="preferred_area[]" value="Blood Donation" autocomplete="off"> <label
                class="btn btn-outline-primary me-2 mb-2" for="area3">Blood Donation</label> <input type="checkbox"
                class="btn-check" id="area4" name="preferred_area[]" value="Clothes" autocomplete="off"> <label
                class="btn btn-outline-primary me-2 mb-2" for="area4">Clothes</label> </div>
          </div> @if ($errors->has("preferred_area")) <span class="text-danger"> {{ $errors->first("preferred_area") }}
          </span> @endif @if ($errors->has("occupation")) <span class="text-danger"> {{ $errors->first("occupation") }}
          </span> @endif
        </div> <!-- Student Fields -->
        <div class="row" id="studentFields" style="display: none;">
          <div class="col-md-6 mb-4"> <input type="text" name="institution_name" class="form-control"
              placeholder="Institution name"> </div>
          <div class="col-md-6 mb-4"> <select class="form-control" name="year">
              <option value="" disabled selected>Select Year</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
            </select> </div>
        </div> <!-- Job Fields -->
        <div class="row" id="jobFields" style="display: none;">
          <div class="col-md-6 mb-4"> <input type="text" name="company_name" class="form-control"
              placeholder="Company name"> </div>
        </div> <!-- Volunteer Checkbox -->
        <div class="row">
          <div class="col-md-12 mb-4">
            <div class="form-check">
              <div class="d-flex justify-content-center align-items-center my-3"> <input type="checkbox" value="1"
                  id="is_volunteer" name="is_volunteer" class="form-check-input me-2" /> <label for="volunteer"
                  class="form-check-label fs-5"> I want to volunteer and be notified when activities are posted. </label>
              </div>
            </div>
          </div>
        </div> <button type="submit" class="btn btn-outline-primary">Sign Up</button>
      </form>
    </div>
  </section> <!-- JavaScript to handle occupation toggle -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ----- Occupation Toggle -----
    const studentRadio = document.getElementById("studentRadio");
    const jobRadio = document.getElementById("jobRadio");
    const otherRadio = document.getElementById("otherRadio");
    const studentFields = document.getElementById("studentFields");
    const jobFields = document.getElementById("jobFields");

    function toggleFields() {
        if (studentRadio.checked) {
            studentFields.style.display = "flex";
            jobFields.style.display = "none";
        } else if (jobRadio.checked) {
            studentFields.style.display = "none";
            jobFields.style.display = "flex";
        } else {
            studentFields.style.display = "none";
            jobFields.style.display = "none";
        }
    }

    studentRadio.addEventListener("change", toggleFields);
    jobRadio.addEventListener("change", toggleFields);
    otherRadio.addEventListener("change", toggleFields);
    toggleFields(); // initialize on page load

    // ----- Blood Donation Checkbox -----
    const bloodDonationCheckbox = document.getElementById("area3");
    const bloodGroupField = document.getElementById("bloodGroupField");

    function toggleBloodGroupField() {
        if (bloodDonationCheckbox.checked) {
            bloodGroupField.style.display = "flex";
        } else {
            bloodGroupField.style.display = "none";
            document.getElementById("blood_group").value = ""; // reset selection
        }
    }

    bloodDonationCheckbox.addEventListener("change", toggleBloodGroupField);
    toggleBloodGroupField(); // initialize on page load

    // ----- Location Select & Lat/Long -----
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