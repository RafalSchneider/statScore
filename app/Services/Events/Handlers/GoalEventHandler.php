<?php
namespace App\Services\Events\Handlers;

use App\Models\Statistic;
use App\Services\Events\EventHandler;

class GoalEventHandler implements EventHandler
{
    public function handle(array $payload): void
    {
        if (!isset($payload['match_id'], $payload['team_id'])) {
            return;
        }

        $stat = Statistic::firstOrCreate(
            [
                'match_id' => $payload['match_id'],
                'team_id' => $payload['team_id'],
            ],
            [
                'fouls' => 0,
                'goals' => 0,
            ]
        );

        if (method_exists($stat, 'increment')) {
            $stat->increment('goals');
        } else {
            $stat->goals = ($stat->goals ?? 0) + 1;
            $stat->save();
        }
    }
}
