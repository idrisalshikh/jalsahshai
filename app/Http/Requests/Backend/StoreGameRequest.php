<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add proper authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:games,name',
            'description' => 'nullable|string|max:1000',
            'video_url' => 'nullable|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_timed' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:500',
            'questions.*.type' => 'required|in:mcq,true_false',
            // Options validation based on question type
            'questions.*.options' => 'required|array',
            'questions.*.options.*' => 'nullable|string|max:255',
            // Correct answer validation
            'questions.*.correct_answer' => 'required|string|max:255',
            'questions.*.thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'questions.*.time_limit' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questions = $this->input('questions', []);

            foreach ($questions as $index => $question) {
                $type = $question['type'] ?? '';
                $options = $question['options'] ?? [];
                $correctAnswer = $question['correct_answer'] ?? '';

                if ($type === 'mcq') {
                    // MCQ requires exactly 4 non-empty options
                    $nonEmptyOptions = array_filter($options, function($option) {
                        return !empty(trim($option));
                    });

                    if (count($nonEmptyOptions) !== 4) {
                        $validator->errors()->add("questions.{$index}.options", 'Multiple choice questions must have exactly 4 options.');
                    }

                    // Correct answer must match one of the options
                    if (!empty($correctAnswer) && !in_array($correctAnswer, $nonEmptyOptions)) {
                        $validator->errors()->add("questions.{$index}.correct_answer", 'The correct answer must match one of the provided options.');
                    }

                } elseif ($type === 'true_false') {
                    // True/False requires exactly 2 options: True and False in that order
                    if (count($options) !== 2 ||
                        !isset($options[0]) || !isset($options[1]) ||
                        trim($options[0]) !== 'True' || trim($options[1]) !== 'False') {
                        $validator->errors()->add("questions.{$index}.options", 'True/False questions must have exactly 2 options: True and False.');
                    }

                    // Correct answer must be either True or False
                    if (!in_array($correctAnswer, ['True', 'False'])) {
                        $validator->errors()->add("questions.{$index}.correct_answer", 'For True/False questions, the correct answer must be either "True" or "False".');
                    }
                }
            }
        });
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The game name is required.',
            'name.unique' => 'A game with this name already exists. Please choose a different name.',
            'name.max' => 'The game name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'video_url.url' => 'Please provide a valid URL for the video.',
            'video_url.max' => 'The video URL cannot exceed 500 characters.',
            'questions.min' => 'Please add at least one question to the game.',
            'questions.*.text.required' => 'Each question must have text.',
            'questions.*.text.max' => 'Question text cannot exceed 500 characters.',
            'questions.*.type.required' => 'Each question must have a type.',
            'questions.*.type.in' => 'Question type must be either multiple choice (mcq) or true/false.',
            'questions.*.options.max' => 'Question options cannot exceed 1000 characters.',
            'questions.*.correct_answer.required' => 'Each question must have a correct answer.',
            'questions.*.correct_answer.max' => 'Correct answer cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'game name',
            'description' => 'game description',
            'video_url' => 'video URL',
            'questions.*.text' => 'question text',
            'questions.*.type' => 'question type',
            'questions.*.options' => 'question options',
            'questions.*.correct_answer' => 'correct answer',
        ];
    }
}
