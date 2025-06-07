<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $user = Auth::user();
        $userExams = UserExam::with('exam')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.dashboard', compact('userExams'));
    }

    public function adminDashboard()
    {
        $totalExams = Exam::count();
        $totalUsers = User::student()->count();
        $totalAttempts = UserExam::count();
        $recentExams = Exam::with('creator')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalExams',
            'totalUsers', 
            'totalAttempts',
            'recentExams'
        ));
    }
}
