<?php
namespace App\Services\Events;

use App\Services\Events\Handlers\FoulEventHandler;
use App\Services\Events\Handlers\GoalEventHandler;

class EventHandlerFactory
{
    public function make(string $type): ?EventHandler
    {
        return match ($type) {
            'foul' => new FoulEventHandler(),
            'goal' => new GoalEventHandler(),
            default => null,
        };
    }
}
