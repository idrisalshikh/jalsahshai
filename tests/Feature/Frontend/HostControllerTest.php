<?php

namespace Tests\Feature\Frontend;

use App\Events\SessionStarted;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_host_a_game()
    {
        $game = Game::factory()->create();
        $response = $this->post(route('host.store'), [
            'game_id' => $game->id,
            'nickname' => 'Test Host',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('game_sessions', ['game_id' => $game->id]);
        $this->assertDatabaseHas('players', ['nickname' => 'Test Host', 'is_host' => true]);
    }

    public function test_can_view_waiting_room()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create([
            'code' => 'TESTCODE',
            'status' => 'waiting',
        ]);

        $response = $this->get(route('host.waiting', $session->code));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.host.waiting');
        $response->assertViewHas('session');
    }

    public function test_can_start_a_game()
    {
        $game = Game::factory()->create();
        $session = $game->sessions()->create([
            'code' => 'TESTCODE',
            'status' => 'waiting',
        ]);

        Event::fake();

        $response = $this->post(route('host.start', $session->code));

        $response->assertRedirect(route('host.play', $session->code));
        $this->assertDatabaseHas('game_sessions', ['id' => $session->id, 'status' => 'started']);
        Event::assertDispatched(SessionStarted::class);
    }
}
