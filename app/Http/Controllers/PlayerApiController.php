<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerApiController extends Controller
{
    public function show($id)
    {
        return Player::findOrFail($id);
    }
}
