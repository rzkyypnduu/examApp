@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalExams }}</h4>
                        <p class="mb-0">Total Exams</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalUsers }}</h4>
                        <p class="mb-0">Total Students</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalAttempts }}</h4>
                        <p class="mb-0">Total Attempts</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ \App\Models\Exam::where('status', 'active')->count() }}</h4>
                        <p class="mb-0">Active Exams</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-play fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-clipboard-list"></i> Recent Exams</h5>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create Exam
                </a>
            </div>
            <div class="card-body">
                @if($recentExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExams as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td><code>{{ $exam->code }}</code></td>
                                        <td>
                                            <span class="badge bg-{{ $exam->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($exam->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $exam->duration }} min</td>
                                        <td>{{ $exam->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.exams.show', $exam) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.exams.edit', $exam) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No exams created yet.</p>
                        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Exam
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cogs"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Exam
                    </a>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Manage Exams
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
