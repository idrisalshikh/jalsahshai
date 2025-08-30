<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'video_url',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'game_question');
    }

    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }
}
