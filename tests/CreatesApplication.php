<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        // Boot the console kernel so facades and the container are fully initialized for tests
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }
}
