<?php
namespace App\Services\Events;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function __construct(private EventHandlerFactory $factory)
    {
    }

    public function store(string $type, array $payload): Event
    {
        return DB::transaction(function () use ($type, $payload) {
            $event = Event::create([
                'type' => $type,
                'timestamp' => now(),
                'data' => $payload,
            ]);

            if ($handler = $this->factory->make($type)) {
                $handler->handle($payload);
            }

            return $event;
        });
    }
}
