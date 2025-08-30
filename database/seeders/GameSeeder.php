<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $game = Game::create([
                'name' => 'Jalsah Game',
                'description' => 'A fun quiz game.',
                'video_url' => '/video.mp4',
            ]);

            $questions = [
                [
                    'type' => 'mcq',
                    'text' => 'ما هي عاصمة ماليزيا؟',
                    'options' => ['كوالالمبور', 'جاكرتا', 'سنغافورة'],
                    'correct_answer' => '0',
                ],
                [
                    'type' => 'true_false',
                    'text' => 'الشاي الأخضر مفيد للصحة.',
                    'correct_answer' => 'true',
                ],
            ];

            foreach ($questions as $questionData) {
                $question = Question::create($questionData);
                $game->questions()->attach($question->id);
            }
        });
    }
}
