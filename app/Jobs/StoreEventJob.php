<?php
namespace App\Jobs;

use App\Services\Events\EventService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $type, public array $payload, public string $eventUuid)
    {
    }

    public function handle(EventService $service): void
    {
        $service->store($this->type, $this->payload, $this->eventUuid);
    }
}
