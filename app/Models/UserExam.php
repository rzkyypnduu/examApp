<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function calculateScore()
    {
        $totalQuestions = $this->exam->questions()->count();
        $correctAnswers = $this->userAnswers()->where('is_correct', true)->count();
        
        if ($totalQuestions === 0) return 0;
        
        return round(($correctAnswers / $totalQuestions) * 100, 2);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getRemainingTime()
    {
        if (!$this->started_at) return 0;
        
        $elapsed = now()->diffInMinutes($this->started_at);
        $remaining = $this->exam->duration - $elapsed;
        
        return max(0, $remaining);
    }
}
