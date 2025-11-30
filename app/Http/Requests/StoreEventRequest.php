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
        return [
            'type' => $this->input('type'),
            'payload' => $this->validated(),
        ];
    }
}
