@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @include('reportes._filtros')

    <div class="card shadow-sm">
        <div class="card-header py-4 flex-wrap gap-3">
            <div class="card-title d-block">
                <h2 class="fw-bold mb-2">{{ $titulo }}</h2>
                <div class="text-muted fs-7">
                    <span class="me-5"><strong>Período:</strong> {{ $filtros['desde']->format('d/m/Y') }} al {{ $filtros['hasta']->format('d/m/Y') }}</span>
                    <span><strong>Sucursal:</strong> {{ $etiquetaSucursal }}</span>
                </div>
                @if($tipo === 'productos')
                    <div class="text-warning fs-7 mt-2">El stock actual es global en el sistema; la sucursal y el período solo filtran entradas y salidas registradas.</div>
                @elseif($tipo === 'movimientos')
                    <div class="text-muted fs-7 mt-2">La sucursal se atribuye al usuario que registró el movimiento; para movimientos de reparación sin creador directo se usa el creador de la orden.</div>
                @elseif($tipo === 'ventas')
                    <div class="text-muted fs-7 mt-2">Se usa la fecha de recepción y la sucursal del usuario que creó la orden; solo se incluyen cotizaciones aprobadas.</div>
                @endif
            </div>
            <div class="card-toolbar d-flex gap-2">
                <a class="btn btn-light-danger btn-sm" href="{{ route('reportes.exportar', ['tipo' => $tipo, 'formato' => 'pdf'] + request()->query()) }}" target="_blank" rel="noopener"><i class="fa fa-file-pdf me-2"></i>PDF</a>
                <a class="btn btn-light-success btn-sm" href="{{ route('reportes.exportar', ['tipo' => $tipo, 'formato' => 'xlsx'] + request()->query()) }}"><i class="fa fa-file-excel me-2"></i>Excel</a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle" id="tablaReporte">
                    <thead>
                        <tr>
                            @foreach($columnas as $etiqueta)
                                <th>{{ $etiqueta }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            <tr>
                                @foreach($columnas as $campo => $etiqueta)
                                    <td>{{ $registro[$campo] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($columnas) }}" class="text-center text-muted py-8">No se encontraron datos para el período y sucursal seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $registros->links() }}</div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function() {
            $('#tablaReporte').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: true,
                language: { emptyTable: 'No hay datos para mostrar' }
            });
        });
    </script>
@endsection