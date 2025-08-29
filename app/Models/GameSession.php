<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'video_url',
        'status',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'game_session_question');
    }
}
