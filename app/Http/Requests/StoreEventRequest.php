<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type');

        $base = [
            'type' => 'required|string|in:foul,goal',
            'data' => 'sometimes|array',
            'event_uuid' => 'sometimes|string|uuid',
        ];

        $foulSpecific = [];
        if ($type === 'foul') {
            $foulSpecific = [
                'match_id' => 'required|string',
                'team_id' => 'required|string',
                'player' => 'required|string',
                'minute' => 'required|integer|min:0',
                'second' => 'nullable|integer|min:0|max:59',
            ];
        }

        $goalSpecific = [];
        if ($type === 'goal') {
            $goalSpecific = [
                'match_id' => 'required|string',
                'team_id' => 'required|string',
                'scorer' => 'required|string',
                'assist' => 'nullable|string',
                'minute' => 'required|integer|min:0',
                'second' => 'nullable|integer|min:0|max:59',
            ];
        }

        return array_merge($base, $foulSpecific, $goalSpecific);
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();
        $eventUuid = $validated['event_uuid'] ?? null;
        unset($validated['event_uuid']);

        return [
            'type' => $this->input('type'),
            'payload' => $validated,
            'event_uuid' => $eventUuid,
        ];
    }
}
