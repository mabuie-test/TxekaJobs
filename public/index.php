<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(ConsoleKernel::class)->bootstrap();

$response = $app->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$app->terminate($request, $response);
