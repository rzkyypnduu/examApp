@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-keyboard"></i> Join Exam</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('exam.join') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Exam Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" placeholder="Enter 6-digit code" maxlength="6" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-play"></i> Join Exam
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-user"></i> Profile</h5>
                <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                @if(Auth::user()->phone)
                    <p><strong>Phone:</strong> {{ Auth::user()->phone }}</p>
                @endif
                @if(Auth::user()->birth_date)
                    <p><strong>Birth Date:</strong> {{ Auth::user()->birth_date->format('d M Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Exam History</h5>
            </div>
            <div class="card-body">
                @if($userExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userExams as $userExam)
                                    <tr>
                                        <td>{{ $userExam->exam->title }}</td>
                                        <td>
                                            @if($userExam->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($userExam->status === 'in_progress')
                                                <span class="badge bg-warning">In Progress</span>
                                            @else
                                                <span class="badge bg-secondary">Not Started</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($userExam->score !== null)
                                                {{ $userExam->score }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $userExam->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            @if($userExam->status === 'in_progress')
                                                <a href="{{ route('exam.take', $userExam->exam) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-play"></i> Continue
                                                </a>
                                            @elseif($userExam->status === 'not_started')
                                                <a href="{{ route('exam.take', $userExam->exam) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i> Start
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $userExams->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No exam history yet. Join an exam to get started!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
