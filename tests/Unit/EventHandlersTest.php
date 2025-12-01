<?php

namespace Tests\Unit;

use App\Models\Statistic;
use App\Services\Events\Handlers\FoulEventHandler;
use App\Services\Events\Handlers\GoalEventHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventHandlersTest extends TestCase
{
    use RefreshDatabase;

    public function test_goal_handler_increments_goals(): void
    {
        $handler = new GoalEventHandler();
        $payload = [
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'scorer' => 'John Doe',
            'minute' => 10,
        ];
        $handler->handle($payload);

        $stat = Statistic::where(['match_id' => 'm1', 'team_id' => 'arsenal'])->first();
        $this->assertNotNull($stat);
        $this->assertSame(1, $stat->stats['goals'] ?? null);
    }

    public function test_foul_handler_increments_fouls(): void
    {
        $handler = new FoulEventHandler();
        $payload = [
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'Jane',
            'minute' => 15,
        ];
        $handler->handle($payload);
        $handler->handle($payload);

        $stat = Statistic::where(['match_id' => 'm1', 'team_id' => 'arsenal'])->first();
        $this->assertNotNull($stat);
        $this->assertSame(2, $stat->stats['fouls'] ?? null);
    }
}
