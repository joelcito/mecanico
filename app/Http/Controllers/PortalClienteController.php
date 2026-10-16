<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class PortalClienteController extends Controller
{
   
public function index()
{
    $cliente = \App\Models\Cliente::query()
        ->with([
            'vehiculos.marca',
            'vehiculos.ordenesServicio.inspeccionActual',
            'vehiculos.ordenesServicio.diagnosticoActual.detalles',
            'vehiculos.ordenesServicio.cotizacionActual',
            'vehiculos.ordenesServicio.reparacion.tecnico',
            'vehiculos.ordenesServicio.reparacion.avances',
        ])
        ->where('user_id', auth()->id())
        ->firstOrFail();

    return view('portal-cliente.inicio', compact('cliente'));
}



}
