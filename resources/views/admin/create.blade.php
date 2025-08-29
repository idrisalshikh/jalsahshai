<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Session - Jalsah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Create New Game Session</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('admin.sessions.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700">Session Code</label>
                <input type="text" name="code" id="code" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                <input type="text" name="video_url" id="video_url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <h3 class="text-xl font-semibold mb-4 mt-6">Questions</h3>
            <div id="questions-container">
                <!-- Questions will be added here dynamically -->
            </div>
            <button type="button" id="add-question" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Add Question</button>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('admin.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500">Cancel</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addQuestionButton = document.getElementById('add-question');
        const questionsContainer = document.getElementById('questions-container');
        let questionIndex = 0;

        addQuestionButton.addEventListener('click', function () {
            const questionHtml = `
                <div class="border p-4 rounded-md mb-4">
                    <div class="flex justify-end">
                        <button type="button" class="text-red-500 hover:text-red-700 remove-question">Remove</button>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" name="questions[${questionIndex}][text]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="questions[${questionIndex}][type]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm question-type">
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>
                    <div class="mb-2 options-container">
                        <label class="block text-sm font-medium text-gray-700">Options (comma-separated)</label>
                        <input type="text" name="questions[${questionIndex}][options]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                        <input type="text" name="questions[${questionIndex}][correct_answer]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                        <p class="text-xs text-gray-500">For MCQ, provide the index (0-based). For True/False, use 'true' or 'false'.</p>
                    </div>
                </div>
            `;
            questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
            questionIndex++;
        });

        questionsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-question')) {
                e.target.closest('.border').remove();
            }
        });

        questionsContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('question-type')) {
                const optionsContainer = e.target.closest('.border').querySelector('.options-container');
                if (e.target.value === 'true_false') {
                    optionsContainer.style.display = 'none';
                } else {
                    optionsContainer.style.display = 'block';
                }
            }
        });
    });
</script>

</body>
</html>
