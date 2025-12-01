<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'match_id',
        'team_id',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];
}