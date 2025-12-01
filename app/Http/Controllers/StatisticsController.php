<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Statistic;

class StatisticsController extends Controller
{
    public function show(Request $request)
    {
        $matchId = $request->query('match_id');
        $teamId = $request->query('team_id');

        if (!$matchId) {
            return response()->json(['error' => 'match_id is required'], 400);
        }

        if ($matchId && $teamId) {
            $stat = Statistic::where('match_id', $matchId)
                ->where('team_id', $teamId)
                ->first();

            return response()->json([
                'match_id' => $matchId,
                'team_id' => $teamId,
                'statistics' => $stat?->stats ?? []
            ]);
        } else {
            $stats = Statistic::where('match_id', $matchId)->get();

            $formattedStats = $stats->map(function ($stat) {
                return [
                    'team_id' => $stat->team_id,
                    'stats' => $stat->stats ?? []
                ];
            });

            return response()->json([
                'match_id' => $matchId,
                'statistics' => $formattedStats
            ]);
        }
    }
}