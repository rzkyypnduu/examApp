<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    public function getData(Question $question): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'correct_answer' => $question->correct_answer,
                'points' => $question->points,
                'options' => $question->options ?? [], // Pastikan selalu array
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading question data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice|array',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $data = $request->all();
        $data['exam_id'] = $exam->id;

        Question::create($data);

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'Question added successfully!');
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice|array',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $question->update($request->all());

        return redirect()->route('admin.questions.edit', $question->exam)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $exam = $question->exam;
        $question->delete();

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'Question deleted successfully!');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }
}