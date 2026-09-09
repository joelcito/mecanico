@extends('layouts.app')
@section('css')
@endsection
@section('content')

    <div class="row g-5 gx-xl-10 mb-5">
        <div class="col-md-3">
            <div class="card card-flush h-md-100" style="background-color:#009EF7;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-car fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalVehiculos ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold fs-5">
                        Vehículos
                    </div>

                    <div class="text-white opacity-75 mt-2">
                        Registrados
                    </div>

                </div>
            </div>
        </div>


        {{-- CLIENTES --}}
        <div class="col-md-3">
            <div class="card card-flush h-md-100" style="background-color:#7239EA;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-users fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalClientes ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold fs-5">
                        Clientes
                    </div>

                    <div class="text-white opacity-75 mt-2">
                        Registrados
                    </div>

                </div>
            </div>
        </div>


        {{-- VEHÍCULOS EN REPARACIÓN --}}
        <div class="col-md-3">
            <div class="card card-flush h-md-100" style="background-color:#F1416C;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-screwdriver-wrench fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $vehiculosEnReparacion ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold fs-5">
                        En reparación
                    </div>

                    <div class="text-white opacity-75 mt-2">
                        Trabajos activos
                    </div>

                </div>
            </div>
        </div>


        {{-- INGRESOS DEL DÍA --}}
        <div class="col-md-3">
            <div class="card card-flush h-md-100" style="background-color:#50CD89;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-money-bill-wave fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        Bs {{ number_format($ingresosHoy ?? 0, 2) }}
                    </div>

                    <div class="text-white fw-semibold fs-5">
                        Ingresos de Hoy
                    </div>

                    <div class="text-white opacity-75 mt-2">
                        Total cobrado
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-8">
            <div class="card card-flush h-100">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold">
                            <i class="fa fa-screwdriver-wrench text-primary me-2"></i>
                            Trabajos en el Taller
                        </h3>
                    </div>

                    <div class="card-toolbar">

                        <a href="{{ url('/servicios') }}"
                           class="btn btn-sm btn-light-primary">

                            Ver todos

                            <i class="fa fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>


                <div class="card-body pt-5">
                    @forelse($trabajosEnTaller ?? [] as $trabajo)
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fa fa-car fs-2 text-primary"></i>
                                </span>
                            </div>


                            {{-- INFORMACIÓN --}}
                            <div class="flex-grow-1">

                                <span class="text-gray-800 fw-bold fs-6">

                                    {{ $trabajo->vehiculo ?? 'Vehículo' }}

                                </span>

                                <span class="text-muted d-block fs-7">

                                    Cliente:
                                    {{ $trabajo->cliente ?? 'Sin cliente' }}

                                </span>

                                <span class="text-muted d-block fs-7">

                                    Servicio:
                                    {{ $trabajo->servicio ?? 'No especificado' }}

                                </span>

                            </div>


                            {{-- ESTADO --}}
                            @php

                                $estado = strtolower($trabajo->estado ?? '');

                                $badge = match ($estado) {

                                    'pendiente' =>
                                        'badge-light-warning',

                                    'en proceso',
                                    'en_proceso' =>
                                        'badge-light-primary',

                                    'finalizado',
                                    'terminado' =>
                                        'badge-light-success',

                                    'cancelado' =>
                                        'badge-light-danger',

                                    default =>
                                        'badge-light-secondary',

                                };

                            @endphp


                            <span class="badge {{ $badge }}">

                                {{ $trabajo->estado ?? 'Pendiente' }}

                            </span>

                        </div>

                    @empty

                        <div class="text-center text-muted py-10">

                            <i class="fa fa-car fs-3x mb-5"></i>

                            <div class="fw-semibold">

                                No existen trabajos activos.

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- PRÓXIMAS CITAS --}}
        <div class="col-md-4">

            <div class="card card-flush h-100">

                <div class="card-header">

                    <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-calendar-check text-success me-2"></i>

                            Próximas Citas

                        </h3>

                    </div>

                </div>


                <div class="card-body pt-5">

                    @forelse($proximasCitas ?? [] as $cita)

                        <div class="d-flex align-items-center mb-7">

                            <div class="symbol symbol-45px me-4">

                                <span class="symbol-label bg-light-success">

                                    <i class="fa fa-calendar text-success"></i>

                                </span>

                            </div>


                            <div class="flex-grow-1">

                                <span class="text-gray-800 fw-bold d-block">

                                    {{ $cita->cliente ?? 'Cliente' }}

                                </span>

                                <span class="text-muted fs-7 d-block">

                                    {{ $cita->vehiculo ?? 'Vehículo' }}

                                </span>

                                <span class="text-primary fw-semibold fs-7">

                                    {{ $cita->hora ?? '' }}

                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="text-center text-muted py-10">

                            <i class="fa fa-calendar-xmark fs-3x mb-5"></i>

                            <div>

                                No hay citas próximas.

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


  
    <div class="row g-5 mb-5">

        <div class="col-md-4">

            <div class="card card-flush h-100">

                <div class="card-header">

                    <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-box-open text-warning me-2"></i>

                            Repuestos con Stock Bajo

                        </h3>

                    </div>

                </div>


                <div class="card-body pt-5">

                    @forelse($repuestosStockBajo ?? [] as $repuesto)

                        <div class="d-flex align-items-center mb-6">

                            <div class="symbol symbol-40px me-4">

                                <span class="symbol-label bg-light-warning">

                                    <i class="fa fa-box text-warning"></i>

                                </span>

                            </div>


                            <div class="flex-grow-1">

                                <span class="fw-bold text-gray-800 d-block">

                                    {{ $repuesto->nombre }}

                                </span>

                                <span class="text-muted fs-7">

                                    Mínimo:
                                    {{ $repuesto->stock_minimo ?? 0 }}

                                </span>

                            </div>


                            <span class="badge badge-light-danger">

                                {{ $repuesto->stock ?? 0 }}

                            </span>

                        </div>

                    @empty

                        <div class="text-center py-8">

                            <i class="fa fa-circle-check fs-3x text-success mb-4"></i>

                            <div class="text-muted">

                                Stock disponible correctamente.

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- SERVICIOS REALIZADOS --}}
        <div class="col-md-4">

            <div class="card card-flush h-100">

                <div class="card-header">

                    <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-wrench text-primary me-2"></i>

                            Servicios de Hoy

                        </h3>

                    </div>

                </div>


                <div class="card-body">

                    <div class="text-center py-10">

                        <i class="fa fa-tools fs-3x text-primary mb-5"></i>

                        <div class="fs-2hx fw-bold">

                            {{ $serviciosHoy ?? 0 }}

                        </div>

                        <div class="text-muted">

                            Servicios realizados

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- INGRESOS --}}
        <div class="col-md-4">

            <div class="card card-flush h-100">

                <div class="card-header">

                    <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-chart-line text-success me-2"></i>

                            Resumen de Ingresos

                        </h3>

                    </div>

                </div>


                <div class="card-body">

                    <div class="d-flex flex-column">

                        <div class="d-flex justify-content-between mb-5">

                            <span class="text-muted">
                                Hoy
                            </span>

                            <span class="fw-bold text-gray-800">

                                Bs {{ number_format($ingresosHoy ?? 0, 2) }}

                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-5">

                            <span class="text-muted">
                                Este mes
                            </span>

                            <span class="fw-bold text-gray-800">

                                Bs {{ number_format($ingresosMes ?? 0, 2) }}

                            </span>

                        </div>


                        <div class="d-flex justify-content-between">

                            <span class="text-muted">
                                Servicios
                            </span>

                            <span class="fw-bold text-success">

                                Bs {{ number_format($ingresosServicios ?? 0, 2) }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="row mb-5">

        <div class="col-md-12">

            <div class="card card-flush">

                <div class="card-header">

                    <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-clipboard-list text-primary me-2"></i>

                            Últimas Órdenes de Trabajo

                        </h3>

                    </div>

                    <div class="card-toolbar">

                        <a href="{{ url('/servicios') }}"
                           class="btn btn-sm btn-light-primary">

                            Ver todas

                            <i class="fa fa-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>


                <div class="card-body table-responsive">

                    <table class="table align-middle table-row-dashed fs-6 gy-5">

                        <thead>

                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">

                                <th>Nº</th>

                                <th>Cliente</th>

                                <th>Vehículo</th>

                                <th>Servicio</th>

                                <th>Estado</th>

                                <th>Total</th>

                                <th>Fecha</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($ultimasOrdenes ?? [] as $orden)

                                <tr>

                                    <td>

                                        <span class="fw-bold">

                                            {{ $orden->id }}

                                        </span>

                                    </td>


                                    <td>

                                        {{ $orden->cliente ?? 'Cliente' }}

                                    </td>


                                    <td>

                                        <span class="fw-bold">

                                            {{ $orden->vehiculo ?? 'Vehículo' }}

                                        </span>

                                    </td>


                                    <td>

                                        {{ $orden->servicio ?? 'Servicio' }}

                                    </td>


                                    <td>

                                        @php

                                            $estado = strtolower($orden->estado ?? '');

                                            $badge = match ($estado) {

                                                'pendiente' =>
                                                    'badge-light-warning',

                                                'en proceso',
                                                'en_proceso' =>
                                                    'badge-light-primary',

                                                'finalizado',
                                                'terminado' =>
                                                    'badge-light-success',

                                                'cancelado' =>
                                                    'badge-light-danger',

                                                default =>
                                                    'badge-light-secondary',

                                            };

                                        @endphp

                                        <span class="badge {{ $badge }}">

                                            {{ $orden->estado ?? 'Pendiente' }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="fw-bold text-success">

                                            Bs {{ number_format($orden->total ?? 0, 2) }}

                                        </span>

                                    </td>


                                    <td>

                                        {{ $orden->fecha ?? '' }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7"
                                        class="text-center text-muted py-10">

                                        <i class="fa fa-clipboard fs-3x mb-5"></i>

                                        <div>

                                            No existen órdenes de trabajo.

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- <div class="row">
        <div class="col-md-12">
            <div class="card card-flush">
                <div class="card-header">
                     <div class="card-title">

                        <h3 class="fw-bold">

                            <i class="fa fa-chart-area text-primary me-2"></i>

                            Ingresos Mensuales

                        </h3>

                    </div>

                </div>


                <div class="card-body">

                    <div id="graficoIngresos"
                         style="width:100%; height:400px;">
                    </div>

                </div>

            </div>

        </div>

    </div> --}}

@stop


@section('js')

    <script
        type="text/javascript"
        src="https://www.gstatic.com/charts/loader.js">
    </script>


    <script>

        google.charts.load('current', {
            packages: ['corechart']
        });


        google.charts.setOnLoadCallback(drawChart);


        function drawChart() {

            var data = google.visualization.arrayToDataTable([

                ['Mes', 'Ingresos'],

                @foreach($ingresosMensuales ?? [] as $ingreso)

                    [
                        '{{ $ingreso->mes }}',
                        {{ $ingreso->total }}
                    ],

                @endforeach

            ]);


            var options = {

                title: 'Ingresos Mensuales del Taller',

                curveType: 'function',

                legend: {
                    position: 'bottom'
                },

                height: 400,

                chartArea: {
                    width: '85%',
                    height: '70%'
                },

                vAxis: {
                    title: 'Ingresos (Bs)'
                },

                hAxis: {
                    title: 'Mes'
                }

            };


            var chart = new google.visualization.LineChart(
                document.getElementById('graficoIngresos')
            );


            chart.draw(data, options);

        }

    </script>

@endsection
```
