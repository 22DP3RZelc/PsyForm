<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class TestCreateController extends Controller
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
}
