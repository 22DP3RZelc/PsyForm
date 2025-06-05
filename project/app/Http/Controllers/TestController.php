<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function create()
    {
        $questions = Question::all();
        return view('test_create', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|string',
            'questions.*.text' => 'required|string',
        ]);

        $test = Test::create([
            'name' => $request->name,
            'creator_id' => Auth::id(),
        ]);

        foreach ($request->questions as $q) {
            // If question has id, link existing; else, create new
            if (!empty($q['id'])) {
                Question::where('id', $q['id'])->update(['test_id' => $test->id]);
            } else {
                Question::create([
                    'text' => $q['text'],
                    'type' => $q['type'],
                    'test_id' => $test->id,
                ]);
            }
        }

        return redirect()->route('tests.create')->with('success', 'Test created successfully!');
    }

    public function index()
    {
        // Show all tests (customize this logic as needed for permissions)
        $tests = \App\Models\Test::all();
        return view('tests.index', compact('tests'));
    }

    public function all()
    {
        $tests = \App\Models\Test::all();
        return view('tests.all', compact('tests'));
    }

    // API: Get all tests with creator email (join tests.creator_id = users.id)
    public function allWithUsers()
    {
        $tests = DB::table('tests')
            ->leftJoin('users', 'tests.creator_id', '=', 'users.id')
            ->select(
                'tests.id',
                'tests.name',
                'tests.created_at',
                'tests.creator_id',
                'users.email as creator_email'
            )
            ->orderBy('tests.created_at', 'desc')
            ->get();
        return response()->json($tests);
    }

    // API: Get a test with its questions
    public function show($testId)
    {
        $test = \App\Models\Test::with('questions')->findOrFail($testId);
        return response()->json($test);
    }

    public function saveUserAnswers(Request $request)
    {
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
    }

    public function savePolarChart(Request $request)
    {
        $userId = Auth::id();
        $testId = $request->input('test_id');
        $answers = $request->input('answers', []);

        $labels = [
            "Positive Affect", "Positive Activation", "Activation", "Negative Activation",
            "Negative Affect", "Negative Deactivation", "Deactivation", "Positive Deactivation"
        ];
        $values = array_values($answers);
        $normalized = array_map(fn($v) => floatval($v) / 9, $values);

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

        DB::table('results')->insert([
            'user_id' => $userId,
            'test_id' => $testId,
            'calc_result' => $chartUrl,
        ]);

        return response()->json([
            'chart_url' => $chartUrl
        ]);
    }
}
