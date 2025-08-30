<?php

namespace Tests\Feature\Frontend;

use App\Events\PlayerJoined;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_join_a_game_session()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE']);
        $host = $session->players()->create(['nickname' => 'Host', 'is_host' => true]);
        $session->update(['host_id' => $host->id]);

        Event::fake();

        $response = $this->post(route('join'), [
            'code' => 'TESTCODE',
            'nickname' => 'Player1',
        ]);

        $response->assertRedirect(route('play', 'TESTCODE'));
        $this->assertDatabaseHas('players', ['nickname' => 'Player1', 'is_host' => false]);
        Event::assertDispatched(PlayerJoined::class);
    }

    public function test_first_player_to_join_becomes_host()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE']);

        $response = $this->post(route('join'), [
            'code' => 'TESTCODE',
            'nickname' => 'HostPlayer',
        ]);

        $response->assertRedirect(route('host.waiting', 'TESTCODE'));
        $this->assertDatabaseHas('players', ['nickname' => 'HostPlayer', 'is_host' => true]);
        $session->refresh();
        $this->assertNotNull($session->host_id);
    }

    public function test_can_view_waiting_page()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE', 'status' => 'waiting']);
        $player = $session->players()->create(['nickname' => 'Player1']);
        $this->withSession(['player_id' => $player->id]);

        $response = $this->get(route('play', 'TESTCODE'));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.player.waiting');
        $response->assertViewHas('session');
    }

    public function test_can_view_play_page()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE', 'status' => 'started']);
        $player = $session->players()->create(['nickname' => 'Player1']);
        $this->withSession(['player_id' => $player->id]);

        $response = $this->get(route('play', 'TESTCODE'));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.player.play');
        $response->assertViewHas('session');
    }

    public function test_can_submit_an_answer()
    {
        $game = Game::factory()->hasQuestions(1)->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE']);
        $player = $session->players()->create(['nickname' => 'Player1']);
        $question = $game->questions->first();
        $this->withSession(['player_id' => $player->id]);

        $response = $this->postJson(route('play.answer', 'TESTCODE'), [
            'question_id' => $question->id,
            'answer' => '0',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('answers', ['player_id' => $player->id, 'question_id' => $question->id]);
    }

    public function test_a_second_player_joining_is_a_normal_player()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create(['code' => 'TESTCODE']);
        $host = $session->players()->create(['nickname' => 'Host', 'is_host' => true]);
        $session->update(['host_id' => $host->id]);

        $response = $this->post(route('join'), [
            'code' => 'TESTCODE',
            'nickname' => 'Player2',
        ]);

        $response->assertRedirect(route('play', 'TESTCODE'));
        $this->assertDatabaseHas('players', ['nickname' => 'Player2', 'is_host' => false]);
    }
}
