<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();

        // Create sample exam
        $exam = Exam::create([
            'title' => 'Basic Mathematics Test',
            'description' => 'A basic test covering fundamental mathematics concepts.',
            'duration' => 30,
            'code' => Exam::generateCode(),
            'created_by' => $admin->id,
            'status' => 'active',
        ]);

        // Add sample questions
        Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'What is 2 + 2?',
            'question_type' => 'multiple_choice',
            'options' => ['3', '4', '5', '6'],
            'correct_answer' => '4',
            'points' => 1,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Is 10 greater than 5?',
            'question_type' => 'true_false',
            'options' => null,
            'correct_answer' => 'True',
            'points' => 1,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Explain the Pythagorean theorem.',
            'question_type' => 'essay',
            'options' => null,
            'correct_answer' => 'The Pythagorean theorem states that in a right triangle, the square of the hypotenuse equals the sum of squares of the other two sides.',
            'points' => 5,
        ]);

        // Create another exam
        $exam2 = Exam::create([
            'title' => 'Science Quiz',
            'description' => 'Test your knowledge of basic science concepts.',
            'duration' => 45,
            'code' => Exam::generateCode(),
            'created_by' => $admin->id,
            'status' => 'active',
        ]);

        Question::create([
            'exam_id' => $exam2->id,
            'question_text' => 'What is the chemical symbol for water?',
            'question_type' => 'multiple_choice',
            'options' => ['H2O', 'CO2', 'NaCl', 'O2'],
            'correct_answer' => 'H2O',
            'points' => 1,
        ]);
    }
}
