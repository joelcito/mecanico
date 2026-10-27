<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HerramientaController;
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
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\ReparacionDetallesController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\AsignacionHerramientaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\OrdenEntregaController;

use App\Http\Controllers\PortalClienteController;
use App\Http\Middleware\VerificarRol;

Route::get('/', function () {
    // return view('welcome');
    return redirect('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::get('/boleta-publica/{id}', [
    OrdenEntregaController::class,
    'boleta',
])->middleware('signed')
  ->name('ordenServicio.entrega.boleta.publica');



Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/{tipo}/exportar/{formato}', [ReportesController::class, 'exportar'])
            ->where(['tipo' => 'ventas|movimientos|productos|pagos', 'formato' => 'pdf|xlsx'])
            ->name('exportar');
        Route::get('/{tipo}', [ReportesController::class, 'index'])
            ->where('tipo', 'ventas|movimientos|productos|pagos')
            ->name('index');
    });


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

        Route::prefix('herramienta')->group(function () {
            Route::get('/listado', [HerramientaController::class, 'listado'])->name('herramienta.listado');
            Route::post('/ajaxListado', [HerramientaController::class, 'ajaxListado'])->name('herramienta.ajaxListado');
            Route::post('/guardar', [HerramientaController::class, 'guardarHerramienta'])->name('herramienta.guardar');
            Route::post('/eliminar', [HerramientaController::class, 'eliminarHerramienta'])->name('herramienta.eliminar');
        });

        Route::prefix('asignaciones-herramientas')->name('asignacionesHerramientas.')->group(function () {
            Route::get('/', [AsignacionHerramientaController::class, 'index'])->name('listado');
            Route::get('/tecnico/{tecnicoId}', [AsignacionHerramientaController::class, 'porTecnico'])->name('tecnico');
            Route::get('/herramienta/{herramientaId}/historial', [AsignacionHerramientaController::class, 'historialHerramienta'])->name('historialHerramienta');
            Route::post('/', [AsignacionHerramientaController::class, 'store'])->name('guardar');
            Route::put('/{id}/devolver', [AsignacionHerramientaController::class, 'devolver'])->name('devolver');
            Route::put('/herramienta/{herramientaId}/estado', [AsignacionHerramientaController::class, 'actualizarEstado'])->name('actualizarEstado');
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
            Route::get('/{id}/verificar-deuda', [OrdenEntregaController::class, 'verificarDeuda'])->name('ordenServicio.verificarDeuda');
            Route::put('/{id}/entregar', [OrdenEntregaController::class, 'entregar'])->name('ordenServicio.entregar');
            Route::get('/{id}/boleta-entrega', [OrdenEntregaController::class, 'boleta'])->name('ordenServicio.entrega.boleta');
           
            Route::post( '/{id}/reparacion/iniciar', [OrdenServicioController::class, 'iniciarReparacion'])->name('ordenServicio.reparacion.iniciar');
           
            Route::get('/{id}/cotizacion/pdf', [OrdenCotizacionController::class, 'pdf'])->name('ordenServicio.cotizacion.pdf');
            Route::put('/{id}/recepcion', [OrdenServicioController::class, 'actualizarRecepcion'])->name('ordenServicio.recepcion.actualizar');
            Route::get('/{id}/recepcion/editar',[OrdenServicioController::class, 'editarRecepcion'])->name('ordenServicio.recepcion.editar');
            Route::delete('/orden-servicio/{orden}/inspeccion/fotos/{foto}',[OrdenInspeccionController::class, 'eliminarFoto'])->name('ordenServicio.inspeccion.foto.eliminar');
            
            
            Route::get('/{id}/cotizacion/preparar-whatsapp', [OrdenCotizacionController::class,'prepararWhatsApp'])->name('ordenServicio.cotizacion.prepararWhatsApp');
            Route::get('/{id}/cotizacion/{cotizacion}/compartida', [OrdenCotizacionController::class,'pdfCompartido'])->middleware('signed')->name('ordenServicio.cotizacion.compartida');

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


            Route::prefix('reparaciones')->group(function () {
                Route::get('/{id}/datos', [ReparacionController::class, 'datos'])->name('reparaciones.datos');
                Route::put('/{id}/guardar', [ReparacionController::class, 'guardar'])->name('reparaciones.guardar');
                Route::put('/{id}/finalizar', [ReparacionController::class,'finalizar'])->name('reparaciones.finalizar');
                Route::get('/{id}/avances', [ReparacionController::class, 'avances'])->name('reparaciones.avances');
                Route::post('/{id}/avances', [ReparacionController::class, 'guardarAvance'])->name('reparaciones.avances.store');
                Route::get('/{ordenId}/productos', [ReparacionDetallesController::class, 'productosDisponibles'])->name('reparaciones.productos');
                Route::get('/{ordenId}/detalles', [ReparacionDetallesController::class, 'index'])->name('reparaciones.detalles');
                Route::post('/{ordenId}/detalles', [ReparacionDetallesController::class, 'store'])->name('reparaciones.detalles.store');
                Route::put('/{ordenId}/detalles/{detalleId}', [ReparacionDetallesController::class, 'update'])->name('reparaciones.detalles.update');
                Route::delete('/{ordenId}/detalles/{detalleId}', [ReparacionDetallesController::class, 'destroy'])->name('reparaciones.detalles.destroy');
            });


            Route::prefix('inventario')->group(function () {
                Route::post('/ingresos/guardar', [MovimientoInventarioController::class,'guardarIngreso'])->name('inventario.ingresos.guardar');
                Route::post('/salidas/guardar', [MovimientoInventarioController::class,'guardarSalida'])->name('inventario.salidas.guardar');
            });

            Route::middleware(['auth', VerificarRol::class . ':4',])->group(function () {
            Route::get('/mi-portal', [ PortalClienteController::class, 'index',])->name('portal-cliente.inicio');
            });

    
});

require __DIR__.'/auth.php';
