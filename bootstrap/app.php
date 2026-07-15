<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureCompleteCounselorSession;
use App\Http\Middleware\EnsureStudentSecurityPinVerified;
use App\Http\Middleware\RoleMiddleware; 
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsurePublicStudentContext;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        '*',
    ]);

    $middleware->append(PreventBackHistory::class);
    $middleware->append(EnsureStudentSecurityPinVerified::class);
    $middleware->web(append: [
        EnsureCompleteCounselorSession::class,
    ]);
    
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'student.public' => EnsurePublicStudentContext::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    
