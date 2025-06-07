@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-edit"></i> Edit Question</h4>
                <a href="{{ route('admin.exams.show', $question->exam) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Exam
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Exam:</strong> {{ $question->exam->title }}
                </div>

                <form method="POST" action="{{ route('admin.questions.update', $question) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question</label>
                        <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                  id="question_text" name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <select class="form-select @error('question_type') is-invalid @enderror" 
                                id="question_type" name="question_type" required onchange="toggleOptionsSection()">
                            <option value="multiple_choice" {{ old('question_type', $question->question_type) === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('question_type', $question->question_type) === 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="essay" {{ old('question_type', $question->question_type) === 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                        @error('question_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="optionsSection" style="{{ $question->question_type === 'multiple_choice' ? 'display: block;' : 'display: none;' }}">
                        <label class="form-label">Options</label>
                        <div id="optionsList">
                            @if($question->options)
                                @foreach($question->options as $index => $option)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="options[]" 
                                               value="{{ old('options.' . $index, $option) }}" 
                                               placeholder="Option {{ $index + 1 }}">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                        @error('options')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                               id="correct_answer" name="correct_answer" 
                               value="{{ old('correct_answer', $question->correct_answer) }}" required>
                        <div class="form-text">
                            For Multiple Choice: Enter the exact option text<br>
                            For True/False: Enter "True" or "False"<br>
                            For Essay: Enter the model answer or key points
                        </div>
                        @error('correct_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="points" class="form-label">Points</label>
                        <input type="number" class="form-control @error('points') is-invalid @enderror" 
                               id="points" name="points" value="{{ old('points', $question->points) }}" min="1" required>
                        @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.exams.show', $question->exam) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-eye"></i> Question Preview</h5>
            </div>
            <div class="card-body">
                <div id="questionPreview">
                    <h6>{{ $question->question_text }}</h6>
                    
                    @if($question->question_type === 'multiple_choice' && $question->options)
                        @foreach($question->options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" disabled
                                       {{ $option === $question->correct_answer ? 'checked' : '' }}>
                                <label class="form-check-label {{ $option === $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    @elseif($question->question_type === 'true_false')
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled
                                   {{ $question->correct_answer === 'True' ? 'checked' : '' }}>
                            <label class="form-check-label {{ $question->correct_answer === 'True' ? 'text-success fw-bold' : '' }}">
                                True
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled
                                   {{ $question->correct_answer === 'False' ? 'checked' : '' }}>
                            <label class="form-check-label {{ $question->correct_answer === 'False' ? 'text-success fw-bold' : '' }}">
                                False
                            </label>
                        </div>
                    @else
                        <textarea class="form-control" rows="3" disabled placeholder="Student will type their answer here..."></textarea>
                        <small class="text-muted mt-2">Model Answer: {{ $question->correct_answer }}</small>
                    @endif
                    
                    <div class="mt-2">
                        <small class="text-muted">Points: {{ $question->points }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleOptionsSection() {
    const questionType = document.getElementById('question_type').value;
    const optionsSection = document.getElementById('optionsSection');
    
    if (questionType === 'multiple_choice') {
        optionsSection.style.display = 'block';
    } else {
        optionsSection.style.display = 'none';
    }
    
    updatePreview();
}

function addOption() {
    const optionsList = document.getElementById('optionsList');
    const optionCount = optionsList.children.length + 1;
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}" onchange="updatePreview()">
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    optionsList.appendChild(optionDiv);
    updatePreview();
}

function removeOption(button) {
    button.parentElement.remove();
    updatePreview();
}

function updatePreview() {
    const questionText = document.getElementById('question_text').value;
    const questionType = document.getElementById('question_type').value;
    const correctAnswer = document.getElementById('correct_answer').value;
    const points = document.getElementById('points').value;
    
    let previewHtml = `<h6>${questionText || 'Question text will appear here...'}</h6>`;
    
    if (questionType === 'multiple_choice') {
        const options = Array.from(document.querySelectorAll('input[name="options[]"]')).map(input => input.value).filter(val => val);
        options.forEach(option => {
            const isCorrect = option === correctAnswer;
            previewHtml += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" disabled ${isCorrect ? 'checked' : ''}>
                    <label class="form-check-label ${isCorrect ? 'text-success fw-bold' : ''}">${option}</label>
                </div>
            `;
        });
    } else if (questionType === 'true_false') {
        previewHtml += `
            <div class="form-check">
                <input class="form-check-input" type="radio" disabled ${correctAnswer === 'True' ? 'checked' : ''}>
                <label class="form-check-label ${correctAnswer === 'True' ? 'text-success fw-bold' : ''}">True</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" disabled ${correctAnswer === 'False' ? 'checked' : ''}>
                <label class="form-check-label ${correctAnswer === 'False' ? 'text-success fw-bold' : ''}">False</label>
            </div>
        `;
    } else {
        previewHtml += `
            <textarea class="form-control" rows="3" disabled placeholder="Student will type their answer here..."></textarea>
            <small class="text-muted mt-2">Model Answer: ${correctAnswer}</small>
        `;
    }
    
    previewHtml += `<div class="mt-2"><small class="text-muted">Points: ${points || 1}</small></div>`;
    
    document.getElementById('questionPreview').innerHTML = previewHtml;
}

// Add event listeners for real-time preview updates
document.getElementById('question_text').addEventListener('input', updatePreview);
document.getElementById('correct_answer').addEventListener('input', updatePreview);
document.getElementById('points').addEventListener('input', updatePreview);

// Initial preview update
updatePreview();
</script>
@endsection
