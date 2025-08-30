<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'question_id',
        'answer',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
