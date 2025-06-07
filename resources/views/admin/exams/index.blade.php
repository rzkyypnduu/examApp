@extends('layouts.app')

@section('title', 'Manage Exams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-list"></i> Manage Exams</h2>
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Exam
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($exams->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Attempts</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td>
                                    <strong>{{ $exam->title }}</strong>
                                    @if($exam->description)
                                        <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                    @endif
                                </td>
                                <td><code>{{ $exam->code }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $exam->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>{{ $exam->duration }} min</td>
                                <td>{{ $exam->questions_count ?? $exam->questions->count() }}</td>
                                <td>{{ $exam->user_exams_count ?? $exam->userExams->count() }}</td>
                                <td>{{ $exam->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.exams.show', $exam) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.exams.edit', $exam) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.exams.destroy', $exam) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $exams->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Exams Found</h4>
                <p class="text-muted">Create your first exam to get started.</p>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Exam
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
