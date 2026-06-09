<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OficioController;

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
| AUTH
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
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [OficioController::class, 'dashboard'])
        ->middleware('verified')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | HOME (Bandeja)
    |--------------------------------------------------------------------------
    */
    Route::get('/home', [OficioController::class, 'home'])
        ->name('home');
    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | FILTROS Y DATATABLES
    |--------------------------------------------------------------------------
    */

    // Consumo para tablas de oficos
    Route::get('/oficios/datatable', [OficioController::class, 'datatable'])
        ->name('oficios.datatable');

    
    /*
    |--------------------------------------------------------------------------
    | OFICIOS
    |--------------------------------------------------------------------------
    */

    // LISTADO
    Route::get('/oficios', [OficioController::class, 'index'])
        ->name('oficios.index');

    // DETALLE
    Route::get('/oficios/{oficio}', [OficioController::class, 'show'])
        ->name('oficios.show');

    //endpoint para obtener datos 
    Route::get(
        '/oficios/{oficio}/detalle',
        [OficioController::class, 'detalle']
    )->name('oficios.detalle');

    Route::get('/oficios/{oficio}/detalle', [OficioController::class, 'detalle']);

    // CREAR
    Route::post('/oficios', [OficioController::class, 'store'])
        ->name('oficios.store');

    // ACTUALIZAR OFICIO (EDITAR)
    Route::put('/oficios/{oficio}', [OficioController::class, 'update'])
        ->name('oficios.update');

    // TURNAR
    Route::post('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])
        ->middleware('permission:puede_turnar')
        ->name('oficios.turnar');

    Route::post('/turnados/{turnado}/atender', [OficioController::class, 'atender'])
        ->name('turnados.atender');

    // CERRAR
    Route::post('/oficios/{oficio}/cerrar', [OficioController::class, 'cerrar'])
        ->middleware('permission:puede_cerrar')
        ->name('oficios.cerrar');

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS PERMISOS
    |--------------------------------------------------------------------------
    */

    Route::get('/sensibles', function () {
        return 'sensibles';
    })->middleware('permission:puede_ver_sensibles');

    Route::get('/admin', function () {
        return 'solo admin';
    })->middleware('role:admin');

});