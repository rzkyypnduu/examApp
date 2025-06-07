<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'options' => 'required_if:question_type,multiple_choice|array',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $exam->questions()->create($request->all());

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

        return redirect()->route('admin.exams.show', $question->exam)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $exam = $question->exam;
        $question->delete();

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'Question deleted successfully!');
    }
}
