<?php
namespace App\Services\Events;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class EventService
{
    public function __construct(private EventHandlerFactory $factory)
    {
    }

    public function store(string $type, array $payload, ?string $eventUuid = null): Event
    {
        return DB::transaction(function () use ($type, $payload, $eventUuid) {
            if ($eventUuid) {
                $existing = Event::where('event_uuid', $eventUuid)->first();
                if ($existing) {
                    return $existing;
                }
            }

            $event = Event::create([
                'type' => $type,
                'timestamp' => now(),
                'data' => $payload,
                'event_uuid' => $eventUuid,
            ]);

            if ($handler = $this->factory->make($type)) {
                $handler->handle($payload);
            }

            $message = [
                'id' => $event->id,
                'type' => $event->type,
                'timestamp' => $event->timestamp?->timestamp ?? now()->timestamp,
                'data' => $event->data,
            ];
            $json = json_encode($message);
            Redis::rpush('events:stream', $json);
            Redis::ltrim('events:stream', -500, -1);
            Redis::publish('events:pub', $json);

            return $event;
        });
    }
}
