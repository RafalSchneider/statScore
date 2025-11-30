<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Services\Events\EventService;

class EventController extends Controller
{
    public function store(StoreEventRequest $request, EventService $service)
    {
        $validated = $request->validatedPayload();
        $type = $validated['type'];
        $payload = $validated['payload'];

        try {
            $event = $service->store($type, $payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Event saved successfully',
                'event' => [
                    'type' => $event->type,
                    'timestamp' => $event->timestamp?->timestamp ?? now()->timestamp,
                    'data' => $event->data,
                ],
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Failed to save event'
            ], 500);
        }
    }
}