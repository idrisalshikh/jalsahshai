<?php

namespace Tests\Feature;

use App\Models\GameSession;
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
        GameSession::factory()->count(3)->create();
        $response = $this->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin');
        $response->assertViewHas('sessions');
    }

    public function test_can_create_a_game_session()
    {
        $data = [
            'code' => 'NEW_SESSION',
            'video_url' => 'http://example.com/video.mp4',
            'questions' => [
                [
                    'text' => 'Question 1',
                    'type' => 'mcq',
                    'options' => 'A,B,C',
                    'correct_answer' => '0',
                ],
            ],
        ];

        $response = $this->post(route('admin.sessions.store'), $data);

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('game_sessions', ['code' => 'NEW_SESSION']);
        $this->assertDatabaseHas('questions', ['text' => 'Question 1']);
    }

    public function test_can_update_a_game_session()
    {
        $session = GameSession::factory()->hasQuestions(1)->create();
        $question = $session->questions->first();

        $data = [
            'code' => 'UPDATED_CODE',
            'video_url' => 'http://example.com/new-video.mp4',
            'questions' => [
                [
                    'id' => $question->id,
                    'text' => 'Updated Question',
                    'type' => 'mcq',
                    'options' => 'X,Y,Z',
                    'correct_answer' => '1',
                ],
            ],
        ];

        $response = $this->put(route('admin.sessions.update', $session->id), $data);

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('game_sessions', ['code' => 'UPDATED_CODE']);
        $this->assertDatabaseHas('questions', ['text' => 'Updated Question']);
    }

    public function test_can_delete_a_game_session()
    {
        $session = GameSession::factory()->create();

        $response = $this->delete(route('admin.sessions.destroy', $session->id));

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseMissing('game_sessions', ['id' => $session->id]);
    }

    public function test_can_start_a_game_session()
    {
        $session = GameSession::factory()->create(['status' => 'waiting']);

        $response = $this->post(route('admin.sessions.start', $session->id));

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('game_sessions', ['id' => $session->id, 'status' => 'started']);
    }

    public function test_can_finish_a_game_session()
    {
        $session = GameSession::factory()->create(['status' => 'started']);

        $response = $this->post(route('admin.sessions.finish', $session->id));

        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('game_sessions', ['id' => $session->id, 'status' => 'finished']);
    }
}
