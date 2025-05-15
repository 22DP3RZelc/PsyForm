<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Models\User;

// API Routes
Route::get('/api/user-session', function () {
    return response()->json(['user' => Auth::user()]);
});
Route::delete('/api/users/bulk-delete', [UserController::class, 'bulkDeleteUsers']);
Route::put('/api/users/bulk-update', [UserController::class, 'bulkUpdateUsers']);
Route::put('/api/users/{id}/update-role', [UserController::class, 'updateRole']);
Route::put('/api/user/profile', [ProfileController::class, 'update'])->middleware('auth');

// Admin Routes
Route::get('/admin', function () {
    return view('admin');
})->middleware('admin');
Route::delete('/users/{user}', [UserController::class, 'removeUser'])->middleware('admin');
Route::get('/users', [UserController::class, 'getUsers'])->middleware('admin');

//LogIn Routes
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

//Logout Route
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');
Route::get('/logout', function() {
    return redirect('/login');
});

// Registration Routes
Route::get('/register', function () {
    return view('registration');
});
Route::post('/register', [RegistrationController::class, 'registerAndRedirect']);

// User Routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile')->middleware('auth');
Route::get('/users', function () {
    return User::all();
})->middleware('auth');

// General Routes
Route::get('/', function () {
    return view('about');
})->name('about');
Route::get('/home', function () {
    return view('home');
})->name('about');
Route::get('/example', function () {
    return view('example');
})->name('example');

// Email Testing Route
Route::get('/send-test-email', function () {
    Mail::to('recipient@example.com')->send(new TestMail());
    return 'Test email sent!';
})->name('send-test-email');






