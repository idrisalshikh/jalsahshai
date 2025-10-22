<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'video_url',
        'thumbnail',
        'is_timed',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the questions associated with this game
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get all game sessions for this game
     */
    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * Get active game sessions (not completed)
     */
    public function activeSessions()
    {
        return $this->hasMany(GameSession::class)->where('status', '!=', 'completed');
    }

    /**
     * Get the user who created this game
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this game
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this game
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Check if game has any active sessions
     */
    public function hasActiveSessions(): bool
    {
        return $this->activeSessions()->exists();
    }

    /**
     * Scope to filter games by search term
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope to sort games
     */
    public function scopeSortBy($query, $sortBy = 'created_at', $sortDirection = 'desc')
    {
        $allowedSortFields = ['name', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];

        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortDirection = in_array($sortDirection, $allowedDirections) ? $sortDirection : 'desc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}
