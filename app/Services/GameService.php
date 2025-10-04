<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GameService
 *
 * Handles game creation and update operations with proper validation and error handling.
 */
class GameService
{
    /**
     * Create a new game with questions
     *
     * @param array $validatedData
     * @return Game
     * @throws \Exception
     */
    public function createGame(array $validatedData): Game
    {
        try {
            return DB::transaction(function () use ($validatedData) {
                $game = Game::create([
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'] ?? null,
                    'video_url' => $validatedData['video_url'] ?? null,
                    'thumbnail' => $validatedData['thumbnail'] ?? null,
                    'is_timed' => $validatedData['is_timed'] ?? false,
                    'created_by' => $validatedData['created_by'] ?? null,
                ]);

                if (isset($validatedData['questions']) && count($validatedData['questions']) > 0) {
                    foreach ($validatedData['questions'] as $questionData) {
                        // Convert options from array to comma-separated string for storage
                        $options = null;
                        if (isset($questionData['options'])) {
                            if (is_array($questionData['options'])) {
                                // Filter out empty options
                                $filteredOptions = array_filter($questionData['options'], function($option) {
                                    return trim($option) !== '';
                                });
                                $options = implode(',', $filteredOptions);
                            } else {
                                $options = $questionData['options'];
                            }
                        }

                        $question = Question::create([
                            'text' => $questionData['text'],
                            'type' => $questionData['type'],
                            'options' => $options,
                            'correct_answer' => $questionData['correct_answer'],
                            'thumbnail' => $questionData['thumbnail'] ?? null,
                            'time_limit' => $questionData['time_limit'] ?? null,
                        ]);
                        $game->questions()->attach($question->id);
                    }
                }

                return $game;
            });
        } catch (\Exception $e) {
            Log::error('Game creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $validatedData
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing game with questions
     *
     * @param Game $game
     * @param array $validatedData
     * @return Game
     * @throws \Exception
     */
    public function updateGame(Game $game, array $validatedData): Game
    {
        try {
            return DB::transaction(function () use ($game, $validatedData) {
                $game->update([
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'] ?? null,
                    'video_url' => $validatedData['video_url'] ?? null,
                    'thumbnail' => $validatedData['thumbnail'] ?? null,
                    'is_timed' => $validatedData['is_timed'] ?? false,
                    'updated_by' => $validatedData['updated_by'] ?? null,
                ]);

                $questionIds = [];
                if (isset($validatedData['questions']) && count($validatedData['questions']) > 0) {
                    foreach ($validatedData['questions'] as $questionData) {
                        // Convert options from array to comma-separated string for storage
                        $options = null;
                        if (isset($questionData['options'])) {
                            if (is_array($questionData['options'])) {
                                // Filter out empty options
                                $filteredOptions = array_filter($questionData['options'], function($option) {
                                    return trim($option) !== '';
                                });
                                $options = implode(',', $filteredOptions);
                            } else {
                                $options = $questionData['options'];
                            }
                        }

                        if (isset($questionData['id'])) {
                            $question = Question::find($questionData['id']);
                            if ($question) {
                                $question->update([
                                    'text' => $questionData['text'],
                                    'type' => $questionData['type'],
                                    'options' => $options,
                                    'correct_answer' => $questionData['correct_answer'],
                                    'thumbnail' => $questionData['thumbnail'] ?? null,
                                    'time_limit' => $questionData['time_limit'] ?? null,
                                ]);
                            }
                        } else {
                            $question = Question::create([
                                'text' => $questionData['text'],
                                'type' => $questionData['type'],
                                'options' => $options,
                                'correct_answer' => $questionData['correct_answer'],
                                'thumbnail' => $questionData['thumbnail'] ?? null,
                                'time_limit' => $questionData['time_limit'] ?? null,
                            ]);
                        }
                        $questionIds[] = $question->id ?? $question->getKey();
                    }
                }

                $game->questions()->sync($questionIds);

                return $game->fresh(['questions']);
            });
        } catch (\Exception $e) {
            Log::error('Game update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'game_id' => $game->id,
                'data' => $validatedData
            ]);
            throw $e;
        }
    }

    /**
     * Check if a game has active sessions
     *
     * @param Game $game
     * @return bool
     */
    public function gameHasActiveSessions(Game $game): bool
    {
        return $game->hasActiveSessions();
    }
}
