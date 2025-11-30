<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{EventController, StatisticsController};


Route::post('/event', [EventController::class, 'store']);
Route::get('/statistics', [StatisticsController::class, 'show']);