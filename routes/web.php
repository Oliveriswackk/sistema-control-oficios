<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\TurnadoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\OficioArchivoController;
use App\Models\Turnado;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login'); 
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

    // DETALLE HTML
    Route::get('/oficios/{oficio}/detalle', [OficioController::class, 'detalle'])
        ->name('oficios.detalle');

    // DETALLE JSON
    Route::get('/oficios/{oficio}/detalle-json', [OficioController::class, 'detalleJson'])
        ->name('oficios.detalle.json');

    // CREAR
    Route::post('/oficios', [OficioController::class, 'store'])
        ->middleware('permission:puede_registrar_oficios')
        ->name('oficios.store');

    // ACTUALIZAR
    Route::put('/oficios/{oficio}', [OficioController::class, 'update'])
        ->name('oficios.update');

    // MODAL TURNAR (GET)
    Route::get('/oficios/{oficio}/turnar', [OficioController::class, 'turnarModal'])
        ->name('oficios.turnar.modal');
        
    // GUARDAR TURNAR (POST SINGLE)
    Route::post('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])
        ->middleware('permission:puede_turnar')
        ->name('oficios.turnar');

    // CARGAR ARCHIVO PDF
    Route::post('/oficios/{oficio}/archivos', [OficioArchivoController::class, 'store'])
        ->name('oficios.archivos.store');

    // CERRAR
    Route::post('/oficios/{oficio}/cerrar', [OficioController::class, 'cerrar'])
        ->name('oficios.cerrar');

    // CANCELAR
    Route::post('/oficios/{oficio}/cancelar', 
        [OficioController::class, 'cancelar']
    )
    ->name('oficios.cancelar');


    // CONSECUTIVO
    Route::get('/proximo-consecutivo', [OficioController::class, 'proximoConsecutivo']);    

    // RESERVAR NO. OFICIO
    Route::post('/oficios/reservar-folios', [
        OficioController::class,
        'reservarFolios'
    ])
    ->middleware('permission:puede_registrar_oficios')
    ->name('oficios.reservar-folios');

    /*Route::post('/oficios/reservar-folios', [
        OficioController::class,
        'reservarFolios'
    ])->name('oficios.reservar-folios'); */

    

    /*
    |--------------------------------------------------------------------------
    | TAGS
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/tags/buscar',
        [TagController::class, 'buscar']
    )->name('tags.buscar');

    Route::post(
        '/oficios/{oficio}/tags',
        [TagController::class, 'agregarTag']
    );

    Route::delete(
        '/oficios/{oficio}/tags/{tag}',
        [TagController::class, 'eliminarTag']
    );


    /*
    |--------------------------------------------------------------------------
    | TURNADOS
    |--------------------------------------------------------------------------
    */

    Route::post('/turnados/{turnado}/atender', [OficioController::class, 'atender'])
        ->name('turnados.atender');


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