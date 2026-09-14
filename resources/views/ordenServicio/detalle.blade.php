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
    <div
        id="kt_app_content_container"
        class="app-container container-xxlg"
    >

        {{-- ENCABEZADO --}}
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

                        <a
                            href="{{ route('ordenServicio.listado') }}"
                            class="btn btn-light btn-sm"
                        >
                            <i class="fa fa-arrow-left"></i>
                            Volver al listado
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- FLUJO --}}
        <div class="card shadow-sm mb-5">

            <div class="card-body">

                <div class="step-container">

                    {{-- RECEPCIÓN --}}
                    <div class="step step-completed">

                        <div class="step-circle">

                            <i class="fa fa-check"></i>

                        </div>

                        <div class="step-title">
                            Recepción
                        </div>

                        <div class="step-subtitle">
                            Completado
                        </div>

                    </div>


                    {{-- INSPECCIÓN --}}
                    <div class="step step-active">

                        <div class="step-circle">
                            2
                        </div>

                        <div class="step-title">
                            Inspección
                        </div>

                        <div class="step-subtitle">
                            Siguiente paso
                        </div>

                    </div>


                    {{-- DIAGNÓSTICO --}}
                    <div class="step step-locked">

                        <div class="step-circle">
                            3
                        </div>

                        <div class="step-title">
                            Diagnóstico
                        </div>

                        <div class="step-subtitle">
                            Bloqueado
                        </div>

                    </div>


                    {{-- COTIZACIÓN --}}
                    <div class="step step-locked">

                        <div class="step-circle">
                            4
                        </div>

                        <div class="step-title">
                            Cotización
                        </div>

                        <div class="step-subtitle">
                            Bloqueado
                        </div>

                    </div>


                    {{-- REPARACIÓN --}}
                    <div class="step step-locked">

                        <div class="step-circle">
                            5
                        </div>

                        <div class="step-title">
                            Reparación
                        </div>

                        <div class="step-subtitle">
                            Bloqueado
                        </div>

                    </div>


                    {{-- ENTREGA --}}
                    <div class="step step-locked">

                        <div class="step-circle">
                            6
                        </div>

                        <div class="step-title">
                            Entrega
                        </div>

                        <div class="step-subtitle">
                            Bloqueado
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- INFORMACIÓN DE LA ORDEN --}}
        <div class="card shadow-sm mb-5">

            <div class="card-header">

                <h3 class="card-title fw-bold">

                    <i class="fa fa-info-circle text-primary me-2"></i>

                    Información de la orden

                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    {{-- CLIENTE --}}
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


                    {{-- VEHÍCULO --}}
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


                    {{-- PLACA --}}
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


                    {{-- FECHA --}}
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


                    {{-- KILOMETRAJE --}}
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


                    {{-- COMBUSTIBLE --}}
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


                    {{-- MOTIVO --}}
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


                {{-- OBSERVACIONES --}}
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


        {{-- SIGUIENTE PASO --}}
        <div class="card shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h4 class="fw-bold mb-1">

                            Siguiente paso: Inspección

                        </h4>

                        <span class="text-muted">

                            Registre el estado actual del vehículo antes de iniciar el diagnóstico.

                        </span>

                    </div>


                    <button
                        type="button"
                        class="btn btn-primary"
                        disabled
                    >

                        <i class="fa fa-clipboard-check"></i>

                        Realizar inspección

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

</div>

@endsection
