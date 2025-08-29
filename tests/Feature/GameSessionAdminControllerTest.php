<?php

namespace Tests\Feature;

use App\Models\GameSession;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSessionAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
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
                    'options' => ['A', 'B', 'C'],
                    'correct_answer' => '0',
                ],
                [
                    'text' => 'Question 2',
                    'type' => 'true_false',
                    'correct_answer' => 'true',
                ],
            ],
        ];

        $response = $this->postJson('/api/admin/sessions', $data);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'NEW_SESSION',
                'questions' => [
                    ['text' => 'Question 1'],
                    ['text' => 'Question 2'],
                ]
            ]);

        $this->assertDatabaseHas('game_sessions', ['code' => 'NEW_SESSION']);
        $this->assertDatabaseHas('questions', ['text' => 'Question 1']);
        $this->assertDatabaseHas('questions', ['text' => 'Question 2']);
    }

    public function test_can_update_a_game_session()
    {
        $session = GameSession::factory()->has(Question::factory()->count(2))->create();
        $question1 = $session->questions->first();

        $data = [
            'code' => 'UPDATED_CODE',
            'video_url' => 'http://example.com/new-video.mp4',
            'questions' => [
                [
                    'id' => $question1->id,
                    'text' => 'Updated Question 1',
                    'type' => 'mcq',
                    'options' => ['X', 'Y', 'Z'],
                    'correct_answer' => '1',
                ],
                [
                    'text' => 'New Question 3',
                    'type' => 'true_false',
                    'correct_answer' => 'false',
                ]
            ]
        ];

        $response = $this->putJson('/api/admin/sessions/' . $session->id, $data);

        $response->assertStatus(200)
            ->assertJson(['code' => 'UPDATED_CODE']);

        $this->assertDatabaseHas('game_sessions', ['code' => 'UPDATED_CODE']);
        $this->assertDatabaseHas('questions', ['text' => 'Updated Question 1']);
        $this->assertDatabaseHas('questions', ['text' => 'New Question 3']);
        $this->assertDatabaseMissing('game_session_question', [
            'game_session_id' => $session->id,
            'question_id' => $session->questions->last()->id
        ]);
    }

    public function test_can_delete_a_game_session()
    {
        $session = GameSession::factory()->create();

        $response = $this->deleteJson('/api/admin/sessions/' . $session->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('game_sessions', ['id' => $session->id]);
    }
}
