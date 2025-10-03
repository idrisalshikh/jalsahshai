<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'type',
        'options',
        'correct_answer',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function gameSessions()
    {
        return $this->belongsToMany(GameSession::class, 'game_session_question')
        ->withPivot(['shown_at', 'closed_at'])
                    ->withTimestamps();
    }
    
    public function answers() {
        return $this->hasMany(PlayerAnswer::class);
    }
}
