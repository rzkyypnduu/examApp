<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\UserExam;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'code' => Exam::generateCode(),
            'created_by' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'Exam created successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['questions', 'userExams.user']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $exam->update($request->all());

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }

    public function joinExam(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $exam = Exam::where('code', strtoupper($request->code))->first();

        if (!$exam) {
            return back()->withErrors(['code' => 'Invalid exam code.']);
        }

        if (!$exam->isAvailable()) {
            return back()->withErrors(['code' => 'This exam is not available.']);
        }

        $userExam = UserExam::firstOrCreate([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
        ]);

        if ($userExam->isCompleted()) {
            return back()->withErrors(['code' => 'You have already completed this exam.']);
        }

        return redirect()->route('exam.take', $exam);
    }

    public function takeExam(Exam $exam)
    {
        $userExam = UserExam::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->first();

        if (!$userExam) {
            return redirect()->route('user.dashboard')
                ->withErrors(['error' => 'You are not registered for this exam.']);
        }

        if ($userExam->isCompleted()) {
            return redirect()->route('user.dashboard')
                ->withErrors(['error' => 'You have already completed this exam.']);
        }

        if ($userExam->status === 'not_started') {
            $userExam->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $questions = $exam->questions()->with('userAnswers', function($query) use ($userExam) {
            $query->where('user_exam_id', $userExam->id);
        })->get();

        return view('exam.take', compact('exam', 'userExam', 'questions'));
    }

    public function submitAnswer(Request $request, Exam $exam)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
        ]);

        $userExam = UserExam::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->first();

        if (!$userExam || $userExam->isCompleted()) {
            return response()->json(['error' => 'Invalid exam session'], 400);
        }

        $question = Question::find($request->question_id);
        $isCorrect = $question->correct_answer === $request->answer;

        UserAnswer::updateOrCreate([
            'user_exam_id' => $userExam->id,
            'question_id' => $request->question_id,
        ], [
            'user_answer' => $request->answer,
            'is_correct' => $isCorrect,
        ]);

        return response()->json(['success' => true]);
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $userExam = UserExam::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->first();

        if (!$userExam || $userExam->isCompleted()) {
            return redirect()->route('user.dashboard')
                ->withErrors(['error' => 'Invalid exam session']);
        }

        $score = $userExam->calculateScore();

        $userExam->update([
            'status' => 'completed',
            'completed_at' => now(),
            'score' => $score,
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', "Exam completed! Your score: {$score}%");
    }
}
