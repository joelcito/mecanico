@extends('layouts.app')
@section('css')

<style>
    .orden-header {
        border-radius: 10px;
    }

    .step-container {
        display: flex;
        align-items: center;
        width: 100%;
        overflow-x: auto;
        padding: 10px 0;
    }

    .step {
        min-width: 130px;
        text-align: center;
        position: relative;
    }

    .step:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 22px;
        left: 65%;
        width: 70%;
        height: 2px;
        background: #e4e6ef;
        z-index: 1;
    }

    .step-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        position: relative;
        z-index: 2;
        font-weight: 700;
    }

    .step-active .step-circle {
        background: #009ef7;
        color: white;
    }

    .step-completed .step-circle {
        background: #50cd89;
        color: white;
    }

    .step-locked .step-circle {
        background: #eff2f5;
        color: #a1a5b7;
    }

    .step-title {
        margin-top: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .step-subtitle {
        font-size: 11px;
        color: #a1a5b7;
    }

    .info-box {
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
        height: 100%;
    }

    .info-box-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #7e8299;
        margin-bottom: 5px;
    }

    .info-box-value {
        font-size: 15px;
        font-weight: 600;
        color: #181c32;
    }

    .repair-timeline {
        border-left: 2px solid #b8e5ea;
        margin-left: 8px;
        padding-left: 22px;
    }

    .repair-timeline-item {
        position: relative;
        padding: 0 0 22px;
    }

    .repair-timeline-item:last-child {
        padding-bottom: 0;
    }

    .repair-timeline-item::before {
        content: "";
        position: absolute;
        left: -29px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #12a6b4;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #12a6b4;
    }

    .repair-timeline-date {
        color: #087f8c;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .repair-timeline-content {
        color: #3f4b5b;
    }
</style>

@endsection
@section('content')
<div class="d-flex flex-column flex-column-fluid">
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container"
        class="app-container container-xxlg">
        <div class="card shadow-sm mb-5 orden-header">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <h2 class="fw-bold mb-0 me-3">
                                {{ $orden->numero_orden }}
                            </h2>
                            <span class="badge badge-light-primary">
                                {{ $orden->estado }}
                            </span>
                        </div>
                        <div class="text-muted">
                            <i class="fa fa-car me-1"></i>
                            {{ $orden->vehiculo?->marca?->nombre ?? '-' }}
                            {{ $orden->vehiculo?->modelo ?? '-' }}
                            <span class="mx-2">•</span>
                            {{ $orden->vehiculo?->placa ?? '-' }}
                            <span class="mx-2">•</span>
                            {{ $orden->vehiculo?->cliente?->user?->nombres ?? '-' }}
                            {{ $orden->vehiculo?->cliente?->user?->ap_paterno ?? '' }}
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('ordenServicio.listado') }}"
                            class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left"></i>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                @php
                    $estado = $orden->estado;
                    $etapas = [
                        [
                            'nombre' => 'Recepción',
                            'estado' => ['RECIBIDO'],
                        ],
                        [
                            'nombre' => 'Inspección',
                            'estado' => ['EN_INSPECCION'],
                        ],
                        [
                            'nombre' => 'Diagnóstico',
                            'estado' => ['EN_DIAGNOSTICO'],
                        ],
                        [
                            'nombre' => 'Cotización',
                            'estado' => ['EN_COTIZACION'],
                        ],
                        [
                            'nombre' => 'Reparación',
                            'estado' => ['AUTORIZADO', 'EN_REPARACION'],
                        ],
                        [
                            'nombre' => 'Entrega',
                            'estado' => ['LISTO', 'ENTREGADO'],
                        ],
                    ];

                    // Determinamos en qué etapa está la orden
                    if (in_array($estado, ['RECIBIDO'])) {
                        $etapaActual = 0;
                    } elseif (in_array($estado, ['EN_INSPECCION'])) {
                        $etapaActual = 1;
                    } elseif (in_array($estado, ['EN_DIAGNOSTICO'])) {
                        $etapaActual = 2;
                    } elseif (in_array($estado, ['EN_COTIZACION'])) {
                        $etapaActual = 3;
                    } elseif (in_array($estado, ['AUTORIZADO', 'EN_REPARACION'])) {
                        $etapaActual = 4;
                    } elseif ($estado === 'LISTO') {
                        $etapaActual = 5;
                    } elseif ($estado === 'ENTREGADO') {
                        $etapaActual = 6;
                    } else {
                        $etapaActual = 0;
                    }
                @endphp
                <div class="step-container">
                    @php
                        $rutasEtapas = [
                            route('ordenServicio.recepcion.editar', $orden->id),
                            route('ordenServicio.inspeccion', $orden->id),
                            route('ordenServicio.diagnostico', $orden->id),
                            route('ordenServicio.cotizacion', $orden->id),
                        ];

                        $etapasDisponibles = [
                            true,
                            (bool) $orden->inspeccionActual,
                            (bool) $orden->diagnosticoActual,
                            (bool) $orden->cotizacionActual,
                            (bool) $orden->reparacion,
                            (bool) $orden->entrega,
                        ];
                    @endphp

                    @foreach($etapas as $index => $etapa)
                        @php
                            $completada = $index < $etapaActual;
                            $actual = $index === $etapaActual;

                            $puedeEditar = $etapasDisponibles[$index]
                                && $index < 4;

                            $rutaEtapa = $index < 4
                                ? $rutasEtapas[$index]
                                : null;
                        @endphp

                        <div class="step
                            {{ $completada ? 'step-completed' : '' }}
                            {{ $actual ? 'step-active' : '' }}
                            {{ !$completada && !$actual ? 'step-locked' : '' }}">

                            @if($puedeEditar)
                                <a href="{{ $rutaEtapa }}"
                                class="text-reset text-decoration-none d-block">
                            @endif

                                <div class="step-circle">
                                    @if($completada)
                                        <i class="fa fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>

                                <div class="step-title">
                                    {{ $etapa['nombre'] }}
                                </div>

                                <div class="step-subtitle">
                                    @if($completada)
                                        Completado · Editar
                                    @elseif($actual)
                                        Etapa actual
                                    @else
                                        Bloqueado
                                    @endif
                                </div>

                            @if($puedeEditar)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fa fa-info-circle text-primary me-2"></i>
                    Información de la orden
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Cliente
                            </div>
                            <div class="info-box-value">
                                {{ $orden->vehiculo?->cliente?->user?->nombres ?? '-' }}
                                {{ $orden->vehiculo?->cliente?->user?->ap_paterno ?? '' }}
                                {{ $orden->vehiculo?->cliente?->user?->ap_materno ?? '' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Vehículo
                            </div>
                            <div class="info-box-value">
                                {{ $orden->vehiculo?->marca?->nombre ?? '-' }}
                                {{ $orden->vehiculo?->modelo ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">Tipo de vehículo</div>
                            <div class="info-box-value">{{ $orden->vehiculo?->tipo_vehiculo ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Placa
                            </div>
                            <div class="info-box-value">
                                {{ $orden->vehiculo?->placa ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Recepción
                            </div>
                            <div class="info-box-value">
                                {{ $orden->fecha_recepcion
                                    ? $orden->fecha_recepcion->format('d/m/Y H:i')
                                    : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Kilometraje
                            </div>
                            <div class="info-box-value">
                                {{ $orden->kilometraje !== null
                                    ? number_format($orden->kilometraje, 0, ',', '.')
                                    : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Combustible
                            </div>
                            <div class="info-box-value">
                                {{ $orden->nivel_combustible !== null
                                    ? $orden->nivel_combustible . ' %'
                                    : '-' }}

                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="info-box">
                            <div class="info-box-title">
                                Motivo de ingreso
                            </div>
                            <div class="info-box-value">
                                {{ $orden->motivo_ingreso ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if ($orden->observaciones)
                    <div class="separator separator-dashed my-5"></div>
                    <div>
                        <div class="info-box-title">
                            Observaciones
                        </div>
                        <div class="text-gray-700">
                            {{ $orden->observaciones }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

@if($orden->diagnosticoActual)
    @php
        $diagnostico = $orden->diagnosticoActual;
        $detallesDiagnostico = $diagnostico->detalles ?? collect();
    @endphp

    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h3 class="card-title fw-bold">
                <i class="fa fa-stethoscope text-primary me-2"></i>
                Diagnóstico del vehículo
            </h3>

            <span class="badge badge-light-success">
                {{ $diagnostico->estado }}
            </span>
        </div>

        <div class="card-body">
            <div class="mb-5">
                <label class="fw-bold text-gray-800">
                    Descripción general
                </label>
                <p class="text-gray-700 mt-2">
                    {{ $diagnostico->descripcion ?: 'Sin descripción registrada.' }}
                </p>
            </div>

            @if($diagnostico->observaciones)
                <div class="mb-5">
                    <label class="fw-bold text-gray-800">
                        Observaciones generales
                    </label>
                    <p class="text-gray-700 mt-2">
                        {{ $diagnostico->observaciones }}
                    </p>
                </div>
            @endif

            <div class="separator separator-dashed my-5"></div>

            <h4 class="fw-bold mb-4">Hallazgos y recomendaciones</h4>

            @forelse($detallesDiagnostico as $indice => $detalle)
                <div class="border rounded p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <h5 class="fw-bold mb-0">
                            {{ $indice + 1 }}. {{ $detalle->descripcion }}
                        </h5>

                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge badge-light-info">
                                {{ $detalle->tipo === 'HALLAZGO' ? 'Hallazgo' : 'Recomendación' }}
                            </span>

                            <span class="badge
                                {{ $detalle->prioridad === 'CRITICA' ? 'badge-light-danger' :
                                    ($detalle->prioridad === 'ALTA' ? 'badge-light-warning' : 'badge-light-secondary') }}">
                                Prioridad: {{ $detalle->prioridad }}
                            </span>
                        </div>
                    </div>

                    @if($detalle->observacion)
                        <p class="text-gray-600 mb-4">
                            <strong>Observación:</strong>
                            {{ $detalle->observacion }}
                        </p>
                    @endif

                    @php
                        $fotosDiagnostico = $detalle->fotos ?? collect();
                    @endphp

                    @if($fotosDiagnostico->isNotEmpty())
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">
                                <i class="fa fa-camera text-primary me-2"></i>
                                Fotografías del hallazgo
                            </h6>

                            <div class="row g-4">
                                @foreach($fotosDiagnostico as $foto)
                                    <div class="col-6 col-md-4 col-xl-3">
                                        <a href="{{ asset('storage/' . ltrim($foto->ruta, '/')) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="d-block text-decoration-none">

                                            <img
                                                src="{{ asset('storage/' . ltrim($foto->ruta, '/')) }}"
                                                alt="{{ $foto->descripcion ?: 'Fotografía del diagnóstico' }}"
                                                class="img-fluid rounded border w-100"
                                                style="height: 190px; object-fit: cover;"
                                                loading="lazy">
                                        </a>

                                        @if($foto->descripcion)
                                            <div class="text-muted small mt-2">
                                                {{ $foto->descripcion }}
                                            </div>
                                        @else
                                            <div class="text-muted small mt-2">
                                                Fotografía {{ $loop->iteration }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-muted small mt-3">
                            <i class="fa fa-image me-1"></i>
                            Este detalle no tiene fotografías.
                        </div>
                    @endif
                </div>
            @empty
                <div class="alert alert-light">
                    No hay hallazgos ni recomendaciones registrados.
                </div>
            @endforelse
        </div>
    </div>
@endif
         @if($orden->cotizacionActual)
            <div class="card shadow-sm mb-5">
                <div class="card-header">
                    <h3 class="card-title fw-bold">
                        <i class="fa fa-file-invoice-dollar text-primary me-2"></i>
                        Cotización
                    </h3>
                    <div class="card-toolbar">
                        @if($orden->cotizacionActual->estado === 'PENDIENTE')
                            <span class="badge badge-light-warning">
                                <i class="fa fa-clock me-1"></i>
                                Pendiente
                            </span>
                        @elseif($orden->cotizacionActual->estado === 'APROBADA')
                            <span class="badge badge-light-success">
                                <i class="fa fa-check-circle me-1"></i>
                                Aprobada
                            </span>
                        @elseif($orden->cotizacionActual->estado === 'RECHAZADA')
                            <span class="badge badge-light-danger">
                                <i class="fa fa-times-circle me-1"></i>
                                Rechazada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle">
                            <thead>
                                <tr>
                                 <th>
                                     Tipo
                                    </th>
                                    <th>
                                        Descripción
                                    </th>
                                    <th class="text-end">
                                        Cantidad
                                    </th>
                                    <th class="text-end">
                                        Precio unitario
                                    </th>
                                    <th class="text-end">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->cotizacionActual->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <span class="badge badge-light-info">
                                                {{ $detalle->tipo }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $detalle->descripcion }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format(
                                                $detalle->cantidad,
                                                2
                                            ) }}
                                        </td>
                                        <td class="text-end">
                                            Bs.
                                            {{ number_format(
                                                $detalle->precio_unitario,
                                                2
                                            ) }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            Bs.
                                            {{ number_format(
                                                $detalle->subtotal,
                                                2
                                            ) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-5">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span>
                                    Subtotal
                                </span>
                                <strong>
                                    Bs.
                                    {{ number_format(
                                        $orden->cotizacionActual->subtotal,
                                        2
                                    ) }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>
                                    Descuento
                                </span>
                                <strong>
                                    Bs.
                                    {{ number_format(
                                        $orden->cotizacionActual->descuento,
                                        2
                                    ) }}

                                </strong>
                            </div>
                            <div class="separator my-4"></div>
                            <div class="d-flex justify-content-between">
                                <span class="fs-3 fw-bold">
                                    TOTAL
                                </span>
                                <strong class="fs-3">

                                    Bs.
                                    {{ number_format(
                                        $orden->cotizacionActual->total,
                                        2
                                    ) }}

                                </strong>
                            </div>
                        </div>
                    </div>

                    @if($orden->cotizacionActual->observaciones)
                        <div class="separator separator-dashed my-5"></div>
                        <div>
                            <div class="info-box-title">
                                Observaciones de la cotización
                            </div>
                            <div class="text-gray-700">
                                {{ $orden->cotizacionActual->observaciones }}
                            </div>
                        </div>
                    @endif


                    @if($orden->cotizacionActual->estado === 'APROBADA')
                    <div class="separator separator-dashed my-5"></div>
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fa fa-check-circle fs-2x me-4"></i>
                        <div>
                            <div class="fw-bold fs-5">
                                Cotización aprobada
                            </div>
                            <div class="text-gray-700">
                                La cotización fue aprobada correctamente.
                            </div>
                            @if($orden->cotizacionActual->fecha_respuesta)
                                <div class="text-muted mt-1">
                                    Fecha de aprobación:
                                    {{ $orden->cotizacionActual->fecha_respuesta->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>

                
                @elseif($orden->cotizacionActual->estado === 'RECHAZADA')
                    <div class="separator separator-dashed my-5"></div>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fa fa-times-circle fs-2x me-4"></i>
                        <div>
                            <div class="fw-bold fs-5">
                                Cotización rechazada
                            </div>
                            @if($orden->cotizacionActual->observacion_respuesta)
                                <div class="text-gray-700 mt-1">
                                    {{ $orden->cotizacionActual->observacion_respuesta }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                @php
                    $telefonoWhatsApp = preg_replace(
                        '/\D+/',
                        '',
                        (string) ($orden->vehiculo?->cliente?->user?->celular ?? '')
                    );

                    if (strlen($telefonoWhatsApp) === 8) {
                        $telefonoWhatsApp = '591' . $telefonoWhatsApp;
                    }
                @endphp

                <div class="d-flex justify-content-end mt-4 gap-2 flex-wrap">
                    <a
                        href="{{ route('ordenServicio.cotizacion.pdf', $orden->id) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-danger"
                    >
                        <i class="fa fa-file-pdf me-2"></i>
                        Ver PDF de cotización
                    </a>

                   


@if($telefonoWhatsApp && $orden->cotizacionActual)
    <button
        type="button"
        id="btnEnviarCotizacionWhatsApp"
        class="btn btn-success"
        data-telefono="{{ preg_replace('/\D/', '', $telefonoWhatsApp) }}"
        data-pdf-url="{{ route('ordenServicio.cotizacion.pdf', $orden->id) }}"
        data-orden="{{ $orden->numero_orden ?? $orden->id }}"
    >
        <i class="fab fa-whatsapp me-2"></i>
        Enviar cotización por WhatsApp
    </button>
@else
    <button type="button" class="btn btn-secondary" disabled>
        <i class="fab fa-whatsapp me-2"></i>
        {{ !$telefonoWhatsApp ? 'Cliente sin celular registrado' : 'No hay cotización registrada' }}
    </button>
@endif



                </div>

                    @if($orden->cotizacionActual->estado === 'PENDIENTE')
                    <div class="separator separator-dashed my-5"></div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">
                                Autorización de cotización
                            </h4>
                            <span class="text-muted">
                                Revise el detalle y determine si la cotización será aprobada o rechazada.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-light-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRechazarCotizacion">
                                <i class="fa fa-times me-2"></i>
                                Rechazar
                            </button>
                            <button type="button"
                                    class="btn btn-success"
                                    id="btnAprobarCotizacion">
                                <i class="fa fa-check me-2"></i>
                                Aprobar cotización
                            </button>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        @endif


        @if($orden->estado === 'EN_REPARACION' && $orden->reparacion?->estado !== 'FINALIZADA')
            <div class="card shadow-sm mb-5">
                <div class="card-header">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">
                                <i class="fa fa-wrench text-primary me-2"></i>
                                Reparación del vehículo
                            </h3>

                            <span class="text-muted">
                                Registre los trabajos realizados y el avance de la reparación.
                            </span>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-warning fs-6">
                            <i class="fa fa-cog fa-spin me-1"></i>
                            EN PROCESO
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div id="erroresReparacion"
                        class="alert alert-danger d-none">
                    </div>

                    <div class="row mb-6">
                        <div class="col-md-4 mb-4">
                            <div class="info-box">
                                <div class="info-box-title">
                                    Técnico responsable
                                </div>

                                <div class="info-box-value">
                                    <span id="tecnicoActual">
                                        Sin asignar
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="info-box">
                                <div class="info-box-title">
                                    Fecha de inicio
                                </div>

                                <div class="info-box-value">
                                    <span id="fechaInicioActual">
                                        -
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="info-box">
                                <div class="info-box-title">Estado de reparación</div>
                                <div class="info-box-value"><span id="estadoReparacionActual" class="badge badge-light-warning">EN_PROCESO</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-6"></div>
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <label class="form-label fw-semibold">
                                Técnico responsable
                            </label>

                            <select id="tecnico_id" class="form-select">
                                <option value="">
                                    Seleccione un técnico
                                </option>
                            </select>
                        </div>


                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-semibold">
                                Trabajos realizados
                            </label>
                            <textarea
                                id="trabajos_realizados"
                                class="form-control"
                                rows="6"
                                placeholder="Describa detalladamente los trabajos realizados en el vehículo..."
                            ></textarea>
                            <div class="form-text">
                                Registre las actividades realizadas, reparaciones,
                                ajustes, cambios y verificaciones efectuadas.
                            </div>
                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-semibold">
                                Observaciones de reparación
                            </label>
                            <textarea
                                id="observaciones_reparacion"
                                class="form-control"
                                rows="4"
                                placeholder="Ingrese observaciones, recomendaciones o detalles adicionales..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-6"></div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Productos / Repuestos utilizados</h4>
                            <span class="text-muted">Se registra el consumo real; sin precios de venta.</span>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnAbrirAgregarProducto" data-bs-toggle="modal" data-bs-target="#modalAgregarProductoReparacion">
                            <i class="fa fa-plus me-2"></i>Agregar producto
                        </button>
                    </div>
                    <div class="table-responsive mb-6">
                        <table class="table table-row-dashed align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th class="text-end">Utilizado</th>
                                    <th class="text-end">Stock actual</th>
                                    <th>Observación</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="productosUtilizadosBody">
                                <tr><td colspan="6" class="text-center text-muted">Cargando productos utilizados...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Avances de reparación</h4>
                            <span class="text-muted">Registro cronológico del trabajo realizado.</span>
                        </div>
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#modalAvanceReparacion">
                            <i class="fa fa-plus me-2"></i>Registrar avance
                        </button>
                    </div>
                    <div id="avancesReparacionTimeline" class="repair-timeline mb-6">
                        <div class="text-muted">Cargando avances...</div>
                    </div>

                    <div class="mb-3">
                        <h4 class="fw-bold">Historial de inventario</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-row-dashed align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Motivo</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Stock anterior</th>
                                    <th class="text-end">Stock nuevo</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody id="movimientosReparacionBody">
                                <tr><td colspan="8" class="text-center text-muted">Cargando movimientos...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="separator separator-dashed my-6"></div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="text-muted">
                                <i class="fa fa-info-circle me-1"></i>
                                Los avances individuales se conservan en el historial cronológico.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-light-primary"
                                id="btnGuardarReparacion">
                                <i class="fa fa-save me-2"></i>
                                Guardar reparación
                            </button>
                            <button
                                type="button"
                                class="btn btn-success"
                                id="btnFinalizarReparacion">
                                <i class="fa fa-check-circle me-2"></i>
                                Finalizar reparación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($orden->estado === 'EN_REPARACION' && $orden->reparacion?->estado !== 'FINALIZADA')
                <div class="modal fade" id="modalAgregarProductoReparacion" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header"><h3 class="modal-title fw-bold">Agregar producto utilizado</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                            <div class="modal-body">
                                <div class="mb-4">
                                    <label for="buscarProductoReparacion" class="form-label fw-semibold">Buscar por código, nombre o marca</label>
                                    <input type="search" id="buscarProductoReparacion" class="form-control" placeholder="Escriba para buscar">
                                </div>
                                <div class="mb-4">
                                    <label for="productoReparacion" class="form-label fw-semibold">Producto</label>
                                    <select id="productoReparacion" class="form-select"><option value="">Seleccione un producto</option></select>
                                    <div id="stockProductoReparacion" class="form-text">Stock disponible: - | Unidad: -</div>
                                </div>
                                <div class="mb-4">
                                    <label for="cantidadProductoReparacion" class="form-label fw-semibold">Cantidad utilizada</label>
                                    <input type="number" id="cantidadProductoReparacion" class="form-control" min="0.01" step="0.01" placeholder="0.00">
                                    <div id="acumuladoProductoReparacion" class="form-text">Si el producto ya está en la reparación, esta cantidad se sumará al consumo existente.</div>
                                </div>
                                <div>
                                    <label for="observacionProductoReparacion" class="form-label fw-semibold">Observación</label>
                                    <input type="text" id="observacionProductoReparacion" class="form-control" maxlength="1000" placeholder="Opcional">
                                </div>
                                <div id="errorModalProductoReparacion" class="alert alert-danger d-none mt-4 mb-0"></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnAgregarProductoReparacion"><i class="fa fa-plus me-2"></i>Agregar consumo</button></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalEditarProductoReparacion" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header"><h3 class="modal-title fw-bold">Editar consumo</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                            <div class="modal-body">
                                <input type="hidden" id="detalleProductoReparacionId">
                                <div class="info-box mb-4"><div class="info-box-title">Producto</div><div class="info-box-value" id="editarProductoNombre"></div></div>
                                <div class="mb-4"><label for="editarCantidadProducto" class="form-label fw-semibold">Cantidad utilizada</label><input type="number" id="editarCantidadProducto" class="form-control" min="0.01" step="0.01"><div id="impactoCantidadProducto" class="form-text">El inventario se ajustará solamente por la diferencia.</div></div>
                                <div><label for="editarObservacionProducto" class="form-label fw-semibold">Observación</label><input type="text" id="editarObservacionProducto" class="form-control" maxlength="1000"></div>
                                <div id="errorEditarProductoReparacion" class="alert alert-danger d-none mt-4 mb-0"></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnGuardarEdicionProducto">Guardar cambios</button></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalAvanceReparacion" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header"><h3 class="modal-title fw-bold">Registrar avance</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                            <div class="modal-body">
                                <div class="mb-4"><label for="descripcionAvanceReparacion" class="form-label fw-semibold">Descripción del avance</label><textarea id="descripcionAvanceReparacion" class="form-control" rows="3" maxlength="5000" required></textarea></div>
                                <div><label for="observacionAvanceReparacion" class="form-label fw-semibold">Observación</label><textarea id="observacionAvanceReparacion" class="form-control" rows="2" maxlength="5000"></textarea></div>
                                <div id="errorAvanceReparacion" class="alert alert-danger d-none mt-4 mb-0"></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnGuardarAvanceReparacion">Registrar avance</button></div>
                        </div>
                    </div>
                </div>

            @endif

            @if($orden->reparacion?->estado === 'FINALIZADA')
                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h3 class="card-title fw-bold"><i class="fa fa-wrench text-primary me-2"></i>Reparación finalizada</h3>
                        <span class="badge badge-light-success">FINALIZADA</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-4"><div class="info-box"><div class="info-box-title">Técnico</div><div class="info-box-value">{{ $orden->reparacion->tecnico?->nombres ?? '-' }} {{ $orden->reparacion->tecnico?->ap_paterno ?? '' }}</div></div></div>
                            <div class="col-md-4"><div class="info-box"><div class="info-box-title">Inicio</div><div class="info-box-value">{{ $orden->reparacion->fecha_inicio?->format('d/m/Y H:i') ?? '-' }}</div></div></div>
                            <div class="col-md-4"><div class="info-box"><div class="info-box-title">Finalización</div><div class="info-box-value">{{ $orden->reparacion->fecha_fin?->format('d/m/Y H:i') ?? '-' }}</div></div></div>
                        </div>
                        <h5 class="fw-bold">Trabajos realizados</h5>
                        <p class="text-gray-700">{{ $orden->reparacion->trabajos_realizados }}</p>
                        @if($orden->reparacion->observaciones)
                            <h5 class="fw-bold mt-4">Observaciones</h5>
                            <p class="text-gray-700">{{ $orden->reparacion->observaciones }}</p>
                        @endif
                        <div class="table-responsive mt-5">
                            <table class="table table-row-dashed align-middle">
                                <thead><tr><th>Producto</th><th>Tipo de uso</th><th class="text-end">Cantidad</th><th>Unidad</th><th>Observación</th></tr></thead>
                                <tbody>
                                    @forelse($orden->reparacion->detalles as $detalle)
                                        <tr>
                                            <td>{{ $detalle->producto?->nombre ?? 'Producto eliminado' }}</td>
                                            <td>{{ $detalle->tipo_uso }}</td>
                                            <td class="text-end">{{ number_format((float) $detalle->cantidad, 2, ',', '.') }}</td>
                                            <td>{{ $detalle->producto?->unidad_medida ?? '-' }}</td>
                                            <td>{{ $detalle->observaciones ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">No se registraron productos utilizados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <h5 class="fw-bold mt-6">Historial de movimientos</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-row-dashed align-middle">
                                <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Motivo</th><th class="text-end">Cantidad</th><th class="text-end">Stock anterior</th><th class="text-end">Stock nuevo</th><th>Usuario</th></tr></thead>
                                <tbody>
                                    @forelse($orden->reparacion->movimientosInventario->whereIn('motivo', ['REPARACION', 'DEVOLUCION_REPARACION'])->sortByDesc('fecha') as $movimiento)
                                        <tr>
                                            <td>{{ $movimiento->fecha?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td>{{ $movimiento->producto?->nombre ?? 'Producto eliminado' }}</td>
                                            <td><span class="badge {{ $movimiento->tipo === 'SALIDA' ? 'badge-light-danger' : 'badge-light-success' }}">{{ $movimiento->tipo }}</span></td>
                                            <td>{{ $movimiento->motivo }}</td>
                                            <td class="text-end">{{ number_format((float) $movimiento->cantidad, 2, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float) $movimiento->stock_anterior, 2, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float) $movimiento->stock_nuevo, 2, ',', '.') }}</td>
                                            <td>{{ $movimiento->usuarioCreador?->nombres ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted">No hay movimientos de inventario.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <h5 class="fw-bold mt-6">Avances de reparación</h5>
                        <div class="repair-timeline mt-4">
                            @forelse($orden->reparacion->avances as $avance)
                                <div class="repair-timeline-item">
                                    <div class="repair-timeline-date">{{ $avance->fecha?->format('d/m/Y H:i') ?? '-' }}</div>
                                    <div class="repair-timeline-content">
                                        <div class="fw-bold">{{ $avance->descripcion }}</div>
                                        <div class="text-muted small">{{ $avance->usuario?->nombres ?? $avance->tecnico?->nombres ?? 'Usuario no disponible' }}</div>
                                        @if($avance->observaciones)<div class="mt-2">{{ $avance->observaciones }}</div>@endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted">No se registraron avances individuales.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if($orden->estado === 'LISTO')
                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h3 class="card-title fw-bold"><i class="fa fa-clipboard-check text-primary me-2"></i>Verificación previa a entrega</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <div class="info-box-title">Reparación</div>
                                    <div class="info-box-value">
                                        @if($orden->reparacion?->estado === 'FINALIZADA')
                                            <span class="badge badge-light-success">FINALIZADA</span>
                                        @else
                                            <span class="badge badge-light-warning">NO FINALIZADA</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <div class="info-box-title">Estado de orden</div>
                                    <div class="info-box-value">LISTO</div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3"><div class="info-box"><div class="info-box-title">Estado de pago</div><div class="info-box-value" id="estadoPagoEntrega">PENDIENTE DE VERIFICACIÓN</div></div></div>
                            <div class="col-md-3 mb-3"><div class="info-box"><div class="info-box-title">Total</div><div class="info-box-value">Bs. <span id="totalEntrega">{{ number_format((float) ($orden->cotizacionActual?->total ?? 0), 2, ',', '.') }}</span></div></div></div>
                            <div class="col-md-3 mb-3"><div class="info-box"><div class="info-box-title">Pagado</div><div class="info-box-value">Bs. <span id="pagadoEntrega">{{ number_format((float) $orden->total_pagado, 2, ',', '.') }}</span></div></div></div>
                            <div class="col-md-3 mb-3"><div class="info-box"><div class="info-box-title">Saldo pendiente</div><div class="info-box-value text-danger">Bs. <span id="saldoEntrega">{{ number_format((float) $orden->saldo_pendiente, 2, ',', '.') }}</span></div></div></div>
                        </div>

                        @if($orden->reparacion?->estado !== 'FINALIZADA')
                            <div class="alert alert-warning">La reparación de esta orden todavía no ha finalizado.</div>
                        @endif
                        <div id="resultadoVerificacionEntrega" class="alert alert-info" role="status">Verifique los pagos para habilitar la entrega.</div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($orden->reparacion?->estado === 'FINALIZADA')
                                <button type="button" class="btn btn-light-primary" id="btnVerificarDeuda"><i class="fa fa-rotate me-2"></i>Verificar pagos</button>
                            @endif
                            <button type="button" class="btn btn-success" id="btnRealizarEntrega" disabled><i class="fa fa-check me-2"></i>Realizar entrega</button>
                        </div>
                    </div>
                </div>
            @endif

            @if($orden->estado === 'ENTREGADO')
                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h3 class="card-title fw-bold"><i class="fa fa-check-circle text-success me-2"></i>Orden entregada</h3>
                        <span class="badge badge-light-success">ENTREGADO</span>
                    </div>
                    <div class="card-body">
                        @if($orden->entrega)
                            <div class="row mb-5">
                                <div class="col-md-6 mb-3"><div class="info-box"><div class="info-box-title">Fecha de entrega</div><div class="info-box-value">{{ $orden->entrega->fecha_entrega?->format('d/m/Y H:i') ?? '-' }}</div></div></div>
                                <div class="col-md-6 mb-3"><div class="info-box"><div class="info-box-title">Entregado por</div><div class="info-box-value">{{ trim(($orden->entrega->usuario?->nombres ?? '') . ' ' . ($orden->entrega->usuario?->ap_paterno ?? '') . ' ' . ($orden->entrega->usuario?->ap_materno ?? '')) ?: 'Usuario no disponible' }}</div></div></div>
                            </div>
                            
                                <div class="d-flex gap-2 flex-wrap">
                                    <a
                                        href="{{ route('ordenServicio.entrega.boleta', $orden->id) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-light-primary"
                                    >
                                        <i class="fa fa-file-pdf me-2"></i>
                                        Ver boleta
                                    </a>

                                    <a
                                        href="{{ route('ordenServicio.entrega.boleta', $orden->id) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-light"
                                    >
                                        <i class="fa fa-print me-2"></i>
                                        Imprimir
                                    </a>

                                    @if($telefonoWhatsApp)
                                        <a
                                            href="https://wa.me/{{ $telefonoWhatsApp }}?text={{ rawurlencode('Hola, le compartimos la boleta de entrega de la orden de servicio #' . $orden->id . '. Le enviaremos el documento PDF por este chat.') }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-success"
                                        >
                                            <i class="fab fa-whatsapp me-2"></i>
                                            Enviar boleta por WhatsApp
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fab fa-whatsapp me-2"></i>
                                            Cliente sin celular registrado
                                        </button>
                                    @endif
                                </div>

                        @else
                            <div class="alert alert-danger mb-0">La orden figura como entregada, pero no tiene un registro de entrega asociado.</div>
                        @endif
                    </div>
                </div>
            @endif
        
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if(in_array($orden->estado, ['RECIBIDO', 'EN_INSPECCION']))
                            <h4 class="fw-bold mb-1">
                                Siguiente paso: Inspección
                            </h4>
                            <span class="text-muted">
                                Registre el estado actual del vehículo antes de iniciar el diagnóstico.
                            </span>
                        @elseif($orden->estado === 'EN_DIAGNOSTICO')
                            <h4 class="fw-bold mb-1">
                                Siguiente paso: Diagnóstico
                            </h4>
                            <span class="text-muted">
                                Analice los hallazgos de la inspección y registre el diagnóstico del vehículo.
                            </span>
                        @elseif($orden->estado === 'EN_COTIZACION')
                            @if($orden->cotizacionActual?->estado === 'PENDIENTE')
                                <h4 class="fw-bold mb-1">
                                    Siguiente paso: Autorizar cotización
                                </h4>
                                <span class="text-muted">
                                    Revise la cotización y apruebe o rechace los trabajos y repuestos propuestos.
                                </span>
                            @else
                                <h4 class="fw-bold mb-1">
                                    Siguiente paso: Cotización
                                </h4>
                                <span class="text-muted">
                                    Prepare la cotización de los trabajos y repuestos necesarios.
                                </span>
                            @endif
                        @elseif($orden->estado === 'AUTORIZADO')
                            <h4 class="fw-bold mb-1">
                                Siguiente paso: Reparación
                            </h4>
                            <span class="text-muted">
                                La cotización fue aprobada. Inicie la reparación del vehículo.
                            </span>
                        @else
                            <h4 class="fw-bold mb-1">
                                Estado de la orden
                            </h4>
                            <span class="text-muted">
                                La orden se encuentra en estado:
                                <strong>{{ $orden->estado }}</strong>
                            </span>
                        @endif
                </div>
                   <div>
                        @if(in_array($orden->estado, ['RECIBIDO', 'EN_INSPECCION']))
                            <a href="{{ route('ordenServicio.inspeccion', $orden->id) }}"
                                class="btn btn-primary">
                                <i class="fa fa-clipboard-check me-2"></i>
                                Realizar inspección
                            </a>
                        @elseif($orden->estado === 'EN_DIAGNOSTICO')

                            <a href="{{ route('ordenServicio.diagnostico', $orden->id) }}"
                                class="btn btn-primary">
                                <i class="fa fa-stethoscope me-2"></i>
                                Realizar diagnóstico
                            </a>
                        @elseif($orden->estado === 'EN_COTIZACION')
                            @if($orden->cotizacionActual?->estado === 'PENDIENTE')
                                <span class="badge badge-light-warning fs-6">
                                    <i class="fa fa-clock me-2"></i>
                                    Pendiente de autorización
                                </span>
                            @else
                                <a href="{{ route('ordenServicio.cotizacion', $orden->id) }}"
                                    class="btn btn-primary">
                                    <i class="fa fa-file-invoice-dollar me-2"></i>
                                    Preparar cotización
                                </a>
                            @endif
                        @elseif($orden->estado === 'AUTORIZADO')
                            <div>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <div class="info-box-title">
                                                Total cotización
                                            </div>
                                            <div class="info-box-value">
                                                Bs. {{ number_format($orden->cotizacionActual?->total ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <div class="info-box-title">
                                                Total pagado
                                            </div>
                                            <div class="info-box-value text-success">
                                                Bs. {{ number_format($orden->total_pagado, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <div class="info-box-title">
                                                Saldo pendiente
                                            </div>
                                            <div class="info-box-value text-danger">
                                                Bs. {{ number_format($orden->saldo_pendiente, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        onclick="registrarPagoOrden({{ $orden->id }})">
                                        <i class="fa fa-dollar-sign me-2"></i>
                                        Registrar pago
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        onclick="iniciarReparacion({{ $orden->id }})">
                                        <i class="fa fa-wrench me-2"></i>
                                        Iniciar reparación
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@if($orden->cotizacionActual && $orden->cotizacionActual->estado === 'PENDIENTE')

<div class="modal fade" id="modalRechazarCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bold">
                    Rechazar cotización
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Motivo del rechazo
                    </label>
                    <textarea
                        id="observacionRechazo"
                        class="form-control"
                        rows="4"
                        placeholder="Ingrese el motivo por el cual se rechaza la cotización..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button"
                        class="btn btn-danger"
                        id="btnRechazarCotizacion">
                    <i class="fa fa-times me-2"></i>
                    Rechazar cotización
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@if($orden->estado === 'AUTORIZADO')

<div class="modal fade" id="modalPagoOrden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bold">
                    <i class="fa fa-dollar-sign me-2 text-success"></i>
                    Registrar pago
                </h3>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>
            </div>

            <form id="formPagoOrden">
                <div class="modal-body">
                    <input type="hidden"
                           id="orden_servicio_id_pago"
                           value="{{ $orden->id }}">
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <div class="info-box">
                                <div class="info-box-title">
                                    Total cotización
                                </div>
                                <div class="info-box-value">
                                    Bs.
                                    {{ number_format($orden->cotizacionActual?->total ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <div class="info-box-title">
                                    Total pagado
                                </div>
                                <div class="info-box-value text-success">
                                    Bs.
                                    {{ number_format($orden->total_pagado, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <div class="info-box-title">
                                    Saldo pendiente
                                </div>

                                <div class="info-box-value text-danger">
                                    Bs.
                                    {{ number_format($orden->saldo_pendiente, 2) }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Caja
                            </label>

                            <select id="caja_id_pago"
                                    class="form-select">
                                <option value="">
                                    Seleccione una caja
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Tipo de pago
                            </label>

                            <select id="tipo_pago_orden"
                                    class="form-select">
                                <option value="">
                                    Seleccione
                                </option>
                                <option value="EFECTIVO">
                                    Efectivo
                                </option>
                                <option value="QR">
                                    QR
                                </option>
                                <option value="TRANSFERENCIA">
                                    Transferencia
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Monto recibido
                            </label>

                           <input
                                type="number"
                                class="form-control"
                                id="monto_recibido_orden"
                                name="monto_recibido"
                                min="0"
                                step="0.01"
                                placeholder="Ingrese el monto recibido"
                            >
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Cambio
                            </label>

                            <input type="text"
                                   id="cambio_pago_orden"
                                   class="form-control"
                                   value="Bs. 0.00"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>

                            <input type="text"
                                   id="descripcion_pago_orden"
                                   class="form-control"
                                   placeholder="Descripción opcional">
                        </div>

                    </div>

                    <div id="erroresPagoOrden"
                         class="alert alert-danger d-none">
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="btnGuardarPagoOrden">
                        <i class="fa fa-check me-2"></i>
                        Registrar pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
@section('js')

<script>

function registrarPagoOrden(idOrden)
{
    limpiarFormularioPagoOrden();

    $('#orden_servicio_id_pago').val(idOrden);

    // El saldo es informativo, no el monto obligatorio del pago.
    $('#monto_recibido_orden').val('');
    $('#cambio_pago_orden').val('Bs. 0.00');

    cargarDatosPagoOrden();

    const modalElement = document.getElementById('modalPagoOrden');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    modal.show();
}

function limpiarFormularioPagoOrden()
{
    $('#caja_id_pago').html(`
        <option value="">
            Seleccione una caja
        </option>
    `);

    $('#tipo_pago_orden').val('');
    $('#monto_recibido_orden').val('');
    $('#cambio_pago_orden').val('Bs. 0.00');
    $('#descripcion_pago_orden').val('');

    $('#erroresPagoOrden')
        .addClass('d-none')
        .html('');

    $('#btnGuardarPagoOrden')
        .prop('disabled', false);
}

function cargarDatosPagoOrden()
{
    $.ajax({
        url: "{{ route('pagos.crear') }}",
        type: "GET",
        success: function(response) {
            console.log('DATOS PAGO:', response);
            if (!response.estado) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message ??
                        'No se pudieron cargar los datos del pago.'
                });
                return;
            }
            let opcionesCaja = `
                <option value="">
                    Seleccione una caja
                </option>
            `;
            if (response.cajas && response.cajas.length > 0) {
                response.cajas.forEach(function(caja) {
                    opcionesCaja += `
                        <option value="${caja.id}">
                            Caja #${caja.id}
                        </option>
                    `;
                });

            } else {
                opcionesCaja = `
                    <option value="">
                        No hay cajas abiertas
                    </option>
                `;
            }
            $('#caja_id_pago').html(opcionesCaja);
        },

        error: function(xhr) {
            console.error('ERROR CARGANDO PAGO:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message ??
                    'No se pudieron cargar los datos del pago.'
            });
        }
    });
}

$(document).on(
    'input change',
    '#monto_recibido_orden, #tipo_pago_orden',
    function() {
        const tipo = $('#tipo_pago_orden').val();
        const montoRecibido = parseFloat(
            $('#monto_recibido_orden').val()
        ) || 0;
        const saldoPendiente = parseFloat(
            '{{ number_format($orden->saldo_pendiente, 2, '.', '') }}'
        ) || 0;
        let cambio = 0;
        if (tipo === 'EFECTIVO') {
            cambio = Math.max( 0, montoRecibido - saldoPendiente );
            $('#cambio_pago_orden').val(
                'Bs. ' + cambio.toFixed(2)
            );
        } else {
            $('#cambio_pago_orden').val(
                'Bs. 0.00'
            );
        }
    }
);

$(document).on('submit', '#formPagoOrden', function(e) {
    e.preventDefault();
    const btn = $('#btnGuardarPagoOrden');
    const ordenId = $('#orden_servicio_id_pago').val();
    const cajaId = $('#caja_id_pago').val();
    const tipoPago = $('#tipo_pago_orden').val();

let montoRecibido = parseFloat(
    $('#monto_recibido_orden').val()
) || 0;

const saldoPendiente = parseFloat(
    '{{ number_format($orden->saldo_pendiente, 2, '.', '') }}'
) || 0;

const descripcion =
    $('#descripcion_pago_orden').val();
    $('#erroresPagoOrden')
        .addClass('d-none')
        .html('');

    if (!cajaId) {
        $('#erroresPagoOrden')
            .removeClass('d-none')
            .html('Debe seleccionar una caja.');
        return;
    }

    if (!tipoPago) {
        $('#erroresPagoOrden')
            .removeClass('d-none')
            .html('Debe seleccionar el tipo de pago.');
        return;
    }

   if (montoRecibido <= 0) {
    $('#erroresPagoOrden')
        .removeClass('d-none')
        .html('El monto recibido debe ser mayor a cero.');
    return;
}

    btn.prop('disabled', true);
    $.ajax({
        url: "{{ route('pagos.guardar') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            orden_servicio_id: ordenId,
            caja_id: cajaId,
            tipo_pago: tipoPago,
            monto_recibido: montoRecibido,
            descripcion: descripcion
        },

        success: function(response) {
            if (response.estado) {
                bootstrap.Modal
                    .getInstance(
                        document.getElementById('modalPagoOrden')
                    )
                    .hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Pago registrado',
                    text: response.message,
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {

                    location.reload();
                });
            } else {
                $('#erroresPagoOrden')
                    .removeClass('d-none')
                    .html(response.message);

                btn.prop('disabled', false);
            }
        },

        error: function(xhr) {
            console.error(
                'RESPONSE:',
                xhr.responseText
            );

            $('#erroresPagoOrden')
                .removeClass('d-none')
                .html(
                    xhr.responseJSON?.message ??
                    'No se pudo registrar el pago.'
                );
            btn.prop('disabled', false);
        }

    });

});

function iniciarReparacion(id) {
    Swal.fire({
        title: '¿Iniciar reparación?',
        text: 'La orden pasará al estado EN REPARACIÓN.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, iniciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }
        $.ajax({
            url: "{{ url('/ordenServicio') }}/" + id + "/reparacion/iniciar",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.estado) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reparación iniciada',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },

            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message ??
                        'No se pudo iniciar la reparación.'
                });

            }
        });
    });
}

@if($orden->estado === 'LISTO')
const numeroOrdenEntrega = @json($orden->numero_orden);
const nombreClienteEntrega = @json(trim(($orden->vehiculo?->cliente?->user?->nombres ?? '') . ' ' . ($orden->vehiculo?->cliente?->user?->ap_paterno ?? '') . ' ' . ($orden->vehiculo?->cliente?->user?->ap_materno ?? '')));
const nombreVehiculoEntrega = @json(trim(($orden->vehiculo?->marca?->nombre ?? '') . ' ' . ($orden->vehiculo?->modelo ?? '')));
const placaVehiculoEntrega = @json($orden->vehiculo?->placa ?? '-');

function formatoMonedaEntrega(valor)
{
    return Number(valor ?? 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$('#btnVerificarDeuda').on('click', function() {
    const btn = $(this);
    btn.prop('disabled', true);
    $('#resultadoVerificacionEntrega').removeClass('alert-warning alert-danger alert-success').addClass('alert-info').text('Consultando pagos registrados...');
    $('#btnRealizarEntrega').prop('disabled', true);

    $.get("{{ route('ordenServicio.verificarDeuda', $orden->id) }}")
        .done(function(response) {
            const resumen = response.data;
            $('#totalEntrega').text(formatoMonedaEntrega(resumen.total));
            $('#pagadoEntrega').text(formatoMonedaEntrega(resumen.pagado));
            $('#saldoEntrega').text(formatoMonedaEntrega(resumen.saldo));

            if (resumen.motivo) {
                $('#estadoPagoEntrega').text('NO VERIFICADO');
                $('#resultadoVerificacionEntrega').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').text(resumen.motivo);
                return;
            }

            if (resumen.puede_entregar) {
                $('#estadoPagoEntrega').text('PAGADO');
                $('#resultadoVerificacionEntrega').removeClass('alert-info alert-danger alert-warning').addClass('alert-success').text('Pago verificado. La orden está habilitada para entrega.');
                $('#btnRealizarEntrega').prop('disabled', false);
            } else {
                $('#estadoPagoEntrega').text('CON SALDO PENDIENTE');
                $('#resultadoVerificacionEntrega').removeClass('alert-info alert-danger alert-success').addClass('alert-warning').text('No se puede realizar la entrega mientras exista un saldo pendiente.');
            }
        })
        .fail(function(xhr) {
            $('#resultadoVerificacionEntrega').removeClass('alert-info alert-success alert-warning').addClass('alert-danger').text(xhr.responseJSON?.message ?? 'No fue posible verificar los pagos.');
        })
        .always(function() {
            btn.prop('disabled', false).html('<i class="fa fa-rotate me-2"></i>Verificar nuevamente');
        });
});

$('#btnRealizarEntrega').on('click', function() {
    const escapeHtml = function(value) {
        return $('<div>').text(value ?? '').html();
    };
    const total = $('#totalEntrega').text();
    const pagado = $('#pagadoEntrega').text();
    const saldo = $('#saldoEntrega').text();

    Swal.fire({
        title: '¿Confirmar entrega?',
        html: `La orden no podrá volver a estado LISTO después de confirmar la entrega.<br><br><strong>Cliente:</strong> ${escapeHtml(nombreClienteEntrega)}<br><strong>Vehículo:</strong> ${escapeHtml(nombreVehiculoEntrega)}<br><strong>Placa:</strong> ${escapeHtml(placaVehiculoEntrega)}<br><strong>Orden:</strong> ${escapeHtml(numeroOrdenEntrega)}<br><br><strong>Total:</strong> Bs. ${escapeHtml(total)}<br><strong>Pagado:</strong> Bs. ${escapeHtml(pagado)}<br><strong>Saldo:</strong> Bs. ${escapeHtml(saldo)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar entrega',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        const btn = $('#btnRealizarEntrega');
        btn.prop('disabled', true);
        $.ajax({
            url: "{{ route('ordenServicio.entregar', $orden->id) }}",
            type: 'PUT',
            headers: { 'Accept': 'application/json' },
            data: { _token: "{{ csrf_token() }}" }
        }).done(function(response) {
            Swal.fire({
                icon: 'success',
                title: '¡Entrega registrada!',
                text: response.message,
                showCancelButton: true,
                confirmButtonText: 'Ver boleta PDF',
                cancelButtonText: 'Continuar',
                allowOutsideClick: false
            }).then(function(result) {
                if (result.isConfirmed && response.data?.boleta_url) {
                    window.open(response.data.boleta_url, '_blank');
                }

                window.location.reload();
            });
        }).fail(function(xhr) {
            const errores = Object.values(xhr.responseJSON?.errors ?? {}).flat();
            Swal.fire({
                icon: 'error',
                title: 'No se pudo registrar la entrega',
                text: xhr.responseJSON?.message ?? errores[0] ?? 'Verifique nuevamente el saldo de la orden.'
            });
            btn.prop('disabled', false);
        });
    });
});
@endif


@if($orden->estado === 'EN_REPARACION' && $orden->reparacion?->estado !== 'FINALIZADA')

function cargarDatosReparacion()
{
    $.ajax({
        url: "{{ route('reparaciones.datos', $orden->id) }}",
        type: "GET",
        success: function(response) {
            if (!response.estado) {
                mostrarErrorReparacion(
                    response.message ??
                    'No se pudieron cargar los datos de reparación.'
                );
                return;
            }

            const reparacion = response.data.reparacion;
            const tecnicos = response.data.tecnicos;
            const selectTecnico = $('#tecnico_id');
            selectTecnico.html(`
                <option value="">
                    Seleccione un técnico
                </option>
            `);

            tecnicos.forEach(function(tecnico) {
                const nombre = [
                    tecnico.nombres,
                    tecnico.ap_paterno,
                    tecnico.ap_materno
                ]
                .filter(Boolean)
                .join(' ');
                selectTecnico.append($('<option>', {
                    value: tecnico.id,
                    text: nombre
                }));

            });

            if (reparacion) {
                if (reparacion.tecnico_id) {
                    selectTecnico.val(
                        reparacion.tecnico_id
                    );
                }
                const tecnico = reparacion.tecnico;
                $('#tecnicoActual').text(tecnico
                    ? [tecnico.nombres, tecnico.ap_paterno, tecnico.ap_materno].filter(Boolean).join(' ')
                    : 'Sin asignar');

                $('#trabajos_realizados').val(
                    reparacion.trabajos_realizados ?? ''
                );

                $('#observaciones_reparacion').val(
                    reparacion.observaciones ?? ''
                );

                $('#fechaInicioActual').text(formatearFechaReparacion(reparacion.fecha_inicio));
                $('#estadoReparacionActual')
                    .text(reparacion.estado ?? 'EN_PROCESO')
                    .toggleClass('badge-light-warning', reparacion.estado !== 'FINALIZADA')
                    .toggleClass('badge-light-success', reparacion.estado === 'FINALIZADA');

                if (reparacion.estado === 'FINALIZADA') {

                    $('#tecnico_id').prop('disabled', true);
                    $('#trabajos_realizados').prop('readonly', true);
                    $('#observaciones_reparacion').prop('readonly', true);
                    $('#btnGuardarReparacion').hide();
                    $('#btnFinalizarReparacion').hide();
                }
            }
        },

        error: function(xhr) {
            mostrarErrorReparacion(mensajeErrorReparacion(xhr));
        }
    });
}

function mostrarErrorReparacion(mensaje)
{
    $('#erroresReparacion')
        .removeClass('d-none')
        .html(mensaje);
}

function escaparHtml(valor)
{
    return $('<div>').text(valor ?? '').html();
}

function formatearFechaReparacion(valor)
{
    if (!valor) return '-';
    const fecha = new Date(String(valor).replace(' ', 'T'));
    return Number.isNaN(fecha.getTime()) ? '-' : fecha.toLocaleString('es-BO');
}

function mensajeErrorReparacion(xhr)
{
    const errores = xhr.responseJSON?.errors;
    if (errores) {
        return Object.values(errores).flat().map(escaparHtml).join('<br>');
    }
    if (xhr.status === 422) {
        return escaparHtml(xhr.responseJSON?.message ?? 'Revise los datos ingresados.');
    }

    return 'No fue posible completar la operación. La solicitud fue revertida; intente nuevamente.';
}

function buscarCatalogoReparacion(termino = '')
{
    $.get("{{ route('reparaciones.productos', $orden->id) }}", { q: termino })
        .done(function(response) {
            const select = $('#productoReparacion');
            select.html('<option value="">Seleccione un producto</option>');

            response.data.forEach(function(producto) {
                const codigo = producto.codigo ? producto.codigo + ' - ' : '';
                const marca = producto.marca?.nombre ? ` · ${producto.marca.nombre}` : '';
                const label = `${codigo}${producto.nombre}${marca} (${producto.cantidad} ${producto.unidad_medida})`;
                select.append(
                    $('<option>', {
                        value: producto.id,
                        text: label,
                        'data-stock': producto.cantidad,
                        'data-unidad': producto.unidad_medida
                    })
                );
            });
        })
        .fail(function(xhr) {
            mostrarErrorReparacion(mensajeErrorReparacion(xhr));
        });
}

function cargarProductosUtilizados()
{
    $.get("{{ route('reparaciones.detalles', $orden->id) }}")
        .done(function(response) {
            const detalles = response.data.detalles;
            const movimientos = response.data.movimientos;
            const tablaDetalles = $('#productosUtilizadosBody');
            const tablaMovimientos = $('#movimientosReparacionBody');

            window.detallesReparacionPorProducto = {};
            detalles.forEach(function(detalle) {
                window.detallesReparacionPorProducto[detalle.producto_id] = detalle;
            });

            if (!detalles.length) {
                tablaDetalles.html('<tr><td colspan="6" class="text-center text-muted">Aún no se registraron productos.</td></tr>');
            } else {
                tablaDetalles.html(detalles.map(function(detalle) {
                    const producto = detalle.producto ?? {};
                    return `<tr data-id="${detalle.id}" data-producto="${producto.id ?? detalle.producto_id}" data-nombre="${escaparHtml(producto.nombre ?? 'Producto eliminado')}" data-unidad="${escaparHtml(producto.unidad_medida ?? '')}" data-cantidad="${escaparHtml(detalle.cantidad)}" data-stock="${escaparHtml(producto.cantidad ?? 0)}" data-observacion="${escaparHtml(detalle.observaciones ?? '')}">
                        <td>${escaparHtml(producto.nombre ?? 'Producto eliminado')}</td>
                        <td>${escaparHtml(producto.unidad_medida ?? '-')}</td>
                        <td class="text-end fw-semibold">${escaparHtml(detalle.cantidad)}</td>
                        <td class="text-end">${escaparHtml(producto.cantidad ?? '-')}</td>
                        <td>${escaparHtml(detalle.observaciones ?? '-')}</td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-sm btn-light-primary btnEditarDetalleReparacion" title="Editar consumo"><i class="fa fa-pen"></i> Editar</button>
                            <button type="button" class="btn btn-sm btn-light-danger btnEliminarDetalleReparacion" title="Devolver y eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                }).join(''));
            }

            if (!movimientos.length) {
                tablaMovimientos.html('<tr><td colspan="8" class="text-center text-muted">No hay movimientos de inventario.</td></tr>');
            } else {
                tablaMovimientos.html(movimientos.map(function(movimiento) {
                    const badge = movimiento.tipo === 'SALIDA' ? 'badge-light-danger' : 'badge-light-success';
                    const fecha = formatearFechaReparacion(movimiento.fecha);
                    return `<tr>
                        <td>${escaparHtml(fecha)}</td>
                        <td>${escaparHtml(movimiento.producto?.nombre ?? 'Producto eliminado')}</td>
                        <td><span class="badge ${badge}">${escaparHtml(movimiento.tipo)}</span></td>
                        <td>${escaparHtml(movimiento.motivo)}</td>
                        <td class="text-end">${escaparHtml(movimiento.cantidad)}</td>
                        <td class="text-end">${escaparHtml(movimiento.stock_anterior)}</td>
                        <td class="text-end">${escaparHtml(movimiento.stock_nuevo)}</td>
                        <td>${escaparHtml(movimiento.usuario_creador?.nombres ?? '-')}</td>
                    </tr>`;
                }).join(''));
            }
        })
        .fail(function(xhr) {
            mostrarErrorReparacion(mensajeErrorReparacion(xhr));
        });
}

$('#modalAgregarProductoReparacion').on('show.bs.modal', function() {
    $('#buscarProductoReparacion').val('');
    $('#cantidadProductoReparacion, #observacionProductoReparacion').val('');
    $('#errorModalProductoReparacion').addClass('d-none').empty();
    buscarCatalogoReparacion();
});

$('#buscarProductoReparacion').on('input', function() {
    clearTimeout(window.temporizadorBusquedaReparacion);
    const termino = $(this).val();
    window.temporizadorBusquedaReparacion = setTimeout(function() {
        buscarCatalogoReparacion(termino);
    }, 250);
});

$('#productoReparacion').on('change', function() {
    const opcion = $(this).find(':selected');
    const stock = Number(opcion.data('stock'));
    const unidad = opcion.data('unidad') ?? '-';
    const detalleExistente = window.detallesReparacionPorProducto?.[$(this).val()];
    $('#stockProductoReparacion').text(
        $(this).val() ? `Stock disponible: ${stock} | Unidad: ${unidad}` : 'Stock disponible: - | Unidad: -'
    );
    $('#cantidadProductoReparacion').attr('max', stock || '');
    $('#acumuladoProductoReparacion').text(detalleExistente
        ? `Ya se utilizaron ${detalleExistente.cantidad} ${unidad}. La cantidad agregada se sumará; el inventario descontará solo lo nuevo.`
        : 'Si agrega el mismo producto más adelante, se acumulará en esta línea.');
});

$('#btnAgregarProductoReparacion').on('click', function() {
    const btn = $(this);
    const productoId = $('#productoReparacion').val();
    const cantidad = Number($('#cantidadProductoReparacion').val());
    const opcion = $('#productoReparacion').find(':selected');
    const disponible = Number(opcion.data('stock'));
    const unidad = opcion.data('unidad') ?? '';
    const error = $('#errorModalProductoReparacion');

    error.addClass('d-none').empty();
    if (!productoId || !Number.isFinite(cantidad) || cantidad <= 0) {
        error.removeClass('d-none').text('Seleccione un producto e indique una cantidad mayor que cero.');
        return;
    }
    if (cantidad > disponible) {
        error.removeClass('d-none').text(`Stock insuficiente. Disponible: ${disponible} ${unidad}; solicitado: ${cantidad} ${unidad}.`);
        return;
    }

    btn.prop('disabled', true);
    $.ajax({
        url: "{{ route('reparaciones.detalles.store', $orden->id) }}",
        type: 'POST',
        headers: { 'Accept': 'application/json' },
        data: {
            _token: "{{ csrf_token() }}",
            producto_id: productoId,
            cantidad: cantidad,
            observaciones: $('#observacionProductoReparacion').val()
        }
    }).done(function() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAgregarProductoReparacion')).hide();
        cargarProductosUtilizados();
        buscarCatalogoReparacion();
    }).fail(function(xhr) {
        error.removeClass('d-none').html(mensajeErrorReparacion(xhr));
    }).always(function() {
        btn.prop('disabled', false);
    });
});

$('#productosUtilizadosBody').on('click', '.btnEditarDetalleReparacion', function() {
    const fila = $(this).closest('tr');
    const cantidadActual = Number(fila.attr('data-cantidad'));
    const stockActual = Number(fila.attr('data-stock'));
    $('#detalleProductoReparacionId').val(fila.data('id'));
    $('#editarProductoNombre').text(`${fila.attr('data-nombre')} · ${fila.attr('data-unidad')}`);
    $('#editarCantidadProducto').val(cantidadActual).attr('max', stockActual + cantidadActual);
    $('#editarObservacionProducto').val(fila.attr('data-observacion'));
    $('#impactoCantidadProducto').text('Sin cambios en el inventario.');
    $('#errorEditarProductoReparacion').addClass('d-none').empty();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarProductoReparacion')).show();
});

$('#editarCantidadProducto').on('input', function() {
    const fila = $(`#productosUtilizadosBody tr[data-id="${$('#detalleProductoReparacionId').val()}"]`);
    const anterior = Number(fila.attr('data-cantidad'));
    const nueva = Number($(this).val());
    const diferencia = Math.round((nueva - anterior) * 100) / 100;
    let mensaje = 'Sin cambios en el inventario.';
    if (diferencia > 0) mensaje = `Se utilizarán ${diferencia} ${fila.attr('data-unidad')} adicionales del inventario.`;
    if (diferencia < 0) mensaje = `Se devolverán ${Math.abs(diferencia)} ${fila.attr('data-unidad')} al inventario.`;
    $('#impactoCantidadProducto').text(mensaje);
});

$('#btnGuardarEdicionProducto').on('click', function() {
    const btn = $(this);
    const detalleId = $('#detalleProductoReparacionId').val();
    const error = $('#errorEditarProductoReparacion');
    error.addClass('d-none').empty();
    btn.prop('disabled', true);
    $.ajax({
        url: `{{ url('/reparaciones') }}/{{ $orden->id }}/detalles/${detalleId}`,
        type: 'PUT',
        headers: { 'Accept': 'application/json' },
        data: {
            _token: "{{ csrf_token() }}",
            cantidad: $('#editarCantidadProducto').val(),
            observaciones: $('#editarObservacionProducto').val()
        }
    }).done(function() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarProductoReparacion')).hide();
        cargarProductosUtilizados();
        buscarCatalogoReparacion();
    }).fail(function(xhr) {
        error.removeClass('d-none').html(mensajeErrorReparacion(xhr));
    }).always(function() {
        btn.prop('disabled', false);
    });
});

$('#productosUtilizadosBody').on('click', '.btnEliminarDetalleReparacion', function() {
    const fila = $(this).closest('tr');
    const detalleId = fila.data('id');
    const nombre = fila.attr('data-nombre');
    const cantidad = fila.attr('data-cantidad');
    const unidad = fila.attr('data-unidad');
    Swal.fire({
        title: '¿Desea eliminar este producto de la reparación?',
        html: `${escaparHtml(nombre)}<br>Cantidad: ${escaparHtml(cantidad)} ${escaparHtml(unidad)}<br><br>Esto devolverá la cantidad utilizada al inventario.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Devolver y eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(resultado) {
        if (!resultado.isConfirmed) return;
        $.ajax({
            url: `{{ url('/reparaciones') }}/{{ $orden->id }}/detalles/${detalleId}`,
            type: 'DELETE',
            headers: { 'Accept': 'application/json' },
            data: { _token: "{{ csrf_token() }}" }
        }).done(function() {
            cargarProductosUtilizados();
            buscarCatalogoReparacion();
        }).fail(function(xhr) {
            mostrarErrorReparacion(mensajeErrorReparacion(xhr));
        });
    });
});

function cargarAvancesReparacion()
{
    $.get("{{ route('reparaciones.avances', $orden->id) }}").done(function(response) {
        const avances = response.data ?? [];
        if (!avances.length) {
            $('#avancesReparacionTimeline').html('<div class="text-muted">Aún no se registran avances.</div>');
            return;
        }
        $('#avancesReparacionTimeline').html(avances.map(function(avance) {
            const actor = avance.usuario ?? avance.tecnico;
            const nombre = actor ? [actor.nombres, actor.ap_paterno, actor.ap_materno].filter(Boolean).join(' ') : 'Usuario no disponible';
            return `<div class="repair-timeline-item"><div class="repair-timeline-date">${escaparHtml(formatearFechaReparacion(avance.fecha))}</div><div class="repair-timeline-content"><div class="fw-bold">${escaparHtml(avance.descripcion)}</div><div class="text-muted small">${escaparHtml(nombre)}</div>${avance.observaciones ? `<div class="mt-2">${escaparHtml(avance.observaciones)}</div>` : ''}</div></div>`;
        }).join(''));
    }).fail(function(xhr) {
        mostrarErrorReparacion(mensajeErrorReparacion(xhr));
    });
}

$('#btnGuardarAvanceReparacion').on('click', function() {
    const btn = $(this);
    const error = $('#errorAvanceReparacion');
    error.addClass('d-none').empty();
    btn.prop('disabled', true);
    $.ajax({
        url: "{{ route('reparaciones.avances.store', $orden->id) }}",
        type: 'POST',
        headers: { 'Accept': 'application/json' },
        data: {
            _token: "{{ csrf_token() }}",
            descripcion: $('#descripcionAvanceReparacion').val(),
            observaciones: $('#observacionAvanceReparacion').val()
        }
    }).done(function() {
        $('#descripcionAvanceReparacion, #observacionAvanceReparacion').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAvanceReparacion')).hide();
        cargarAvancesReparacion();
    }).fail(function(xhr) {
        error.removeClass('d-none').html(mensajeErrorReparacion(xhr));
    }).always(function() {
        btn.prop('disabled', false);
    });
});

function guardarDatosReparacion()
{
    return $.ajax({
        url: "{{ route('reparaciones.guardar', $orden->id) }}",
        type: 'PUT',
        headers: { 'Accept': 'application/json' },
        data: {
            _token: "{{ csrf_token() }}",
            tecnico_id: $('#tecnico_id').val(),
            trabajos_realizados: $('#trabajos_realizados').val(),
            observaciones: $('#observaciones_reparacion').val()
        }
    });
}

$('#btnGuardarReparacion').on('click', function() {
    const btn = $(this);
    $('#erroresReparacion').addClass('d-none').empty();
    btn.prop('disabled', true);
    guardarDatosReparacion()
        .done(function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Reparación guardada',
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            });
            const tecnico = response.data?.tecnico;
            $('#tecnicoActual').text(tecnico
                ? [tecnico.nombres, tecnico.ap_paterno, tecnico.ap_materno].filter(Boolean).join(' ')
                : 'Sin asignar');
        })
        .fail(function(xhr) {
            mostrarErrorReparacion(mensajeErrorReparacion(xhr));
        })
        .always(function() {
            btn.prop('disabled', false);
        });
});


$('#btnFinalizarReparacion').on('click', function() {
    Swal.fire({
        title: '¿Está seguro de finalizar esta reparación?',
        text: 'Una vez finalizada no podrá modificar los productos utilizados, cantidades ni avances.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) {
            return;
        }
        const btn = $('#btnFinalizarReparacion');
        btn.prop('disabled', true);
        guardarDatosReparacion()
            .done(function() {
                $.ajax({
                    url: "{{ route('reparaciones.finalizar', $orden->id) }}",
                    type: 'PUT',
                    headers: { 'Accept': 'application/json' },
                    data: { _token: "{{ csrf_token() }}" }
                }).done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reparación finalizada',
                        text: response.message,
                        confirmButtonText: 'Continuar'
                    }).then(function() {
                        location.reload();
                    });
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se puede finalizar',
                        html: mensajeErrorReparacion(xhr),
                        confirmButtonText: 'Entendido'
                    });
                    btn.prop('disabled', false);
                });
            })
            .fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se puede guardar la reparación',
                    html: mensajeErrorReparacion(xhr)
                });
                btn.prop('disabled', false);
            });
    });
});

$(document).ready(function() {
    cargarDatosReparacion();
    cargarProductosUtilizados();
    cargarAvancesReparacion();
});
@endif


</script>


@if($orden->cotizacionActual && $orden->cotizacionActual->estado === 'PENDIENTE')

<script>
    const btnAprobarCotizacion = document.getElementById('btnAprobarCotizacion');
    if (btnAprobarCotizacion) {
        btnAprobarCotizacion.addEventListener('click', function () {
            Swal.fire({
                title: '¿Aprobar cotización?',
                text: 'La orden pasará al estado AUTORIZADO.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (!result.isConfirmed) {
                    return;
                }

                btnAprobarCotizacion.disabled = true;
                fetch(
                    "{{ route('ordenServicio.cotizacion.aprobar', $orden->id) }}",
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }
                )
                .then(response => response.json())
                .then(data => {

                    if (data.estado) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cotización aprobada',
                            text: data.mensaje || 'La orden ha sido autorizada correctamente.',
                            confirmButtonText: 'Continuar'
                        }).then(() => {
                            location.reload();
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje ||
                                'No se pudo aprobar la cotización.'
                        });
                        btnAprobarCotizacion.disabled = false;
                    }

                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al aprobar la cotización.'
                    });

                    btnAprobarCotizacion.disabled = false;
                });
            });
        });
    }

   
const btnEnviarCotizacion = document.getElementById(
    'btnEnviarCotizacionWhatsApp'
);

if (btnEnviarCotizacion) {
    btnEnviarCotizacion.addEventListener('click', async function () {
        const telefono = this.dataset.telefono;
        const urlPdf = this.dataset.pdfUrl;
        const numeroOrden = this.dataset.orden;

        const mensaje =
            `Hola, le compartimos la cotización de la orden de servicio #${numeroOrden}. Por favor, revise el documento adjunto.`;

        const urlWhatsApp =
            `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;

        const boton = this;
        const textoOriginal = boton.innerHTML;

        // Compartir archivos requiere soporte del navegador.
        if (
            !navigator.share ||
            !navigator.canShare
        ) {
            window.open(urlWhatsApp, '_blank');
            window.open(urlPdf, '_blank');

            Swal.fire({
                icon: 'info',
                title: 'Adjunta el PDF',
                text: 'Descarga la cotización y adjúntala en el chat de WhatsApp antes de enviarla.'
            });

            return;
        }

        boton.disabled = true;
        boton.innerHTML =
            '<i class="fas fa-spinner fa-spin me-2"></i>Preparando PDF...';

        try {
            const respuesta = await fetch(urlPdf, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/pdf'
                }
            });

            if (!respuesta.ok) {
                throw new Error(
                    'No se pudo obtener el PDF de la cotización.'
                );
            }

            const blob = await respuesta.blob();

            if (blob.type && !blob.type.includes('pdf')) {
                throw new Error(
                    'La ruta no devolvió un archivo PDF.'
                );
            }

            const archivo = new File(
                [blob],
                `cotizacion-${numeroOrden}.pdf`,
                { type: 'application/pdf' }
            );

            if (!navigator.canShare({ files: [archivo] })) {
                throw new Error(
                    'El navegador no admite compartir este PDF.'
                );
            }

            await navigator.share({
                files: [archivo],
                title: `Cotización ${numeroOrden}`,
                text: mensaje
            });

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error(
                'Error al compartir cotización:',
                error
            );

            window.open(urlPdf, '_blank');
            window.open(urlWhatsApp, '_blank');

            Swal.fire({
                icon: 'warning',
                title: 'No se pudo adjuntar automáticamente',
                text: 'Descarga el PDF y adjúntalo manualmente en WhatsApp.'
            });

        } finally {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        }
    });
}


</script>
@endif
@endsection