<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'type',
        'timestamp',
        'data',
        'event_uuid',
    ];

    protected $casts = [
        'data' => 'array',
        'timestamp' => 'datetime',
    ];

    public function scopeForMatch($query, ?string $matchId)
    {
        if ($matchId) {
            $query->where('data->match_id', $matchId);
        }
        return $query;
    }

    public function scopeForTeam($query, ?string $teamId)
    {
        if ($teamId) {
            $query->where('data->team_id', $teamId);
        }
        return $query;
    }

    public function scopeForType($query, ?string $type)
    {
        if ($type) {
            $query->where('type', $type);
        }
        return $query;
    }

    public function scopeForPlayer($query, ?string $player)
    {
        if ($player) {
            $query->where(function ($q) use ($player) {
                $q->where('data->player', $player)
                    ->orWhere('data->scorer', $player)
                    ->orWhere('data->assist', $player)
                    ->orWhere('data->affected_player', $player)
                    ->orWhere('data->foul_player', $player);
            });
        }
        return $query;
    }
}