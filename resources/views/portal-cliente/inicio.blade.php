@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold">Mi portal</h2>
        <p class="text-muted mb-0">
            Consulta tus vehículos y el progreso de sus reparaciones.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-1">
                {{ $cliente->razon_social ?: trim(
                    ($cliente->user->nombres ?? '') . ' ' .
                    ($cliente->user->ap_paterno ?? '') . ' ' .
                    ($cliente->user->ap_materno ?? '')
                ) }}
            </h5>
            <div class="text-muted small">
                Cliente registrado
            </div>
        </div>
    </div>

    @forelse($cliente->vehiculos as $vehiculo)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">
                            {{ $vehiculo->marca->nombre ?? 'Vehículo' }}
                            {{ $vehiculo->modelo }}
                        </h5>
                        <span class="text-muted">
                            Placa: {{ $vehiculo->placa }}
                        </span>
                    </div>

                    <span class="badge bg-secondary">
                        {{ $vehiculo->anio ?? 'Año no registrado' }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                @forelse($vehiculo->ordenesServicio as $orden)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    Orden {{ $orden->numero_orden }}
                                </h6>
                                <small class="text-muted">
                                    Recepción:
                                    {{ $orden->fecha_recepcion?->format('d/m/Y H:i') ?? 'No registrada' }}
                                </small>
                            </div>

                            <span class="badge bg-primary">
                                {{ str_replace('_', ' ', $orden->estado ?? 'SIN ESTADO') }}
                            </span>
                        </div>

                        @if($orden->motivo_ingreso)
                            <p class="mb-3">
                                <strong>Motivo de ingreso:</strong>
                                {{ $orden->motivo_ingreso }}
                            </p>
                        @endif

                        @if($orden->diagnosticoActual)
                            <div class="alert alert-light border">
                                <strong>Diagnóstico</strong>
                                <p class="mb-1">
                                    {{ $orden->diagnosticoActual->descripcion }}
                                </p>
                                @if($orden->diagnosticoActual->observaciones)
                                    <small class="text-muted">
                                        {{ $orden->diagnosticoActual->observaciones }}
                                    </small>
                                @endif
                            </div>
                        @endif

                        @if($orden->reparacion)
                            <div class="mb-3">
                                <h6 class="fw-bold">Estado de reparación</h6>
                                <p class="mb-1">
                                    <strong>Estado:</strong>
                                    {{ str_replace('_', ' ', $orden->reparacion->estado ?? 'EN PROCESO') }}
                                </p>

                                @if($orden->reparacion->tecnico)
                                    <p class="mb-1">
                                        <strong>Técnico:</strong>
                                        {{ $orden->reparacion->tecnico->nombres }}
                                    </p>
                                @endif

                                @if($orden->reparacion->trabajos_realizados)
                                    <p class="mb-1">
                                        <strong>Trabajos realizados:</strong>
                                        {{ $orden->reparacion->trabajos_realizados }}
                                    </p>
                                @endif

                                @if($orden->reparacion->fecha_inicio)
                                    <p class="small text-muted mb-1">
                                        Inicio:
                                        {{ $orden->reparacion->fecha_inicio->format('d/m/Y H:i') }}
                                    </p>
                                @endif

                                @if($orden->reparacion->fecha_fin)
                                    <p class="small text-muted mb-0">
                                        Finalización:
                                        {{ $orden->reparacion->fecha_fin->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>

                            <h6 class="fw-bold mt-4">Historial de avances</h6>

                            @forelse($orden->reparacion->avances as $avance)
                                <div class="border-start border-3 ps-3 py-2 mb-3">
                                    <div class="small text-muted">
                                        {{ $avance->fecha?->format('d/m/Y H:i') }}
                                    </div>

                                    <div class="fw-semibold">
                                        {{ $avance->descripcion }}
                                    </div>

                                    @if($avance->observaciones)
                                        <p class="text-muted mb-1">
                                            {{ $avance->observaciones }}
                                        </p>
                                    @endif

                                    @if($avance->tecnico)
                                        <small class="text-muted">
                                            Técnico: {{ $avance->tecnico->nombres }}
                                        </small>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small">
                                    Aún no hay avances publicados.
                                </p>
                            @endforelse
                        @else
                            <p class="text-muted mb-0">
                                La reparación todavía no ha comenzado o no se ha registrado.
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        Este vehículo todavía no tiene órdenes de servicio.
                    </p>
                @endforelse
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No tienes vehículos registrados. Contacta con el taller para asociar tu vehículo a tu cuenta.
        </div>
    @endforelse
</div>
@endsection
