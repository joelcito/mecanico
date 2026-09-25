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
                            'estado' => ['AUTORIZADO', 'EN_REPARACION', 'LISTO'],
                        ],
                        [
                            'nombre' => 'Entrega',
                            'estado' => ['ENTREGADO'],
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
                    } elseif (in_array($estado, ['AUTORIZADO', 'EN_REPARACION', 'LISTO'])) {
                        $etapaActual = 4;
                    } elseif (in_array($estado, ['ENTREGADO'])) {
                        $etapaActual = 5;
                    } else {
                        $etapaActual = 0;
                    }
                @endphp
                <div class="step-container">
                    @foreach($etapas as $index => $etapa)
                        @if($index < $etapaActual)
                            <div class="step step-completed">
                                <div class="step-circle">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="step-title">
                                    {{ $etapa['nombre'] }}
                                </div>
                                <div class="step-subtitle">
                                    Completado
                                </div>
                            </div>

                        @elseif($index === $etapaActual)
                            <div class="step step-active">
                                <div class="step-circle">
                                    {{ $index + 1 }}
                                </div>

                                <div class="step-title">
                                    {{ $etapa['nombre'] }}
                                </div>

                                <div class="step-subtitle">
                                    Siguiente paso
                                </div>
                            </div>

                        @else

                            <div class="step step-locked">
                                <div class="step-circle">
                                    {{ $index + 1 }}
                                </div>

                                <div class="step-title">
                                    {{ $etapa['nombre'] }}
                                </div>
                                <div class="step-subtitle">
                                    Bloqueado
                                </div>
                            </div>
                        @endif
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

         @if($orden->cotizacionActual)
            <div class="card shadow-sm mb-5">
                <div class="card-header">
                    <h3 class="card-title fw-bold">
                        <i class="fa fa-file-invoice-dollar text-primary me-2"></i>
                        Cotización
                    </h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-warning">
                            {{ $orden->cotizacionActual->estado }}
                        </span>
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

            cambio = Math.max(
                0,
                montoRecibido - saldoPendiente
            );

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

    console.log('SUBMIT PAGO EJECUTADO');

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

            console.log('RESPUESTA GUARDAR:', response);

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

            console.error('ERROR GUARDAR:', xhr);

            console.error(
                'STATUS:',
                xhr.status
            );

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
</script>


@if($orden->cotizacionActual && $orden->cotizacionActual->estado === 'PENDIENTE')

<script>

    const btnAprobarCotizacion =
        document.getElementById('btnAprobarCotizacion');

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

                    if (data.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Cotización aprobada',
                            text: 'La orden ha sido autorizada correctamente.',
                            confirmButtonText: 'Continuar'
                        }).then(() => {
                            location.reload();
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message ??
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

    const btnRechazarCotizacion = document.getElementById('btnRechazarCotizacion');
    if (btnRechazarCotizacion) {
        btnRechazarCotizacion.addEventListener('click', function () {
            const observacion = document.getElementById('observacionRechazo').value.trim();
            if (!observacion) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo requerido',
                    text: 'Debe ingresar el motivo del rechazo.'
                });
                return;
            }
            btnRechazarCotizacion.disabled = true;
            fetch(
                "{{ route('ordenServicio.cotizacion.rechazar', $orden->id) }}",
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        observacion_respuesta: observacion
                    })
                }
            )
            .then(response => response.json())
            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Cotización rechazada',
                        text: 'La cotización ha sido rechazada.',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message ??
                            'No se pudo rechazar la cotización.'
                    });
                    btnRechazarCotizacion.disabled = false;
                }

            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al rechazar la cotización.'
                });

                btnRechazarCotizacion.disabled = false;
            });
        });
    }
</script>
@endif
@endsection