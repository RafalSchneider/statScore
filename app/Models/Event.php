<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'type',
        'timestamp',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'timestamp' => 'datetime',
    ];
}