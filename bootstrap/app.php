<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\UppercaseRequestData::class,
        ]);

        $middleware->alias([
            'module' => \App\Http\Middleware\EnsureModuleAccess::class,
            'teacher.auth' => \App\Http\Middleware\EnsureTeacherAuthenticated::class,
            'student.auth' => \App\Http\Middleware\EnsureStudentAuthenticated::class,
            'workspace.admin' => \App\Http\Middleware\IsolateAdminWorkspace::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('espace-eleve*')) {
                return route('portail.etudiant');
            }

            if ($request->is('espace-prof*')) {
                return route('portail.profs');
            }

            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
