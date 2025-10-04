<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Game - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Create New Game</h1>
        <p class="text-gray-600 mt-1">Create a new quiz game with multiple questions.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" id="game-form">
            @csrf

            <!-- Game Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium">Step 1</span>
                    <span class="ml-3">Game Information</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Game Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter a descriptive name for your quiz"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="form-textarea w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Provide a brief description of what this quiz covers"
                                  >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional description to help players understand the quiz topic.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                            Thumbnail Image
                        </label>
                        <input type="file"
                               name="thumbnail"
                               id="thumbnail"
                               accept="image/*"
                               class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('thumbnail') border-red-500 @enderror">
                        <div id="thumbnail-preview" class="mt-2 hidden">
                            <img id="thumbnail-image" class="max-w-xs max-h-32 object-cover rounded-md border border-gray-300" alt="Game thumbnail preview">
                        </div>
                        @error('thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional thumbnail image for the game (max 2MB, JPG/PNG/GIF).</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Video URL
                        </label>
                        <input type="url"
                               name="video_url"
                               id="video_url"
                               value="{{ old('video_url') }}"
                               class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('video_url') border-red-500 @enderror"
                               placeholder="https://example.com/your-video.mp4">
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional video to show before the quiz starts.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_timed"
                                   id="is_timed"
                                   value="1"
                                   {{ old('is_timed') ? 'checked' : '' }}
                                   class="form-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable timed questions</span>
                        </label>
                        @error('is_timed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">When enabled, you can set time limits for each question (30 seconds default).</p>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium">Step 2</span>
                    <span class="ml-3">Questions</span>
                    <span class="ml-auto text-sm text-gray-500">
                        Questions: <span id="questions-count" class="font-medium">0</span>
                    </span>
                </h2>

                <div id="questions-container" class="space-y-4">
                    <!-- Questions will be added here dynamically -->
                    @if(old('questions'))
                        @foreach(old('questions') as $index => $question)
                            <div class="question-item border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="font-medium">Question {{ (int)$index + 1 }}</h4>
                                    <button type="button" class="remove-question text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Question Text <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               name="questions[{{ $index }}][text]"
                                               value="{{ $question['text'] ?? '' }}"
                                               class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter your question here"
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Question Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="questions[{{ $index }}][type]"
                                                class="question-type form-select w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="mcq" {{ ($question['type'] ?? '') === 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                                            <option value="true_false" {{ ($question['type'] ?? '') === 'true_false' ? 'selected' : '' }}>True/False</option>
                                        </select>
                                    </div>

                                    <!-- Options container will be populated by JavaScript -->
                                    <div class="options-container lg:col-span-2">
                                        <!-- Options will be added here -->
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <button type="button" id="add-question"
                        class="mt-4 bg-green-500 text-white px-6 py-3 rounded-md hover:bg-green-600 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Question
                </button>

                @if($errors->has('questions.*'))
                    <div class="mt-4 text-red-600 text-sm">
                        Please check the questions section for validation errors.
                    </div>
                @endif
            </div>

            <!-- Save Actions -->
            <div class="bg-gray-50 px-6 py-4 -mx-6 -mb-6 rounded-b-lg">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('admin.index') }}"
                       class="w-full sm:w-auto bg-gray-400 text-white px-6 py-3 rounded-md hover:bg-gray-500 transition-colors text-center">
                        Cancel
                    </a>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button type="button" id="save-draft"
                                class="w-full sm:w-auto bg-yellow-500 text-white px-6 py-3 rounded-md hover:bg-yellow-600 transition-colors">
                            Save as Draft
                        </button>
                        <button type="submit" id="save-game"
                                class="w-full sm:w-auto bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition-colors flex items-center justify-center">
                            <span class="save-text">Create Game</span>
                            <div class="save-spinner hidden ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addQuestionButton = document.getElementById('add-question');
        const questionsContainer = document.getElementById('questions-container');
        const questionsCountElement = document.getElementById('questions-count');
        let questionIndex = 0;

        // Render options UI based on question type
        function renderOptionsUI(questionIndex, questionType, existingOptions = null, correctAnswer = null) {
            let optionsHtml = '';

            if (questionType === 'mcq') {
                // Multiple Choice: 4 separate input fields
                const options = existingOptions || ['', '', '', ''];
                const optionLabels = ['Option A', 'Option B', 'Option C', 'Option D'];

                optionsHtml += '<div class="space-y-3">';
                optionsHtml += '<label class="block text-sm font-medium text-gray-700">Answer Options</label>';

                for (let i = 0; i < 4; i++) {
                    const optionValue = options[i] || '';
                    const isChecked = correctAnswer === optionValue && optionValue ? 'checked' : '';

                    optionsHtml += `
                        <div class="flex items-center gap-3">
                            <input type="radio"
                                   name="questions[${questionIndex}][correct_answer]"
                                   value="${optionValue || `option_${i}`}"
                                   class="option-radio-${i} form-radio text-blue-600"
                                   data-option-index="${i}"
                                   ${isChecked}
                                   required
                                   ${optionValue ? '' : 'disabled'}>
                            <label class="flex-1">
                                <span class="text-sm font-medium text-gray-700">${optionLabels[i]}</span>
                                <input type="text"
                                       name="questions[${questionIndex}][options][${i}]"
                                       value="${optionValue}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 option-input-${i}"
                                       placeholder="Enter option ${String.fromCharCode(65 + i)}"
                                       required>
                            </label>
                        </div>
                    `;
                }
                optionsHtml += '<p class="text-xs text-gray-500 mt-2">Select the correct answer by clicking the radio button next to it.</p>';
                optionsHtml += '</div>';
            } else if (questionType === 'true_false') {
                // True/False: 2 locked inputs with "True" and "False"
                const trueChecked = correctAnswer === 'True' ? 'checked' : '';
                const falseChecked = correctAnswer === 'False' ? 'checked' : '';

                optionsHtml += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                optionsHtml += '<label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>';

                // True option
                optionsHtml += `
                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio"
                                   name="questions[${questionIndex}][correct_answer]"
                                   value="True"
                                   class="form-radio text-blue-600 mr-3"
                                   ${trueChecked}
                                   required>
                            <div>
                                <div class="font-medium text-gray-900">True</div>
                                <input type="hidden" name="questions[${questionIndex}][options][0]" value="True">
                            </div>
                        </label>
                    </div>
                `;

                // False option
                optionsHtml += `
                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio"
                                   name="questions[${questionIndex}][correct_answer]"
                                   value="False"
                                   class="form-radio text-red-600 mr-3"
                                   ${falseChecked}
                                   required>
                            <div>
                                <div class="font-medium text-red-900">False</div>
                                <input type="hidden" name="questions[${questionIndex}][options][1]" value="False">
                            </div>
                        </label>
                    </div>
                `;

                optionsHtml += '</div>';
            }

            return optionsHtml;
        }

        // Update correct answer radio values when options change
        function updateCorrectAnswerRadios(questionIndex, questionType) {
            if (questionType === 'mcq') {
                const optionsContainer = questionsContainer.querySelector(`[data-question-index="${questionIndex}"] .options-container`);
                for (let i = 0; i < 4; i++) {
                    const optionInput = optionsContainer.querySelector(`.option-input-${i}`);
                    const optionRadio = optionsContainer.querySelector(`.option-radio-${i}`);

                    if (optionInput && optionRadio) {
                        const optionValue = optionInput.value.trim();
                        optionRadio.value = optionValue || `option_${i}`;
                        optionRadio.disabled = !optionValue;

                        // If the radio was checked but option is empty, uncheck it
                        if (!optionValue && optionRadio.checked) {
                            optionRadio.checked = false;
                        }
                    }
                }
            }
        }

        // Add new question
        addQuestionButton.addEventListener('click', function () {
            addQuestion(questionIndex, 'mcq');
            questionIndex++;
            updateQuestionsCount();
        });

        // Helper function to add a question
        function addQuestion(qIndex, questionType, questionText = '', existingOptions = null, correctAnswer = null, questionThumbnail = '', timeLimit = '') {
            const isTimed = document.getElementById('is_timed').checked;
            const timeLimitHtml = isTimed ? `
                <div class="time-limit-container">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Time Limit (seconds)
                    </label>
                    <input type="number"
                           name="questions[${qIndex}][time_limit]"
                           value="${timeLimit || 30}"
                           min="1"
                           class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="30">
                    <p class="text-xs text-gray-500 mt-1">Time allowed for this question in seconds.</p>
                </div>
            ` : '';

            const questionHtml = `
                <div class="question-item border border-gray-200 rounded-lg p-4 bg-gray-50" data-question-index="${qIndex}">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="font-medium">Question ${parseInt(qIndex) + 1}</h4>
                        <button type="button" class="remove-question text-red-500 hover:text-red-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Question Text <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="questions[${qIndex}][text]"
                                   value="${questionText}"
                                   class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your question here"
                                   required>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Question Thumbnail
                            </label>
                            <input type="file"
                                   name="questions[${qIndex}][thumbnail]"
                                   accept="image/*"
                                   data-question-index="${qIndex}"
                                   class="question-thumbnail form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="question-thumbnail-preview-${qIndex} mt-2 hidden">
                                <img class="question-thumbnail-image-${qIndex} max-w-xs max-h-32 object-cover rounded-md border border-gray-300" alt="Question thumbnail preview">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Optional thumbnail image for this question (max 2MB, JPG/PNG/GIF).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Question Type <span class="text-red-500">*</span>
                            </label>
                            <select name="questions[${qIndex}][type]"
                                    class="question-type form-select w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="mcq" ${questionType === 'mcq' ? 'selected' : ''}>Multiple Choice (4 options)</option>
                                <option value="true_false" ${questionType === 'true_false' ? 'selected' : ''}>True/False (2 options)</option>
                            </select>
                        </div>

                        ${timeLimitHtml}

                        <div class="options-container lg:col-span-2">
                            ${renderOptionsUI(qIndex, questionType, existingOptions, correctAnswer)}
                        </div>
                    </div>
                </div>
            `;

            questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
        }

        // Handle question type changes
        questionsContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('question-type')) {
                const questionItem = e.target.closest('.question-item');
                const questionIndex = questionItem.dataset.questionIndex;
                const questionType = e.target.value;
                const optionsContainer = questionItem.querySelector('.options-container');

                // Re-render options
                optionsContainer.innerHTML = renderOptionsUI(questionIndex, questionType);
            }
        });

        // Handle option input changes for MCQ (to update radio button values)
        questionsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('option-input-0') ||
                e.target.classList.contains('option-input-1') ||
                e.target.classList.contains('option-input-2') ||
                e.target.classList.contains('option-input-3')) {

                const questionItem = e.target.closest('.question-item');
                const questionIndex = questionItem.dataset.questionIndex;
                const questionType = questionItem.querySelector('.question-type').value;

                if (questionType === 'mcq') {
                    updateCorrectAnswerRadios(questionIndex, questionType);
                }
            }
        });

        // Remove question
        questionsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-question') || e.target.closest('.remove-question')) {
                e.target.closest('.question-item').remove();
                updateQuestionsCount();
                updateQuestionNumbers();
            }
        });

        // Update question count
        function updateQuestionsCount() {
            const questionCount = questionsContainer.querySelectorAll('.question-item').length;
            questionsCountElement.textContent = questionCount;
        }

        // Update question numbers after removal
        function updateQuestionNumbers() {
            const questionItems = questionsContainer.querySelectorAll('.question-item');
            questionItems.forEach((item, index) => {
                const titleElement = item.querySelector('h4');
                titleElement.textContent = `Question ${index + 1}`;
            });
        }

        // Update question indices for form submission
        function updateFormIndices() {
            const questionItems = questionsContainer.querySelectorAll('.question-item');
            questionItems.forEach((item, newIndex) => {
                const oldIndex = item.dataset.questionIndex;

                // Update question text input
                const textInput = item.querySelector('input[type="text"][placeholder*="question here"]');
                if (textInput) {
                    textInput.name = textInput.name.replace(/\[\d+\]/g, `[${newIndex}]`);
                }

                // Update question type select
                const typeSelect = item.querySelector('.question-type');
                if (typeSelect) {
                    typeSelect.name = typeSelect.name.replace(/\[\d+\]/g, `[${newIndex}]`);
                }

                // Update options and correct answer inputs for both MCQ and True/False
                const optionInputs = item.querySelectorAll('input[type="text"]');
                const radioInputs = item.querySelectorAll('input[type="radio"]');
                const hiddenInputs = item.querySelectorAll('input[type="hidden"]');

                optionInputs.forEach(input => {
                    if (input.placeholder?.includes('option')) {
                        input.name = input.name.replace(/\[\d+\]/g, `[${newIndex}]`);
                    }
                });

                radioInputs.forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/g, `[${newIndex}]`);
                });

                hiddenInputs.forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/g, `[${newIndex}]`);
                });

                item.dataset.questionIndex = newIndex;
                const titleElement = item.querySelector('h4');
                titleElement.textContent = `Question ${newIndex + 1}`;
            });
        }

        // Form submission handler
        const gameForm = document.getElementById('game-form');
        const saveButton = document.getElementById('save-game');
        const saveSpinner = document.querySelector('.save-spinner');
        const saveText = document.querySelector('.save-text');

        gameForm.addEventListener('submit', function (e) {
            saveButton.disabled = true;
            saveText.textContent = 'Creating Game...';
            saveSpinner.classList.remove('hidden');
        });

        // Initialize with existing questions if any
        const existingQuestions = questionsContainer.querySelectorAll('.question-item');
        if (existingQuestions.length > 0) {
            questionIndex = existingQuestions.length;
            updateQuestionsCount();
        }

        // Initialize options containers for existing questions
        document.querySelectorAll('.question-type').forEach(select => {
            const questionItem = select.closest('.question-item');
            const questionIndex = questionItem.dataset.questionIndex;
            const questionType = select.value;
            const optionsContainer = questionItem.querySelector('.options-container');

            if (optionsContainer && optionsContainer.children.length === 0) {
                // Get existing options from old input if available
                const questionData = @json(old('questions', []));
                const questionDataIndex = parseInt(questionIndex);
                const existingOptions = questionData[questionDataIndex]?.options ?? null;
                const correctAnswer = questionData[questionDataIndex]?.correct_answer ?? null;

                optionsContainer.innerHTML = renderOptionsUI(questionIndex, questionType, existingOptions, correctAnswer);
            }
        });

        // Game thumbnail preview
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('thumbnail-preview');
            const image = document.getElementById('thumbnail-image');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });

        // Question thumbnail previews
        questionsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('question-thumbnail')) {
                const questionIndex = e.target.dataset.questionIndex;
                const file = e.target.files[0];
                const preview = document.querySelector(`.question-thumbnail-preview-${questionIndex}`);
                const image = document.querySelector(`.question-thumbnail-image-${questionIndex}`);

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                }
            }
        });

        // Handle timed questions toggle
        document.getElementById('is_timed').addEventListener('change', function(e) {
            const isTimed = e.target.checked;
            const questionItems = document.querySelectorAll('.question-item');

            questionItems.forEach(item => {
                const questionIndex = item.dataset.questionIndex;
                let timeLimitContainer = item.querySelector('.time-limit-container');

                if (isTimed) {
                    // Add time limit input if it doesn't exist
                    if (!timeLimitContainer) {
                        const timeLimitHtml = `
                            <div class="time-limit-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Time Limit (seconds)
                                </label>
                                <input type="number"
                                       name="questions[${questionIndex}][time_limit]"
                                       value="30"
                                       min="1"
                                       class="form-input w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="30">
                                <p class="text-xs text-gray-500 mt-1">Time allowed for this question in seconds.</p>
                            </div>
                        `;
                        // Insert before options container
                        const optionsContainer = item.querySelector('.options-container');
                        optionsContainer.insertAdjacentHTML('beforebegin', timeLimitHtml);
                    }
                } else {
                    // Remove time limit input if it exists
                    if (timeLimitContainer) {
                        timeLimitContainer.remove();
                    }
                }
            });
        });
    });
</script>

</body>
</html>
