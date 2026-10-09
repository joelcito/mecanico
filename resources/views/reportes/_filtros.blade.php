<div class="card shadow-sm mb-5">
    <div class="card-header py-4">
        <h3 class="card-title fw-bold">Filtros del reporte</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.index', $tipo) }}" class="row g-4 align-items-end">
            <div class="col-md-4 col-xl-3">
                <label for="fechaDesde" class="form-label fw-semibold">Fecha desde</label>
                <input type="date" id="fechaDesde" name="fecha_desde" class="form-control" value="{{ $filtros['desde_input'] }}" required>
            </div>
            <div class="col-md-4 col-xl-3">
                <label for="fechaHasta" class="form-label fw-semibold">Fecha hasta</label>
                <input type="date" id="fechaHasta" name="fecha_hasta" class="form-control" value="{{ $filtros['hasta_input'] }}" required>
            </div>
            <div class="col-md-4 col-xl-4">
                <label for="sucursalReporte" class="form-label fw-semibold">Sucursal</label>
                <select id="sucursalReporte" name="sucursal_id" class="form-select">
                    <option value="">Todas las sucursales autorizadas</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected($filtros['sucursal_id'] === (int) $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 d-flex gap-2 justify-content-xl-end">
                <a href="{{ route('reportes.index', $tipo) }}" class="btn btn-light">Limpiar</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter me-2"></i>Filtrar</button>
            </div>
        </form>
        @if($errors->any())
            <div class="alert alert-danger mt-4 mb-0" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</div>