<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OficioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ui\OficioUiController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTH (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |------------------------------
    | DASHBOARD
    |------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');


    Route::get('/oficios-ui', [OficioController::class, 'indexUi'])
    ->middleware('auth')
    ->name('oficios.ui');

        
    Route::get('/oficios-ui/{oficio}', [OficioUiController::class, 'show'])
        ->name('oficios.show.ui');

    /*
    |------------------------------
    | PERFIL
    |------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |------------------------------
    | OFICIOS
    |------------------------------
    */
    Route::get('/oficios-ui', [OficioController::class, 'index']);

    Route::post('/oficios', [OficioController::class, 'store'])
        ->name('oficios.store');

    Route::get('/oficios/{oficio}', [OficioController::class, 'show'])
        ->name('oficios.show');

    Route::post('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])
        ->middleware('permission:puede_turnar')
        ->name('oficios.turnar');

    Route::post('/oficios/{oficio}/cerrar', [OficioController::class, 'cerrar'])
        ->middleware('permission:puede_cerrar')
        ->name('oficios.cerrar');

    /*
    |------------------------------
    | PERMISOS / ROLES
    |------------------------------
    */

    Route::get('/sensibles', function () {
        return 'sensibles';
    })->middleware('permission:puede_ver_sensibles');

    Route::get('/admin', function () {
        return 'solo admin';
    })->middleware('role:admin');
});