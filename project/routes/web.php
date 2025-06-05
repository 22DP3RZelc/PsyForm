<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestCreateController;
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
Route::delete('/api/users/bulk-delete', [UserController::class, 'bulkDeleteUsers']);
Route::put('/api/users/bulk-update', [UserController::class, 'bulkUpdateUsers']);
Route::put('/api/users/{id}/update-role', [UserController::class, 'updateRole']);
Route::put('/api/user/profile', [ProfileController::class, 'update'])->middleware('auth');
Route::get('/api/tests', function () {
    return Test::all();
})->middleware('auth');

Route::get('/api/tests/{test}', function ($testId) {
    $test = \App\Models\Test::with('questions')->findOrFail($testId);
    return response()->json($test);
})->middleware('auth');

Route::post('/api/user-answers', function (Request $request) {
    $userId = Auth::id();
    $answers = $request->input('answers', []);
    foreach ($answers as $questionId => $answer) {
        UserAnswer::create([
            'user_id' => $userId,
            'quest_id' => $questionId,
            'answer' => $answer,
        ]);
    }
    return response()->json(['message' => 'Answers saved']);
})->middleware('auth');

// Add this route to handle saving the polar chart
Route::post('/api/save-polar-chart', function(\Illuminate\Http\Request $request) {
    // Get the normalized data and labels from the request or use dummy data for now
    $labels = [
        "Positive Affect", "Positive Activation", "Activation", "Negative Activation",
        "Negative Affect", "Negative Deactivation", "Deactivation", "Positive Deactivation"
    ];
    $answers = $request->input('answers', []);
    $values = array_values($answers);
    $normalized = array_map(fn($v) => floatval($v) / 9, $values);

    // Generate a chart using QuickChart.io (no server-side image generation needed)
    $chartConfig = [
        "type" => "radar",
        "data" => [
            "labels" => $labels,
            "datasets" => [[
                "label" => "User Data",
                "data" => $normalized,
                "backgroundColor" => "rgba(0, 123, 255, 0.3)",
                "borderColor" => "red",
                "borderWidth" => 2,
                "pointBackgroundColor" => "red"
            ]]
        ],
        "options" => [
            "responsive" => true,
            "scales" => [
                "r" => [
                    "min" => 0,
                    "max" => 1,
                    "ticks" => [ "display" => false ],
                    "pointLabels" => [
                        "font" => [
                            "size" => 12,
                            "weight" => "bold"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));

    return response()->json([
        'chart_url' => $chartUrl
    ]);
})->middleware('auth');

// Add this route to handle saving the polar chart and result in the database
Route::post('/api/save-polar-chart', function(\Illuminate\Http\Request $request) {
    $userId = Auth::id();
    $testId = $request->input('test_id');
    $answers = $request->input('answers', []);

    // Prepare chart data
    $labels = [
        "Positive Affect", "Positive Activation", "Activation", "Negative Activation",
        "Negative Affect", "Negative Deactivation", "Deactivation", "Positive Deactivation"
    ];
    $values = array_values($answers);
    $normalized = array_map(fn($v) => floatval($v) / 9, $values);

    // Generate chart URL using QuickChart.io
    $chartConfig = [
        "type" => "radar",
        "data" => [
            "labels" => $labels,
            "datasets" => [[
                "label" => "User Data",
                "data" => $normalized,
                "backgroundColor" => "rgba(0, 123, 255, 0.3)",
                "borderColor" => "red",
                "borderWidth" => 2,
                "pointBackgroundColor" => "red"
            ]]
        ],
        "options" => [
            "responsive" => true,
            "scales" => [
                "r" => [
                    "min" => 0,
                    "max" => 1,
                    "ticks" => [ "display" => false ],
                    "pointLabels" => [
                        "font" => [
                            "size" => 12,
                            "weight" => "bold"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));

    // Save result in the results table
    DB::table('results')->insert([
        'user_id' => $userId,
        'test_id' => $testId,
        'calc_result' => $chartUrl,
    ]);

    return response()->json([
        'chart_url' => $chartUrl
    ]);
})->middleware('auth');

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

// Update /all-tests to return the Vue page (not just a view)


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
Route::get('/tests', [TestCreateController::class, 'index'])->middleware('auth')->name('tests.index');
Route::get('/tests/create', function () {
    $questions = \App\Models\Question::all();
    return view('Tests/test_create', compact('questions'));
})->middleware('auth')->name('tests.create');
Route::post('/tests', [TestCreateController::class, 'store'])->middleware('auth')->name('tests.store');

Route::get('/tests/{test}', function ($testId) {
    return view('test_complete', ['testId' => $testId]);
})->middleware('auth')->name('tests.complete');

// Edit Profile Route
Route::get('/profile/edit', function () {
    return view('edit_profile');
})->name('edit_profile')->middleware('auth');








