<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Models\User;
use App\Models\Test;
use Illuminate\Http\Request;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\DB;

// API Routes
Route::get('/api/user-session', function () {
    return response()->json(['user' => Auth::user()]);
});
//Admin API Routes
Route::delete('/api/users/bulk-delete', [UserController::class, 'bulkDeleteUsers']);
Route::put('/api/users/bulk-update', [UserController::class, 'bulkUpdateUsers']);
Route::put('/api/users/{id}/update-role', [UserController::class, 'updateRole']);
Route::put('/api/user/profile', [ProfileController::class, 'update'])->middleware('auth');
Route::get('/api/tests', function () {
    return Test::all();
})->middleware('auth');

//API: Group all tests by creator_id
Route::get('/api/tests/group-by-creator', function () {
    $grouped = \App\Models\Test::all()->groupBy('creator_id')->map(function ($tests) {
        return $tests->values();
    });
    return response()->json($grouped);
});

//Test API Routes
Route::get('/api/tests/all-with-users', [\App\Http\Controllers\TestController::class, 'allWithUsers']);
Route::get('/api/tests/{test}', [\App\Http\Controllers\TestController::class, 'show'])->middleware('auth');
Route::post('/api/user-answers', [\App\Http\Controllers\TestController::class, 'saveUserAnswers'])->middleware('auth');
Route::post('/api/save-polar-chart', [\App\Http\Controllers\TestController::class, 'savePolarChart'])->middleware('auth');


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

// Add a route for the test completion page
Route::get('/tests/{test}/complete', function ($testId) {
    return view('Tests/test_complete', ['testId' => $testId]);
})->middleware('auth')->name('tests.complete');

// Email Testing Route
Route::get('/send-test-email', function () {
    Mail::to('recipient@example.com')->send(new TestMail());
    return 'Test email sent!';
})->name('send-test-email');

// Test Routes
Route::get('/tests', [TestController::class, 'index'])->middleware('auth')->name('tests.index');
Route::get('/tests/create', function () {
    $questions = \App\Models\Question::all();
    return view('Tests/test_create', compact('questions'));
})->middleware('TestCreateMiddleware')->name('tests.create');
Route::post('/tests', [TestController::class, 'store'])->middleware('TestCreateMiddleware')->name('tests.store');

Route::get('/tests/{test}', function ($testId) {
    return view('test_complete', ['testId' => $testId]);
})->middleware('auth')->name('tests.complete');

// Edit Profile Route
Route::get('/profile/edit', function () {
    return view('edit_profile');
})->name('edit_profile')->middleware('auth');








