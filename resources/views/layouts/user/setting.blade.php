@extends("layouts.user.user_default")

@section("contents")

<!-- Profile Settings -->

<section id="setting" class="contact section d-flex justify-content-center">
    <div class="container col-md-8" data-aos="fade-up">
        <div class="card shadow-lg border-1 rounded-4">
            <div class="card-header text-black text-center rounded-top-4">
                <h3 class="mb-0">
            
<i class="fa-solid fa-circle-exclamation"></i> Update Information
                </h3>
            </div>

            <form action="{{ route('setting.update') }}" method="post" enctype="multipart/form-data" class="p-4 php-email-form" data-aos="fade-up" data-aos-delay="200">
                @csrf

                <!-- Username & Email -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <input type="text" name="username" class="form-control" placeholder="User Name" required
                            value="{{ old('username', $user->username) }}">
                        @error('username')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <input type="email" name="email" class="form-control" placeholder="Email" required
                            value="{{ old('email', $user->email) }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Phone Number & Location -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <input type="number" name="phone" class="form-control" placeholder="Phone Number" required
                            value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
 <select id="location" name="location" class="form-control" style="width: 100%;">
  <option value="{{ $user->location }}" disabled selected>{{ $user->location }}</option>
</select>
        <input type="hidden" id="latitude" name="latitude" />
        <input type="hidden" id="longitude" name="longitude" />
                        @error('location')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <input type="password" name="password1" class="form-control" placeholder="New Password (Optional)">
                        @error('password1')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Occupation & Preferred Areas -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="mb-2 pb-1">Occupation: </h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="occupation" id="studentRadio" value="student"
                                {{ old('occupation', $user->occupation) == 'student' ? 'checked' : '' }}>
                            <label class="form-check-label" for="studentRadio">Student</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="occupation" id="jobRadio" value="job"
                                {{ old('occupation', $user->occupation) == 'job' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jobRadio">Job Holder</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="occupation" id="otherRadio" value="other"
                                {{ old('occupation', $user->occupation) == 'other' ? 'checked' : '' }}>
                            <label class="form-check-label" for="otherRadio">Other</label>
                        </div>
                        @error('occupation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                 <!-- Preferred Help Areas -->
<div class="col-md-6 mb-4">
    <label class="form-label d-block mb-2">Preferred Area to Help:</label>
    @php
        $selectedAreas = old('preferred_area', $user->help_areas ?? []);
        $selectedBloodGroup = old('blood_group', $user->blood_group ?? '');
    @endphp
    @foreach (['Book', 'Medical', 'Blood Donation', 'Clothes'] as $area)
        <input type="checkbox" class="btn-check preferred-area-checkbox" id="area_{{ $area }}" name="preferred_area[]" value="{{ $area }}"
            {{ in_array($area, $selectedAreas) ? 'checked' : '' }}>
        <label class="btn btn-outline-secondary me-2 mb-2" for="area_{{ $area }}">{{ $area }}</label>
    @endforeach
    @error('preferred_area')
        <span class="text-danger d-block">{{ $message }}</span>
    @enderror
</div>

<!-- Blood Group Field (hidden by default) -->
<div class="row mb-4" id="bloodGroupField" style="display: none;">
    <div class="col-md-6">
        <select name="blood_group" id="blood_group" class="form-control">
            <option value="" disabled {{ $selectedBloodGroup == '' ? 'selected' : '' }}>Select Blood Group</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                <option value="{{ $bg }}" {{ $selectedBloodGroup == $bg ? 'selected' : '' }}>{{ $bg }}</option>
            @endforeach
        </select>
        @error('blood_group')
            <span class="text-danger d-block">{{ $message }}</span>
        @enderror
    </div>
</div>

                <!-- Student Fields -->
                <div class="row" id="studentFields" style="display: none;">
                    <div class="col-md-6 mb-4">
                        <input type="text" name="institution_name" class="form-control" placeholder="Institution name"
                            value="{{ old('institution_name', $user->institution_name) }}">
                    </div>
                    <div class="col-md-6 mb-4">
                        <select class="form-control" name="year">
                            <option value="" disabled>Select Year</option>
                            @foreach (['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'] as $val => $label)
                                <option value="{{ $val }}" {{ old('year', $user->year) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Job Fields -->
                <div class="row" id="jobFields" style="display: none;">
                    <div class="col-md-6 mb-4">
                        <input type="text" name="company_name" class="form-control" placeholder="Company name"
                            value="{{ old('company_name', $user->company_name) }}">
                    </div>
                </div>

                <!-- Volunteer Checkbox -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="form-check">
                            <div class="d-flex justify-content-center align-items-center my-3">
                                <input type="checkbox" value="1" id="is_volunteer" name="is_volunteer" class="form-check-input me-2"
                                    style="transform: scale(1.5);" {{ old('is_volunteer', $user->is_volunteer) ? 'checked' : '' }} />
                                <label for="is_volunteer" class="form-check-label fs-5">
                                    I want to volunteer and be notified when activities are posted.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3 px-5">
                        <i class="fas fa-paper-plane me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Show/hide student/job fields -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

      // Toggle blood group field if "Blood Donation" is selected
function toggleBloodGroupField() {
    const bloodDonationCheckbox = document.getElementById('area_Blood Donation');
    const bloodGroupField = document.getElementById('bloodGroupField');
    if (bloodDonationCheckbox.checked) {
        bloodGroupField.style.display = 'flex';
    } else {
        bloodGroupField.style.display = 'none';
        document.getElementById('blood_group').value = ''; // reset selection
    }
}

// Attach change event to Blood Donation checkbox
document.querySelectorAll('.preferred-area-checkbox').forEach(el => {
    el.addEventListener('change', toggleBloodGroupField);
});

// Initialize on page load
toggleBloodGroupField();

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
        function toggleFields() {
            document.getElementById('studentFields').style.display = document.getElementById('studentRadio').checked ? 'flex' : 'none';
            document.getElementById('jobFields').style.display = document.getElementById('jobRadio').checked ? 'flex' : 'none';
        }

        document.getElementById('studentRadio').addEventListener('change', toggleFields);
        document.getElementById('jobRadio').addEventListener('change', toggleFields);
        document.getElementById('otherRadio').addEventListener('change', toggleFields);

        toggleFields(); // Initial check
    });
</script>
@endsection