<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{ 

    public function login(){
        return view('auth.login');
    }
public function loginPost(Request $request)
{
    $request->validate([
        "email" => "required|email",
        "password" => "required"
    ]);

    $credentials = $request->only("email", "password");

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Check role
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.categories'); // Admin panel
        } else {
            return redirect()->route('help_posts.show'); // Normal user
        }
    }

    return back()->with('error', 'Login failed');
}

    public function register(){
        return view('auth.regiform');
    }

public function RegiPost(Request $request)
{

    $request->validate([
        "username" => "required|max:15",
        "email" => "required|email|unique:users,email",
        "phone" => [
            "required",
            "regex:/^(01[3-9])[0-9]{8}$/",
            "unique:users,phone"
        ],
        "location" => "required",
        "latitude" => "required|numeric|between:-90,90",
        "longitude" => "required|numeric|between:-180,180",
        "password1" => [
            "required",
            "string",
            "min:8",
            "max:15",
            "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/"
        ],
        "password2" => "required|same:password1",
        "occupation" => "required|in:student,job,other",
        'institution_name' => 'nullable|string',
        'company_name' => 'nullable|string',
        'year' => 'nullable|string',
        'preferred_area' => 'required|array',
        'blood_group' => 'required_if:preferred_area,Blood Donation|nullable|string',
        'is_volunteer' => 'nullable|boolean',
    ], [
        'phone.regex' => 'Please enter valid BD phone number',
        'password1.regex' => 'Password must be 8–15 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'password2.same' => 'Password confirmation does not match.',
        'blood_group.required_if' => 'Blood group is required when Blood Donation is selected.',
    ]);

    $user = new User();
    $user->username = $request->username;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->location = $request->location;
    $user->latitude = $request->latitude;
    $user->longitude = $request->longitude;
    //     $user->latitude = "22.33510900";
    // $user->longitude = "91.8340730";
    $user->password = Hash::make($request->password2);
    $user->occupation = $request->occupation;
    $user->institution_name = $request->institution_name;
    $user->year = $request->year;
    $user->company_name = $request->company_name;
    $user->help_areas = $request->preferred_area ?? []; // save as JSON
    $user->blood_group = $request->blood_group;
    $user->is_volunteer = $request->has('is_volunteer');

    if ($user->save()) {
        return redirect(route("login"))->with("success", "User created successfully.");
    }

    return redirect(route("register"))->with("error", "Failed to create account");
}




public function logout(Request $request)
{

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->intended(route('home'));
}
public function showSettingForm()
{
    $user = Auth::user();
    return view('layouts.user.setting', compact('user'));
}
public function updateSetting(Request $request)
{
    $user = Auth::user();

    // Use old values if not edited
    $location = $request->location ?? $user->location;
    $latitude = $request->latitude ?? $user->latitude;
    $longitude = $request->longitude ?? $user->longitude;

    $validated = $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'unique:users,email,' . $user->user_id . ',user_id',
        'phone' => [
            'required',
            'regex:/^(01[3-9])[0-9]{8}$/',
            'unique:users,phone,' . $user->user_id . ',user_id',
        ],
        'location' => 'nullable|string',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'occupation' => 'required|string',
        'preferred_area' => 'required|array',
        'blood_group' => 'required_if:preferred_area.*,"Blood Donation"|nullable|string',
        'password1' => 'nullable|string|min:8|max:15|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/',
    ], [
        'phone.regex' => 'Please enter a valid BD phone number',
        'password1.regex' => 'Password must be 8–15 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'blood_group.required_if' => 'Blood group is required when Blood Donation is selected.',
    ]);

    $user->username = $validated['username'];
    $user->email = $validated['email'];
    $user->phone = $validated['phone'];
    $user->location = $location;
    $user->latitude = $latitude;
    $user->longitude = $longitude;
    $user->occupation = $validated['occupation'];
    $user->help_areas = $validated['preferred_area'];
    $user->blood_group = $validated['blood_group'] ?? $user->blood_group;
    $user->is_volunteer = $request->has('is_volunteer') ? 1 : 0;

    // Occupation fields
    if ($user->occupation === 'student') {
        $user->institution_name = $request->institution_name;
        $user->year = $request->year;
        $user->company_name = null;
    } elseif ($user->occupation === 'job') {
        $user->company_name = $request->company_name;
        $user->institution_name = null;
        $user->year = null;
    } else {
        $user->institution_name = null;
        $user->year = null;
        $user->company_name = null;
    }

    if (!empty($validated['password1'])) {
        $user->password = Hash::make($validated['password1']);
    }

    if ($user->save()) {
        return redirect(route("setting"))->with("success", "User updated successfully.");
    }

    return redirect(route("setting"))->with("error", "Failed to update account");
}

}