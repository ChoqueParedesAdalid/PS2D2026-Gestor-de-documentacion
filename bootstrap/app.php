<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\PreventBackHistory;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        // AGREGAR NUESTRO MIDDLEWARE AL GRUPO 'web'
        $middleware->web(append: [
            PreventBackHistory::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('recordatorios:vencimiento')
                 ->dailyAt('09:00')
                 ->timezone('America/La_Paz');
    })
    ->create();