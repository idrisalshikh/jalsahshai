<?php

namespace Tests\Feature;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_sessions()
    {
        GameSession::factory()->count(3)->create();

        $response = $this->getJson('/api/sessions');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_get_a_single_session()
    {
        $session = GameSession::factory()->has(Question::factory()->count(5))->create();

        $response = $this->getJson('/api/sessions/' . $session->code);

        $response->assertStatus(200)
            ->assertJson([
                'code' => $session->code,
                'questions' => [] // The structure is complex, just check for the key
            ])
            ->assertJsonCount(5, 'questions');
    }

    public function test_can_start_a_session()
    {
        $session = GameSession::factory()->create(['status' => 'waiting']);

        $response = $this->postJson('/api/sessions/' . $session->code . '/start');

        $response->assertStatus(200)
            ->assertJson(['status' => 'started']);

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'started',
        ]);
    }

    public function test_can_finish_a_session()
    {
        $session = GameSession::factory()->create(['status' => 'started']);

        $response = $this->postJson('/api/sessions/' . $session->code . '/finish');

        $response->assertStatus(200)
            ->assertJson(['status' => 'finished']);

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'finished',
        ]);
    }

    public function test_can_submit_an_answer()
    {
        $session = GameSession::factory()->create();
        $question = Question::factory()->create();
        $session->questions()->attach($question->id);

        $response = $this->postJson('/api/sessions/' . $session->code . '/answer', [
            'question_id' => $question->id,
            'nickname' => 'Player1',
            'answer' => 'A',
        ]);

        $response->assertStatus(200)
            ->assertJson(['logged' => true]);

        $this->assertDatabaseHas('answers', [
            'game_session_id' => $session->id,
            'question_id' => $question->id,
            'nickname' => 'Player1',
            'answer' => 'A',
        ]);
    }
}
