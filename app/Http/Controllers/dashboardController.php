<?php

namespace App\Http\Controllers;
use App\Models\HelpPost;
class dashboardController extends Controller
{

public function index()
{
    // You can pass user data here if needed
    return view('layouts.user.dashboard');
}    

}
