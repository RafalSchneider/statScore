<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{EventController, StatisticsController};


Route::post('/event', [EventController::class, 'store']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/statistics', [StatisticsController::class, 'show']);
Route::get('/events-stream', [EventController::class, 'stream']);