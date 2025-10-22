<?php

namespace Tests\Feature\Backend;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_view_admin_dashboard()
    {
        Game::factory()->count(3)->create();
        $response = $this->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertViewIs('backend.dashboard');
        $response->assertViewHas('games');
    }

    public function test_can_create_a_game()
    {
        $data = [
            'name' => 'New Game',
            'description' => 'A new game description.',
            'video_url' => 'dQw4w9WgXcQ', // Valid YouTube video ID
            'questions' => [
                [
                    'text' => 'Question 1',
                    'type' => 'mcq',
                    'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                    'correct_answer' => '0',
                ],
            ],
        ];

        $response = $this->post(route('admin.games.store'), $data);

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('games', ['name' => 'New Game']);
        $this->assertDatabaseHas('questions', ['text' => 'Question 1']);
    }

    public function test_can_update_a_game()
    {
        $game = Game::factory()->hasQuestions(1)->create();
        $question = $game->questions->first();

        $data = [
            'name' => 'Updated Game',
            'description' => 'An updated description.',
            'video_url' => 'x4YhdUNTT8I', // Valid YouTube video ID
            'questions' => [
                [
                    'id' => $question->id,
                    'text' => 'Updated Question',
                    'type' => 'mcq',
                    'options' => ['Updated Option X', 'Updated Option Y', 'Updated Option Z', 'Updated Option W'],
                    'correct_answer' => '1',
                ],
            ],
        ];

        $response = $this->put(route('admin.games.update', $game->id), $data);

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('games', ['name' => 'Updated Game']);
        $this->assertDatabaseHas('questions', ['text' => 'Updated Question']);
    }

    public function test_can_delete_a_game()
    {
        $game = Game::factory()->create();

        $response = $this->delete(route('admin.games.destroy', $game->id));

        $response->assertRedirect(route('admin.index'));
        $this->assertSoftDeleted($game);
    }
}
