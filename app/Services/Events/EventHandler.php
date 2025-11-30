<?php
namespace App\Services\Events;

interface EventHandler
{
    public function handle(array $payload): void;
}
