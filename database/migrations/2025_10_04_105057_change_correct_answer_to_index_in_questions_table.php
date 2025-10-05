<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert existing text answers to indices
        $questions = DB::table('questions')->get();

        foreach ($questions as $question) {
            $options = json_decode($question->options, true);
            $correctAnswer = $question->correct_answer;

            if ($question->type === 'mcq') {
                $index = array_search($correctAnswer, $options, true);
                DB::table('questions')->where('id', $question->id)->update(['correct_answer' => $index !== false ? $index : 0]);
            } elseif ($question->type === 'true_false') {
                $index = ($correctAnswer === 'True') ? 0 : 1;
                DB::table('questions')->where('id', $question->id)->update(['correct_answer' => $index]);
            }
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->integer('correct_answer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to text
        $questions = DB::table('questions')->get();

        foreach ($questions as $question) {
            $options = json_decode($question->options, true);

            if ($question->type === 'mcq') {
                $textAnswer = isset($options[$question->correct_answer]) ? $options[$question->correct_answer] : '';
                DB::table('questions')->where('id', $question->id)->update(['correct_answer' => $textAnswer]);
            } elseif ($question->type === 'true_false') {
                $textAnswer = $question->correct_answer == 0 ? 'True' : 'False';
                DB::table('questions')->where('id', $question->id)->update(['correct_answer' => $textAnswer]);
            }
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->string('correct_answer')->change();
        });
    }
};
