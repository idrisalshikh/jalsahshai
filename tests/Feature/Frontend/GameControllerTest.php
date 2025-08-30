<?php

namespace Tests\Feature\Frontend;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_game_lobby()
    {
        Game::factory()->count(3)->create();
        $response = $this->get(route('games.index'));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.games.index');
        $response->assertViewHas('games');
    }

    public function test_can_view_game_details()
    {
        $game = Game::factory()->create();
        $response = $this->get(route('games.show', $game->id));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.games.show');
        $response->assertViewHas('game');
    }
}
