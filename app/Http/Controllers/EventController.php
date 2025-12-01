<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Services\Events\EventService;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use App\Jobs\StoreEventJob;

class EventController extends Controller
{
    public function store(StoreEventRequest $request, EventService $service)
    {
        $validated = $request->validatedPayload();
        $type = $validated['type'];
        $payload = $validated['payload'];
        $eventUuid = $validated['event_uuid'] ?? Str::uuid()->toString();

        if ($existing = Event::where('event_uuid', $eventUuid)->first()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Event already processed',
                'idempotent' => true,
                'event' => [
                    'type' => $existing->type,
                    'timestamp' => $existing->timestamp?->timestamp ?? now()->timestamp,
                    'data' => $existing->data,
                ],
            ], 200);
        }

        dispatch(new StoreEventJob($type, $payload, $eventUuid))->onQueue('events');

        return response()->json([
            'status' => 'accepted',
            'message' => 'Event queued for processing',
            'idempotent' => $validated['event_uuid'] !== null,
            'event_uuid' => $eventUuid,
        ], 202);
    }

    public function index(Request $request)
    {
        $matchId = $request->query('match_id');
        $teamId = $request->query('team_id');
        $type = $request->query('type');
        $player = $request->query('player');
        $perPage = (int) ($request->query('per_page', 25));
        $perPage = $perPage > 100 ? 100 : ($perPage < 1 ? 25 : $perPage);

        $query = Event::query()
            ->forMatch($matchId)
            ->forTeam($teamId)
            ->forType($type)
            ->forPlayer($player)
            ->orderBy('timestamp');

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json([
            'id' => $event->id,
            'type' => $event->type,
            'timestamp' => $event->timestamp?->timestamp ?? null,
            'data' => $event->data,
        ]);
    }

    public function stream(): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            set_time_limit(0);

            echo ": connected\n\n";
            @ob_flush();
            flush();


            try {
                $recent = Redis::lrange('events:stream', -50, -1) ?? [];
                foreach ($recent as $json) {
                    echo "event: replay\n";
                    echo "data: {$json}\n\n";
                    @ob_flush();
                    flush();
                }
            } catch (\Throwable $e) {
            }

            Redis::subscribe(['events:pub'], function ($message) {
                echo "event: live\n";
                echo "data: {$message}\n\n";
                @ob_flush();
                flush();
            });
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-transform');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}