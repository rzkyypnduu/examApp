@extends('layouts.app')

@section('title', $exam->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Exam Details -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-clipboard-list"></i> {{ $exam->title }}</h4>
                <div>
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this exam?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($exam->description)
                    <p class="text-muted">{{ $exam->description }}</p>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Exam Code:</strong> <code class="fs-5">{{ $exam->code }}</code>
                    </div>
                    <div class="col-md-6">
                        <strong>Duration:</strong> {{ $exam->duration }} minutes
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $exam->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Questions:</strong> {{ $exam->questions->count() }}
                    </div>
                </div>

                @if($exam->start_time || $exam->end_time)
                    <div class="row mb-3">
                        @if($exam->start_time)
                            <div class="col-md-6">
                                <strong>Start Time:</strong> {{ $exam->start_time->format('d M Y H:i') }}
                            </div>
                        @endif
                        @if($exam->end_time)
                            <div class="col-md-6">
                                <strong>End Time:</strong> {{ $exam->end_time->format('d M Y H:i') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Questions Section -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-question-circle"></i> Questions</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
            <div class="card-body">
                @if($exam->questions->count() > 0)
                    @foreach($exam->questions as $index => $question)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6>Question {{ $index + 1 }}</h6>
                                    <div>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="editQuestion({{ $question->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.questions.destroy', $question) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p>{{ $question->question_text }}</p>
                                
                                @if($question->question_type === 'multiple_choice' && $question->options)
                                    <div class="ms-3">
                                        @foreach($question->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled
                                                       {{ $option === $question->correct_answer ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $option === $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p><strong>Correct Answer:</strong> {{ $question->correct_answer }}</p>
                                @endif
                                
                                <small class="text-muted">Points: {{ $question->points }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No questions added yet.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="fas fa-plus"></i> Add First Question
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Exam Statistics -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $exam->userExams->count() }}</h4>
                        <small>Total Attempts</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $exam->userExams->where('status', 'completed')->count() }}</h4>
                        <small>Completed</small>
                    </div>
                </div>
                <hr>
                @if($exam->userExams->where('status', 'completed')->count() > 0)
                    <p><strong>Average Score:</strong> 
                        {{ number_format($exam->userExams->where('status', 'completed')->avg('score'), 1) }}%
                    </p>
                @endif
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Recent Attempts</h5>
            </div>
            <div class="card-body">
                @if($exam->userExams->count() > 0)
                    @foreach($exam->userExams->take(5) as $userExam)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $userExam->user->name }}</strong><br>
                                <small class="text-muted">{{ $userExam->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <div class="text-end">
                                @if($userExam->status === 'completed')
                                    <span class="badge bg-success">{{ $userExam->score }}%</span>
                                @elseif($userExam->status === 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-secondary">Not Started</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">No attempts yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.questions.store', $exam) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <select class="form-select" id="question_type" name="question_type" required onchange="toggleOptions()">
                            <option value="">Select Type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div id="optionsSection" style="display: none;">
                        <label class="form-label">Options</label>
                        <div id="optionsList">
                            <!-- Initial Option Fields -->
                            @for($i = 1; $i <= 2; $i++)
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="options[]" placeholder="Option {{ $i }}">
                                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endfor
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" required>
                        <div class="form-text">For True/False questions, enter "True" or "False"</div>
                    </div>

                    <div class="mb-3">
                        <label for="points" class="form-label">Points</label>
                        <input type="number" class="form-control" id="points" name="points" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleOptions() {
    const questionType = document.getElementById('question_type').value;
    const optionsSection = document.getElementById('optionsSection');
    optionsSection.style.display = (questionType === 'multiple_choice') ? 'block' : 'none';
}

function addOption() {
    const optionsList = document.getElementById('optionsList');
    const optionCount = optionsList.children.length + 1;

    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}">
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    optionsList.appendChild(optionDiv);
}

function removeOption(button) {
    button.parentElement.remove();
}

function editQuestion(questionId) {
    // Show loading state
     window.location.href = `/admin/questions/${questionId}/edit`;
    console.log('Loading question data for ID:', questionId);
    
    fetch(`/admin/questions/${questionId}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Question data received:', data);
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to load question data');
            }
            
            // Populate form fields
            document.getElementById('edit_question_text').value = data.question_text || '';
            document.getElementById('edit_question_type').value = data.question_type || '';
            document.getElementById('edit_correct_answer').value = data.correct_answer || '';
            document.getElementById('edit_points').value = data.points || 1;
            
            // Set form action
            const form = document.getElementById('editQuestionForm');
            if (form) {
                form.action = `/admin/questions/${questionId}`;
            }

            // Handle options
            const editOptionsList = document.getElementById('editOptionsList');
            if (editOptionsList) {
                editOptionsList.innerHTML = '';

                if (data.question_type === 'multiple_choice' && data.options && Array.isArray(data.options)) {
                    data.options.forEach((option, index) => {
                        const optionDiv = document.createElement('div');
                        optionDiv.className = 'input-group mb-2';
                        optionDiv.innerHTML = `
                            <input type="text" class="form-control" name="options[]" 
                                   value="${escapeHtml(option)}" placeholder="Option ${index + 1}">
                            <button type="button" class="btn btn-outline-danger" onclick="removeEditOption(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        editOptionsList.appendChild(optionDiv);
                    });
                }
            }

            // Toggle options visibility
            if (typeof toggleEditOptions === 'function') {
                toggleEditOptions();
            }

            // Show modal
            const modalElement = document.getElementById('editQuestionModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Edit modal not found');
                alert('Edit modal not found. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Error fetching question data:', error);
            alert('Error loading question data: ' + error.message);
        });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Function to toggle edit options visibility
function toggleEditOptions() {
    const questionType = document.getElementById('edit_question_type')?.value;
    const optionsSection = document.getElementById('editOptionsSection');
    
    if (optionsSection) {
        if (questionType === 'multiple_choice') {
            optionsSection.style.display = 'block';
        } else {
            optionsSection.style.display = 'none';
        }
    }
}

// Function to remove edit option
function removeEditOption(button) {
    if (button && button.parentElement) {
        button.parentElement.remove();
    }
}

// Function to add edit option
function addEditOption() {
    const optionsList = document.getElementById('editOptionsList');
    if (!optionsList) return;
    
    const optionCount = optionsList.children.length + 1;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}">
        <button type="button" class="btn btn-outline-danger" onclick="removeEditOption(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    optionsList.appendChild(optionDiv);
}
</script>
@endsection
