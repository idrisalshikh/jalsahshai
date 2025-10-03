<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSessionQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'question_id',
        'shown_at',
        'closed_at',
        
    ];
     protected $casts = [
        'shown_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }
    

    public function questions() {
        return $this->belongsToMany(Question::class, 'game_session_questions')
                    ->withPivot(['shown_at', 'closed_at'])
                    ->withTimestamps();
    }
}
