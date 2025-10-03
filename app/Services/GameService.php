<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class GameService
{
    public function createGame(array $validatedData): Game
    {
        return DB::transaction(function () use ($validatedData) {
            $game = Game::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'video_url' => $validatedData['video_url'],
            ]);

            if (isset($validatedData['questions'])) {
                foreach ($validatedData['questions'] as $questionData) {
                    $question = Question::create([
                        'text' => $questionData['text'],
                        'type' => $questionData['type'],
                        'options' => isset($questionData['options']) ? explode(',', $questionData['options']) : null,
                        'correct_answer' => $questionData['correct_answer'],
                    ]);
                    $game->questions()->attach($question->id);
                }
            }

            return $game;
        });
    }

    public function updateGame(Game $game, array $validatedData): Game
    {
        return DB::transaction(function () use ($game, $validatedData) {
            $game->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'video_url' => $validatedData['video_url'],
            ]);

            $questionIds = [];
            if (isset($validatedData['questions'])) {
                foreach ($validatedData['questions'] as $questionData) {
                    $options = isset($questionData['options']) ? explode(',', $questionData['options']) : null;
                    if (isset($questionData['id'])) {
                        $question = Question::find($questionData['id']);
                        if ($question) {
                            $question->update([
                                'text' => $questionData['text'],
                                'type' => $questionData['type'],
                                'options' => $options,
                                'correct_answer' => $questionData['correct_answer'],
                            ]);
                        }
                    } else {
                        $question = Question::create([
                            'game_id' => $game->id,
                            'text' => $questionData['text'],
                            'type' => $questionData['type'],
                            'options' => $options,
                            'correct_answer' => $questionData['correct_answer'],
                        ]);
                    }
                    $questionIds[] = $question->id;
                }
            }

            $game->questions()->sync($questionIds);

            return $game;
        });
    }
}
