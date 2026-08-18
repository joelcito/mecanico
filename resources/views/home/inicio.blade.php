```blade
@extends('layouts.app')

@section('css')
@endsection

@section('content')


    <div class="row g-5 gx-xl-10 mb-5">

        {{-- PRODUCTOS --}}
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#F1416C;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-box fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalProductos ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold">
                        Productos
                    </div>

                </div>
            </div>
        </div>

        {{-- VENTAS HOY --}}
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#4d41f1;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-shopping-cart fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        Bs {{ number_format($ventasHoy ?? 0, 2) }}
                    </div>

                    <div class="text-white fw-semibold">
                        Ventas Hoy
                    </div>

                </div>
            </div>
        </div>

        {{-- UTILIDADES --}}
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#f18241;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-chart-line fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        Bs {{ number_format($utilidades ?? 0, 2) }}
                    </div>

                    <div class="text-white fw-semibold">
                        Utilidades
                    </div>

                </div>
            </div>
        </div>

        {{-- USUARIOS --}}
        <div class="col-md-3">
            <div class="card card-flush" style="background-color:#3e7213;">
                <div class="card-body text-center py-10">

                    <i class="fa fa-users fs-2x text-white mb-5"></i>

                    <div class="fs-2hx fw-bold text-white">
                        {{ $totalUsuarios ?? 0 }}
                    </div>

                    <div class="text-white fw-semibold">
                        Usuarios
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="row mb-5">

        {{-- STOCK BAJO --}}
        <div class="col-md-4">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Productos con Stock Bajo
                    </h3>
                </div>

                <div class="card-body">

                </div>

            </div>

        </div>

        {{-- ULTIMAS VENTAS --}}
        <div class="col-md-8">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Últimas Ventas
                    </h3>
                </div>

                <div class="card-body table-responsive">

                    <table class="table table-row-bordered">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- GRAFICO --}}
    <div class="row">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Ventas Mensuales
                    </h3>
                </div>

                <div class="card-body">

                    <div id="graficoVentas" style="width:100%; height:400px;">
                    </div>

                </div>

            </div>

        </div>

    </div>


@stop


@section('js')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js">
    </script>

    <script>

        google.charts.load('current', {
            packages: ['corechart']
        });

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Mes', 'Ventas'],

                @foreach($ventasMensuales ?? [] as $venta)
                    ['{{ $venta->mes }}', {{ $venta->total }}],
                @endforeach

                    ]);

            var options = {
                title: 'Ventas Mensuales',
                curveType: 'function',
                legend: {
                    position: 'bottom'
                }
            };

            var chart = new google.visualization.LineChart(
                document.getElementById('graficoVentas')
            );

            chart.draw(data, options);
        }

    </script>

@endsection
```
