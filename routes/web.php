<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\OrdenInspeccionController;
use App\Http\Controllers\OrdenDiagnosticoController;
use App\Http\Controllers\OrdenCotizacionController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CuentaPorCobrarController;

Route::get('/', function () {
    // return view('welcome');
    return redirect('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');


     Route::prefix('/rol')->group(function () {
        Route::get('/listado', [RolController::class, 'listado'])->name('rol.listado');
        Route::post('/ajaxListado', [RolController::class, 'ajaxListado'])->name('rol.ajaxListado');
        Route::post('/guardarRol', [RolController::class, 'guardarRol'])->name('rol.guardarRol');
        Route::post('/eliminarRol', [RolController::class, 'eliminarRol'])->name('rol.eliminarRol');
    });

    Route::prefix('/user')->group(function () {
        Route::get('/listado', [UserController::class, 'listado'])->name('user.listado');
        Route::post('/ajaxListado', [UserController::class, 'ajaxListado'])->name('user.ajaxListado');
        Route::post('/guardarUser', [UserController::class, 'guardarUser'])->name('user.guardarUser');
        Route::post('/eliminarUser', [UserController::class, 'eliminarUser'])->name('user.eliminarUser');

        Route::get('/control-personal/user/{id}', [UserController::class, 'getUser']);
    });


    Route::prefix('/sucursal')->group(function () {
        Route::get('/listado', [SucursalController::class, 'listado'])->name('sucursal.listado');
        Route::post('/ajaxListado', [SucursalController::class, 'ajaxListado'])->name('sucursal.ajaxListado');
        Route::post('/guardarSucursal', [SucursalController::class, 'guardarSucursal'])->name('sucursal.guardarSucursal');
        Route::post('/eliminarSucursal', [SucursalController::class, 'eliminarSucursal'])->name('sucursal.eliminarSucursal');
    });
    
    Route::prefix('cliente')->group(function () {

            Route::get('/listado', [ClienteController::class, 'listado'])->name('cliente.listado');
            Route::post('/ajaxListado', [ClienteController::class, 'ajaxListado'])->name('cliente.ajaxListado');
            Route::post('/guardar', [ClienteController::class, 'guardarCliente'])->name('cliente.guardar');
            Route::post('/eliminar', [ClienteController::class, 'eliminarCliente'])->name('cliente.eliminar');
        });

        Route::prefix('categoria')->group(function () {

            Route::get('/listado', [CategoriaController::class, 'listado'])->name('categoria.listado');
            Route::post('/ajaxListado', [CategoriaController::class, 'ajaxListado'])->name('categoria.ajaxListado');
            Route::post('/guardar', [CategoriaController::class, 'guardarCategoria'])->name('categoria.guardar');
            Route::post('/eliminar', [CategoriaController::class, 'eliminarCategoria'])->name('categoria.eliminar');
        });

        Route::prefix('marca')->group(function () {

            Route::get('/listado', [MarcaController::class, 'listado'])->name('marca.listado');
            Route::post('/ajaxListado', [MarcaController::class, 'ajaxListado'])->name('marca.ajaxListado');
            Route::post('/guardar', [MarcaController::class, 'guardarMarca'])->name('marca.guardar');
            Route::post('/eliminar', [MarcaController::class, 'eliminarMarca'])->name('marca.eliminar');
        });

        Route::prefix('producto')->group(function () {

            Route::get('/listado', [ProductoController::class, 'listado'])->name('producto.listado');
            Route::post('/ajaxListado', [ProductoController::class, 'ajaxListado'])->name('producto.ajaxListado');
            Route::post('/guardar', [ProductoController::class, 'guardarProducto'])->name('producto.guardar');
            Route::post('/eliminar', [ProductoController::class, 'eliminarProducto'])->name('producto.eliminar');
        });

        Route::prefix('vehiculo')->group(function () {

            Route::get('/listado', [VehiculoController::class, 'listado'])->name('vehiculo.listado');
            Route::post('/ajaxListado', [VehiculoController::class, 'ajaxListado'])->name('vehiculo.ajaxListado');
            Route::post('/guardar', [VehiculoController::class, 'guardarVehiculo'])->name('vehiculo.guardar');
            Route::post('/eliminar', [VehiculoController::class, 'eliminarVehiculo'])->name('vehiculo.eliminar');
        });

        Route::prefix('ordenServicio')->group(function () {
            Route::get('/listado', [OrdenServicioController::class, 'listado'] )->name('ordenServicio.listado');
            Route::post('/ajax-listado', [OrdenServicioController::class, 'ajaxListado'])->name('ordenServicio.ajaxListado');
            Route::get('/nuevo',[OrdenServicioController::class, 'nuevo'])->name('ordenServicio.nuevo');
            Route::post(  '/guardar', [OrdenServicioController::class, 'guardar'])->name('ordenServicio.guardar');
            Route::get('/{id}/inspeccion',[OrdenInspeccionController::class, 'crear'])->name('ordenServicio.inspeccion');
            Route::post('/{id}/inspeccion', [OrdenInspeccionController::class, 'guardar'])->name('ordenServicio.inspeccion.guardar');
            Route::get('/{id}', [OrdenServicioController::class, 'detalle'])->name('ordenServicio.detalle');

            Route::get('/{id}/diagnostico', [OrdenDiagnosticoController::class, 'crear'])->name('ordenServicio.diagnostico');
            Route::post('/{id}/diagnostico', [OrdenDiagnosticoController::class, 'guardar'])->name('ordenServicio.diagnostico.guardar');

            Route::get('/{id}/cotizacion',[OrdenCotizacionController::class, 'crear'])->name('ordenServicio.cotizacion');
            Route::post('/{id}/cotizacion',[OrdenCotizacionController::class, 'guardar'])->name('ordenServicio.cotizacion.guardar');
    
            Route::post('/{id}/cotizacion/aprobar',[OrdenServicioController::class, 'aprobarCotizacion'])->name('ordenServicio.cotizacion.aprobar');
            Route::post('/{id}/cotizacion/rechazar',[OrdenServicioController::class, 'rechazarCotizacion'])->name('ordenServicio.cotizacion.rechazar');
           
            Route::post( '/{id}/reparacion/iniciar', [OrdenServicioController::class, 'iniciarReparacion'])->name('ordenServicio.reparacion.iniciar');
           
            });

       Route::prefix('cajas')->group(function () {
            Route::get('/listado', [CajaController::class, 'listado'])->name('cajas.listado');
            Route::post('/ajaxListado', [CajaController::class, 'ajaxListado'])->name('cajas.ajaxListado');
            Route::get('/abrir', [CajaController::class, 'crear'])->name('cajas.abrir');
            Route::post('/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir.guardar');
            Route::get('/{id}/actual', [CajaController::class, 'actual'])->name('cajas.actual');
            Route::post('/{id}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
        });

        Route::prefix('movimientosCaja')->group(function () {
            Route::get('/listado', [MovimientoCajaController::class, 'listado'])->name('movimientosCaja.listado');
            Route::post('/ajaxListado', [MovimientoCajaController::class, 'ajaxListado'])->name('movimientosCaja.ajaxListado');
            Route::get('/crear', [MovimientoCajaController::class, 'crear'])->name('movimientosCaja.crear');
            Route::post('/guardar', [MovimientoCajaController::class, 'guardar'])->name('movimientosCaja.guardar');
            Route::post('/{id}/anular', [MovimientoCajaController::class, 'anular'])->name('movimientosCaja.anular');
            });

            Route::prefix('pagos')->group(function () {
                Route::get('/listado', [PagoController::class, 'listado'])->name('pagos.listado');
                Route::post('/ajaxListado', [PagoController::class, 'ajaxListado'])->name('pagos.ajaxListado');
                Route::get('/crear', [PagoController::class, 'crear'])->name('pagos.crear');
                Route::post('/guardar', [PagoController::class, 'guardar'])->name('pagos.guardar');
                Route::get('/{id}/actual', [PagoController::class, 'actual'])->name('pagos.actual');
                Route::post('/{id}/anular', [PagoController::class, 'anular'])->name('pagos.anular');

            });

            Route::prefix('cuentasPorCobrar')->group(function () {
                Route::get('/listado', [CuentaPorCobrarController::class, 'listado'])->name('cuentasPorCobrar.listado');
                Route::post( '/ajaxListado',[CuentaPorCobrarController::class, 'ajaxListado'])->name('cuentasPorCobrar.ajaxListado');

            });





});

require __DIR__.'/auth.php';
