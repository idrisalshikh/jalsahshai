<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class PlayerAnswer extends Model
{
    protected $fillable = ['player_id', 'question_id', 'answer', 'is_correct', 'answered_at'];

    public function player() {
        return $this->belongsTo(Player::class);
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }
}
