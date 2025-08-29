<?php

namespace Database\Seeders;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $session = GameSession::create([
                'code' => 'JALSAH123',
                'video_url' => '/video.mp4',
                'status' => 'waiting',
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
                $session->questions()->attach($question->id);
            }
        });
    }
}
