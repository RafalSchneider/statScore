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

            $fouls = $stat ? $stat->fouls : 0;

            return response()->json([
                'match_id' => $matchId,
                'team_id' => $teamId,
                'statistics' => [
                    'fouls' => $fouls
                ]
            ]);
        } else {
            $stats = Statistic::where('match_id', $matchId)->get();

            $formattedStats = $stats->map(function ($stat) {
                return [
                    'team_id' => $stat->team_id,
                    'fouls' => $stat->fouls
                ];
            });

            return response()->json([
                'match_id' => $matchId,
                'statistics' => $formattedStats
            ]);
        }
    }
}