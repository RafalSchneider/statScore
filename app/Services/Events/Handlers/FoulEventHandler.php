<?php
namespace App\Services\Events\Handlers;

use App\Models\Statistic;
use App\Services\Events\EventHandler;

class FoulEventHandler implements EventHandler
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
            ['fouls' => 0]
        );

        $stat->increment('fouls');
    }
}
