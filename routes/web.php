<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/categories', 'pages.categories')->name('categories');
Route::view('/activites', 'pages.activites')->name('activites');

Route::view('/portail-profs', 'pages.portail-profs')->name('portail.profs');
Route::view('/portail-parents', 'pages.portail-parents')->name('portail.parents');

Route::post('/portail-profs', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // TODO: brancher l'authentification réelle (Auth::attempt) lorsque le système sera prêt.
    return back()->with('status', 'Identifiants reçus — prêt à être connecté au système ECOPILOTE.');
})->name('portail.profs.login');

Route::post('/portail-parents', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // TODO: brancher l'authentification réelle (Auth::attempt) lorsque le système sera prêt.
    return back()->with('status', 'Identifiants reçus — prêt à être connecté au système ECOPILOTE.');
})->name('portail.parents.login');

/*
|--------------------------------------------------------------------------
| Espace Administration (authentifié + rôles)
|--------------------------------------------------------------------------
*/
Route::prefix('administration')->group(function () {
    // Connexion
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Zone protégée
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        foreach (array_keys(config('admin.modules', [])) as $key) {
            Route::get("/{$key}", [AdminController::class, 'module'])
                ->defaults('key', $key)
                ->middleware("module:{$key}")
                ->name("admin.module.{$key}");
        }
    });
});
