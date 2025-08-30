<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Player;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class PlayerApiController extends Controller
{
    public function show($id)
    {
        return Player::findOrFail($id);
    }
}
