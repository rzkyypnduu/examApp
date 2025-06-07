@extends('layouts.app')

@section('title', 'Take Exam: ' . $exam->title)

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>{{ $exam->title }}</h4>
                <div id="timer" class="badge bg-warning fs-6">
                    <i class="fas fa-clock"></i> <span id="timeRemaining">{{ $userExam->getRemainingTime() }}:00</span>
                </div>
            </div>
            <div class="card-body">
                <form id="examForm">
                    @csrf
                    @foreach($questions as $index => $question)
                        <div class="question-container mb-4 p-3 border rounded" data-question="{{ $question->id }}">
                            <h5>Question {{ $index + 1 }}</h5>
                            <p>{{ $question->question_text }}</p>
                            
                            @if($question->question_type === 'multiple_choice')
                                @foreach($question->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="question_{{ $question->id }}" 
                                               value="{{ $option }}" 
                                               id="q{{ $question->id }}_{{ $loop->index }}"
                                               {{ $question->userAnswers->first()?->user_answer === $option ? 'checked' : '' }}
                                               onchange="saveAnswer({{ $question->id }}, this.value)">
                                        <label class="form-check-label" for="q{{ $question->id }}_{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->question_type === 'true_false')
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                           name="question_{{ $question->id }}" 
                                           value="True" 
                                           id="q{{ $question->id }}_true"
                                           {{ $question->userAnswers->first()?->user_answer === 'True' ? 'checked' : '' }}
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                    <label class="form-check-label" for="q{{ $question->id }}_true">True</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                           name="question_{{ $question->id }}" 
                                           value="False" 
                                           id="q{{ $question->id }}_false"
                                           {{ $question->userAnswers->first()?->user_answer === 'False' ? 'checked' : '' }}
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                    <label class="form-check-label" for="q{{ $question->id }}_false">False</label>
                                </div>
                            @else
                                <textarea class="form-control" 
                                          name="question_{{ $question->id }}" 
                                          rows="4" 
                                          placeholder="Enter your answer here..."
                                          onchange="saveAnswer({{ $question->id }}, this.value)">{{ $question->userAnswers->first()?->user_answer ?? '' }}</textarea>
                            @endif
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="card-footer text-center">
                <button type="button" class="btn btn-success btn-lg" onclick="submitExam()">
                    <i class="fas fa-paper-plane"></i> Submit Exam
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Question Navigator</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($questions as $index => $question)
                        <div class="col-4 mb-2">
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm w-100 question-nav" 
                                    data-question="{{ $question->id }}"
                                    onclick="scrollToQuestion({{ $question->id }})">
                                {{ $index + 1 }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Exam Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Duration:</strong> {{ $exam->duration }} minutes</p>
                <p><strong>Total Questions:</strong> {{ $questions->count() }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-warning">In Progress</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit your exam? You won't be able to make changes after submission.</p>
                <p><strong>Answered Questions:</strong> <span id="answeredCount">0</span> / {{ $questions->count() }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('exam.submit', $exam) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Submit Exam</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let timeRemaining = {{ $userExam->getRemainingTime() * 60 }}; // Convert to seconds

// Timer functionality
function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    
    document.getElementById('timeRemaining').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeRemaining <= 0) {
        alert('Time is up! Your exam will be submitted automatically.');
        document.querySelector('#submitModal form').submit();
        return;
    }
    
    timeRemaining--;
}

// Start timer
setInterval(updateTimer, 1000);

// Save answer function
function saveAnswer(questionId, answer) {
    fetch(`{{ route('exam.answer', $exam) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mark question as answered
            const navButton = document.querySelector(`[data-question="${questionId}"]`);
            navButton.classList.remove('btn-outline-primary');
            navButton.classList.add('btn-success');
            
            updateAnsweredCount();
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Scroll to question
function scrollToQuestion(questionId) {
    const questionElement = document.querySelector(`[data-question="${questionId}"]`);
    questionElement.scrollIntoView({ behavior: 'smooth' });
}

// Submit exam
function submitExam() {
    updateAnsweredCount();
    const modal = new bootstrap.Modal(document.getElementById('submitModal'));
    modal.show();
}

// Update answered count
function updateAnsweredCount() {
    const answeredButtons = document.querySelectorAll('.question-nav.btn-success');
    document.getElementById('answeredCount').textContent = answeredButtons.length;
}

// Initialize answered count on page load
document.addEventListener('DOMContentLoaded', function() {
    // Mark already answered questions
    @foreach($questions as $question)
        @if($question->userAnswers->first())
            const navButton{{ $question->id }} = document.querySelector(`[data-question="{{ $question->id }}"]`);
            navButton{{ $question->id }}.classList.remove('btn-outline-primary');
            navButton{{ $question->id }}.classList.add('btn-success');
        @endif
    @endforeach
    
    updateAnsweredCount();
});

// Prevent accidental page refresh
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>
@endsection
