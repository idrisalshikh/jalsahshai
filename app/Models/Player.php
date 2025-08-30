<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'nickname',
        'is_host',
        'score',
    ];

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
