<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\HelpPostController;
use App\Http\Controllers\HelpPostApprovalController;
use App\Models\User;
use App\Models\HelpPostApproval;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\donatePostController;
use App\Http\Controllers\BuyKarmaController;
use App\Http\Controllers\DonationApprovalController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//For home page
Route::view("/", "layouts.home")->name('home');

//For Registration
Route::get("/register", [AuthController::class, "register"])->name('register');
Route::post("/register", [AuthController::class, "RegiPost"])->name('register.post');

//For Login
Route::get("/login", [AuthController::class, "login"])->name('login');
Route::post("/login", [AuthController::class, "loginPost"])->name('login.post');


//logout
Route::post("/home", [AuthController::class, "logout"])->name('logout');


Route::middleware(['auth'])->group(function () {

    //HELP POSTS

    //storing help form
    Route::post('/user/help_post', [HelpPostController::class, 'store'])->name('help_post.store');
    // showing posts
   // Route::get('/posts/{type?}', [HelpPostController::class, 'showPosts'])->name('posts.show');
    //showing post details
    Route::get('/user/post_details/{post_id}', [HelpPostController::class, 'show_details'])->name('post_details');
    //showing help form
    Route::get('/help_post', [HelpPostController::class, 'index'])->name('help_post');
    //Edit & update posts
    Route::get('/post/edit/{post_id}', [HelpPostController::class, 'edit'])->name('post_edit');
    Route::post('/post/update/{post_id}', [HelpPostController::class, 'update'])->name('post_update');
    //Delete posts
    Route::delete('/post/delete/{post_id}', [HelpPostApprovalController::class, 'cancelRequest'])->name('post_delete');
    //setting- editing user info
    Route::get('/setting', [AuthController::class, 'showSettingForm'])->name('setting');
    Route::post('/setting', [AuthController::class, 'updateSetting'])->name('setting.update');

    //Help Post approval
    Route::post('/post/help/{post_id}', [HelpPostApprovalController::class, 'store'])->name('post.help');
    Route::get('/post/approvals/{post_id}', [HelpPostApprovalController::class, 'viewApprovals'])->name('post.approvals');
    Route::post('/post/approvals/accept/{approval_id}', [HelpPostApprovalController::class, 'accept'])->name('post.approvals.accept');
    Route::post('/post/approvals/reject/{approval_id}', [HelpPostApprovalController::class, 'reject'])->name('post.approvals.reject');
    Route::delete('/post/help/{approval_id}/cancel', [HelpPostApprovalController::class, 'cancelRequest'])->name('post.help.cancel');
    Route::post('/help-post/{approval_id}/cancel-request/confirm', [HelpPostApprovalController::class, 'confirmCancelRequest'])->name('post.help.cancel.request.confirm');

    //Help confirmation
    Route::post('/post/approvals/{approval_id}/confirm', [HelpPostApprovalController::class, 'confirm'])->name('post.confirm.help');

    //notification
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.read');


    //DONATION POSTS 

    //donate post
    Route::get('/donate_post', [donatePostController::class, 'index'])->name('donate_post');
    Route::post('/donate/store', [donatePostController::class, 'store'])->name('donation.store');
    // showing posts
    Route::get('/donate/view', [donatePostController::class, 'showPosts'])->name('UserDonationPosts.show');
    //showing post details
    Route::get('/user/donation_post_details/{post_id}', [donatePostController::class, 'show_details'])->name('donation_post_details');
    //Donation filter & search
    Route::get('/donations/ajax', [donatePostController::class, 'ajaxPosts'])
        ->name('donation_posts.ajax');
    //Edit & update posts
    Route::get('/donation/edit/{post_id}', [donatePostController::class, 'edit'])->name('donationpost_edit');
    Route::post('/donation/update/{post_id}', [donatePostController::class, 'update'])->name('donationpost_update');

    //Buy With Karma
    Route::get('/user/buy_karma', [BuyKarmaController::class, 'index'])->name('buy.karma');
    Route::get('/user/buy_karma/donations', [BuyKarmaController::class, 'search_buyKarma'])->name('donations.search');

    //request the item
    Route::post('/donation/request/{post_id}', [DonationApprovalController::class, 'store'])->name('request.item');
    //owner seeing total requests
    Route::get('/donation/{post_id}/requests', [DonationApprovalController::class, 'index'])->name('donation.requests');

    // accpeting a request for donation
    Route::post('/donation/accept/{approval_id}', [DonationApprovalController::class, 'accept'])
        ->name('donation.accept');
    // requester confirming not owner 
    Route::post('/donation/confirm/{approval_id}', [DonationApprovalController::class, 'confirmDonation'])
        ->name('donation.confirm');
    //owner delete and requester cancel
    Route::delete('/donations/{post}/delete', [donatePostController::class, 'deletePost'])->name('donation.delete');
Route::delete('/approvals/{approval}/cancel', [DonationApprovalController::class, 'cancelRequest'])->name('donation.cancel_request');
// Initial page load
Route::get('/help-posts', [HelpPostController::class, 'showHelpPosts'])
    ->name('help_posts.show');

// AJAX filtering & search
Route::get('/help-posts/ajax', [HelpPostController::class, 'ajaxHelpPosts'])
    ->name('help_posts.ajax');


});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard redirect
    Route::get('/', function () {
        return redirect()->route('admin.categories');
    });

    /** ----------------
     * Categories
     * ----------------*/
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    /** ----------------
     * Posts (Help + Donation)
     * ----------------*/
  Route::get('/posts', [AdminController::class, 'posts'])->name('posts'); // main page
    Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('posts.destroy'); // delete
    Route::get('/posts/ajax', [AdminController::class, 'ajaxPosts'])->name('posts.ajax'); // AJAX search

    /** ----------------
     * Users
     * ----------------*/
// Show Manage Users page
Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('users');

// AJAX search users
Route::get('/admin/users/ajax', [AdminController::class, 'ajaxUsers'])->name('users.ajax');

// Delete user
Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

});
