<?php

namespace Tests\Feature\Api;

use App\Jobs\StoreEventJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake(); // avoid running async jobs during tests
    }

    public function test_foul_event_is_validated_and_queued(): void
    {
        $payload = [
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'William Saliba',
            'minute' => 45,
            'second' => 34,
        ];

        $res = $this->postJson('/api/event', $payload);
        $res->assertStatus(202)
            ->assertJson([
                'status' => 'accepted',
                'message' => 'Event queued for processing',
            ])
            ->assertJsonStructure([
                'event_uuid',
            ]);

        Queue::assertPushed(StoreEventJob::class, function ($job) use ($payload) {
            return $job->type === 'foul'
                && $job->payload['match_id'] === $payload['match_id']
                && $job->payload['team_id'] === $payload['team_id']
                && !empty($job->eventUuid);
        });
    }

    public function test_validation_errors_on_missing_fields_for_foul(): void
    {
        $payload = [
            'type' => 'foul',
            'player' => 'William Saliba',
            'minute' => 45,
            'second' => 34,
        ];

        $res = $this->postJson('/api/event', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['match_id', 'team_id']);
    }

    public function test_event_requires_type(): void
    {
        $payload = [
            'player' => 'John Doe',
            'minute' => 23,
            'second' => 34,
        ];

        $res = $this->postJson('/api/event', $payload);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_invalid_json_returns_400(): void
    {
        $response = $this->call(
            'POST',
            '/api/event',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        $response->assertStatus(400);
    }
}
