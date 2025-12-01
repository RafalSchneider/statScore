<?php

namespace Tests\Feature\Api;

use App\Services\Events\Handlers\FoulEventHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StatisticsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_get_team_statistics(): void
    {
        // apply two foul events via handler to update stats
        $handler = new FoulEventHandler();
        $handler->handle([
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'William Saliba',
            'minute' => 15,
            'second' => 34,
        ]);
        $handler->handle([
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'Gabriel Jesus',
            'minute' => 30,
            'second' => 33,
        ]);

        $res = $this->getJson('/api/statistics?match_id=m1&team_id=arsenal');
        $res->assertOk()
            ->assertJson([
                'match_id' => 'm1',
                'team_id' => 'arsenal',
                'statistics' => [
                    'fouls' => 2,
                ],
            ]);
    }

    public function test_get_match_statistics(): void
    {
        $handler = new FoulEventHandler();
        $handler->handle([
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'William Saliba',
            'minute' => 15,
            'second' => 34,
        ]);
        $handler->handle([
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'liverpool',
            'player' => 'Virgil van Dijk',
            'minute' => 30,
            'second' => 33,
        ]);

        $res = $this->getJson('/api/statistics?match_id=m1');
        $res->assertOk();
        $json = $res->json();
        $this->assertSame('m1', $json['match_id']);

        // stats array of teams
        $this->assertIsArray($json['statistics']);
        $teams = collect($json['statistics'])->keyBy('team_id');
        $this->assertSame(1, $teams['arsenal']['stats']['fouls'] ?? null);
        $this->assertSame(1, $teams['liverpool']['stats']['fouls'] ?? null);
    }

    public function test_missing_match_id_returns_400(): void
    {
        $this->getJson('/api/statistics')->assertStatus(400)
            ->assertJson(['error' => 'match_id is required']);
    }

    public function test_nonexistent_team_returns_empty_stats(): void
    {
        $res = $this->getJson('/api/statistics?match_id=m1&team_id=nonexistent');
        $res->assertOk()
            ->assertJson([
                'match_id' => 'm1',
                'team_id' => 'nonexistent',
            ]);
        $this->assertSame([], $res->json('statistics'));
    }

    public function test_nonexistent_match_returns_empty_list(): void
    {
        $res = $this->getJson('/api/statistics?match_id=nonexistent');
        $res->assertOk();
        $this->assertSame('nonexistent', $res->json('match_id'));
        $this->assertSame([], $res->json('statistics'));
    }
}
