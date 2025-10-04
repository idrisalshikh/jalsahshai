<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\GameInUseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreGameRequest;
use App\Http\Requests\Backend\UpdateGameRequest;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * AdminController
 * 
 * Handles backend game management operations including CRUD operations,
 * pagination, filtering, sorting, and soft deletes with audit trail.
 */
class AdminController extends Controller
{
    /**
     * Process file uploads and return file paths
     *
     * @param mixed $file
     * @param string $directory
     * @return string|null
     */
    private function processFileUpload($file, $directory = 'thumbnails')
    {
        if (!$file || !is_file($file)) {
            return null;
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        return $path ? ( $path) : null;
    }

    /**
     * Display a paginated list of games with search and sorting capabilities
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $perPage = $request->input('per_page', 15);

            $games = Game::with(['questions', 'creator', 'updater'])
                ->search($search)
                ->sortBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->withQueryString();

            return view('backend.dashboard', compact('games', 'search', 'sortBy', 'sortDirection'));
        } catch (\Exception $e) {
            Log::error('Error loading games list: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.index')
                ->with('error', 'An error occurred while loading the games list. Please try again.');
        }
    }

    /**
     * Show the form for creating a new game
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.games.create');
    }

    /**
     * Store a newly created game in storage
     *
     * @param StoreGameRequest $request
     * @param GameService $gameService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreGameRequest $request, GameService $gameService)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['created_by'] = Auth::id();

            // Process game thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $validatedData['thumbnail'] = $this->processFileUpload($request->file('thumbnail'), 'game-thumbnails');
            }

            // Process question thumbnails and set time limits for timed questions
            if (isset($validatedData['questions'])) {
                foreach ($validatedData['questions'] as $index => &$questionData) {
                    if ($request->hasFile("questions.{$index}.thumbnail")) {
                        $questionData['thumbnail'] = $this->processFileUpload($request->file("questions.{$index}.thumbnail"), 'question-thumbnails');
                    }
                }
            }

            $game = $gameService->createGame($validatedData);

            Log::info('Game created successfully', [
                'game_id' => $game->id,
                'game_name' => $game->name,
                'created_by' => Auth::id()
            ]);

            return redirect()
                ->route('admin.index')
                ->with('success', 'Game "' . $game->name . '" created successfully with ' . $game->questions()->count() . ' question(s).');
        } catch (\Exception $e) {
            Log::error('Error creating game: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'data' => $request->except(['questions'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create game. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified game
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $game = Game::with('questions')->findOrFail($id);
            return view('backend.games.edit', compact('game'));
        } catch (\Exception $e) {
            Log::error('Error loading game for edit: ' . $e->getMessage(), [
                'exception' => $e,
                'game_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.index')
                ->with('error', 'Game not found or could not be loaded.');
        }
    }

    /**
     * Update the specified game in storage
     *
     * @param UpdateGameRequest $request
     * @param int $id
     * @param GameService $gameService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateGameRequest $request, $id, GameService $gameService)
    {
        try {
            $game = Game::findOrFail($id);
            $validatedData = $request->validated();
            $validatedData['updated_by'] = Auth::id();

            // Process game thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Store old thumbnail path for cleanup after successful upload
                $oldGameThumbnail = $game->thumbnail;

                // Upload new thumbnail
                $newThumbnailPath = $this->processFileUpload($request->file('thumbnail'), 'game-thumbnails');

                if ($newThumbnailPath) {
                    $validatedData['thumbnail'] = $newThumbnailPath;

                    // Delete old thumbnail if it exists and is different from new one
                    if ($oldGameThumbnail && $oldGameThumbnail !== $newThumbnailPath && Storage::disk('public')->exists($oldGameThumbnail)) {
                        Storage::disk('public')->delete($oldGameThumbnail);
                    }
                }
            } else {
                // No new thumbnail uploaded, preserve existing one
                $validatedData['thumbnail'] = $game->thumbnail;
            }

            // Process question thumbnails and set time limits for timed questions
            if (isset($validatedData['questions'])) {
                foreach ($validatedData['questions'] as $index => &$questionData) {
                    if ($request->hasFile("questions.{$index}.thumbnail")) {
                        // Store old thumbnail path for cleanup after successful upload
                        $oldQuestionThumbnail = null;
                        if (isset($questionData['id'])) {
                            $existingQuestion = \App\Models\Question::find($questionData['id']);
                            $oldQuestionThumbnail = $existingQuestion ? $existingQuestion->thumbnail : null;
                        }

                        // Upload new thumbnail
                        $newThumbnailPath = $this->processFileUpload($request->file("questions.{$index}.thumbnail"), 'question-thumbnails');

                        if ($newThumbnailPath) {
                            $questionData['thumbnail'] = $newThumbnailPath;

                            // Delete old thumbnail if it exists and is different from new one
                            if ($oldQuestionThumbnail && $oldQuestionThumbnail !== $newThumbnailPath && Storage::disk('public')->exists($oldQuestionThumbnail)) {
                                Storage::disk('public')->delete($oldQuestionThumbnail);
                            }
                        }
                    } else {
                        // No new thumbnail uploaded, preserve existing one if question has an ID
                        if (isset($questionData['id'])) {
                            $existingQuestion = \App\Models\Question::find($questionData['id']);
                            $questionData['thumbnail'] = $existingQuestion ? $existingQuestion->thumbnail : null;
                        }
                    }
                }
            }

            $updatedGame = $gameService->updateGame($game, $validatedData);

            Log::info('Game updated successfully', [
                'game_id' => $game->id,
                'game_name' => $game->name,
                'updated_by' => Auth::id()
            ]);

            return redirect()
                ->route('admin.index')
                ->with('success', 'Game "' . $updatedGame->name . '" updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating game: ' . $e->getMessage(), [
                'exception' => $e,
                'game_id' => $id,
                'user_id' => Auth::id(),
                'data' => $request->except(['questions'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update game. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified game from storage
     * Checks if game has active sessions before deletion
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $game = Game::findOrFail($id);

            // Check if game has active sessions
            if ($game->hasActiveSessions()) {
                throw new GameInUseException(
                    'Cannot delete "' . $game->name . '" because it has active game sessions. Please complete or cancel all active sessions first.'
                );
            }

            // Track who deleted the game
            $game->deleted_by = Auth::id();
            $game->save();

            // Soft delete the game
            $game->delete();

            Log::info('Game soft deleted successfully', [
                'game_id' => $game->id,
                'game_name' => $game->name,
                'deleted_by' => Auth::id()
            ]);

            return redirect()
                ->route('admin.index')
                ->with('success', 'Game "' . $game->name . '" deleted successfully.');
        } catch (GameInUseException $e) {
            Log::warning('Attempted to delete game with active sessions', [
                'game_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting game: ' . $e->getMessage(), [
                'exception' => $e,
                'game_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete game. Please try again. Error: ' . $e->getMessage());
        }
    }
}
