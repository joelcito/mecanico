@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light-info py-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h3 class="card-title fw-bold mb-1">{{ $esTecnico ? 'Mis herramientas asignadas' : 'Asignaciones de herramientas' }}</h3>
                        <span class="text-muted">{{ $esTecnico ? 'Consulta tus herramientas actuales y su historial.' : 'Control de herramientas prestadas, devoluciones y disponibilidad.' }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('herramienta.listado') }}" class="btn btn-light btn-sm"><i class="fa fa-toolbox me-2"></i>Catálogo</a>
                        @unless($esTecnico)
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAsignarHerramienta"><i class="fa fa-plus me-2"></i>Asignar herramienta</button>
                        @endunless
                    </div>
                </div>

                @if($resumen)
                    <div class="card-body border-bottom py-4">
                        <div class="row g-3">
                            <div class="col-6 col-xl-3"><div class="info-box"><div class="info-box-title">Herramientas</div><div class="info-box-value">{{ $resumen['total'] }}</div></div></div>
                            <div class="col-6 col-xl-3"><div class="info-box"><div class="info-box-title">Disponibles</div><div class="info-box-value text-success">{{ $resumen['disponibles'] }}</div></div></div>
                            <div class="col-6 col-xl-3"><div class="info-box"><div class="info-box-title">Asignadas</div><div class="info-box-value text-primary">{{ $resumen['asignadas'] }}</div></div></div>
                            <div class="col-6 col-xl-3"><div class="info-box"><div class="info-box-title">Mantenimiento</div><div class="info-box-value text-warning">{{ $resumen['mantenimiento'] }}</div></div></div>
                        </div>
                    </div>
                @endif

                <div class="card-body border-bottom py-4">
                    <form method="GET" action="{{ route('asignacionesHerramientas.listado') }}" class="row g-3 align-items-end">
                        @unless($esTecnico)
                            <div class="col-md-3">
                                <label for="filtroTecnico" class="form-label">Técnico</label>
                                <select name="tecnico_id" id="filtroTecnico" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}" @selected(($filtros['tecnico_id'] ?? '') == $tecnico->id)>{{ trim($tecnico->nombres . ' ' . $tecnico->ap_paterno . ' ' . $tecnico->ap_materno) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endunless
                        <div class="col-md-3">
                            <label for="filtroHerramienta" class="form-label">Herramienta</label>
                            <select name="herramienta_id" id="filtroHerramienta" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach($todasHerramientas as $herramienta)
                                    <option value="{{ $herramienta->id }}" @selected(($filtros['herramienta_id'] ?? '') == $herramienta->id)>{{ $herramienta->codigo }} - {{ $herramienta->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtroEstado" class="form-label">Estado</label>
                            <select name="estado" id="filtroEstado" class="form-select form-select-sm">
                                <option value="TODAS" @selected($estado === 'TODAS')>Todas</option>
                                <option value="ASIGNADA" @selected($estado === 'ASIGNADA')>Activas</option>
                                <option value="DEVUELTA" @selected($estado === 'DEVUELTA')>Devueltas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtroDesde" class="form-label">Desde</label>
                            <input type="date" name="desde" id="filtroDesde" class="form-control form-control-sm" value="{{ $filtros['desde'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label for="filtroHasta" class="form-label">Hasta</label>
                            <input type="date" name="hasta" id="filtroHasta" class="form-control form-control-sm" value="{{ $filtros['hasta'] ?? '' }}">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('asignacionesHerramientas.listado') }}" class="btn btn-light btn-sm">Limpiar</a>
                            <button type="submit" class="btn btn-light-primary btn-sm"><i class="fa fa-filter me-2"></i>Filtrar</button>
                        </div>
                    </form>
                </div>

                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle" id="tablaAsignacionesHerramientas">
                            <thead>
                                <tr>
                                    <th>Herramienta</th>
                                    <th>Código</th>
                                    <th>Técnico</th>
                                    <th>Asignación</th>
                                    <th>Devolución</th>
                                    <th>Estado</th>
                                    <th>Asignó</th>
                                    <th>Recibió devolución</th>
                                    <th>Observaciones</th>
                                    @unless($esTecnico)<th class="text-end">Acciones</th>@endunless
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asignaciones as $asignacion)
                                    <tr>
                                        <td>{{ $asignacion->herramienta?->nombre ?? 'Herramienta eliminada' }}</td>
                                        <td>{{ $asignacion->herramienta?->codigo ?? '-' }}</td>
                                        <td>{{ trim(($asignacion->tecnico?->nombres ?? '') . ' ' . ($asignacion->tecnico?->ap_paterno ?? '') . ' ' . ($asignacion->tecnico?->ap_materno ?? '')) ?: 'Técnico no disponible' }}</td>
                                        <td data-order="{{ $asignacion->fecha_asignacion?->format('Y-m-d H:i:s') }}">{{ $asignacion->fecha_asignacion?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td data-order="{{ $asignacion->fecha_devolucion?->format('Y-m-d H:i:s') }}">{{ $asignacion->fecha_devolucion?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td>
                                            @if(in_array($asignacion->estado, ['ASIGNADA', 'ACTIVA'], true))
                                                <span class="badge badge-light-primary">ACTIVA</span>
                                            @else
                                                <span class="badge badge-light-success">DEVUELTA</span>
                                            @endif
                                        </td>
                                        <td>{{ $asignacion->usuarioAsignador?->nombres ?? '-' }}</td>
                                        <td>{{ $asignacion->usuarioDevolucion?->nombres ?? '-' }}</td>
                                        <td class="text-wrap">{{ $asignacion->observaciones ?? '-' }}</td>
                                        @unless($esTecnico)
                                            <td class="text-end text-nowrap">
                                                @if(in_array($asignacion->estado, ['ASIGNADA', 'ACTIVA'], true))
                                                    <button type="button" class="btn btn-sm btn-light-success btnDevolverAsignacion" data-id="{{ $asignacion->id }}" data-nombre="{{ $asignacion->herramienta?->nombre ?? 'Herramienta' }}"><i class="fa fa-rotate-left me-1"></i>Devolver</button>
                                                @endif
                                                @if($asignacion->herramienta_id)
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-primary btnHistorialHerramienta" data-id="{{ $asignacion->herramienta_id }}" title="Ver historial"><i class="fa fa-clock-rotate-left"></i></button>
                                                @endif
                                            </td>
                                        @endunless
                                    </tr>
                                @empty
                                    <h4>No hay asignaciones para los filtros seleccionados.</h4>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @unless($esTecnico)
                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title fw-bold">Disponibilidad operativa</h3></div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle" id="tablaDisponibilidadHerramientas">
                                <thead><tr><th>Herramienta</th><th>Código</th><th>Estado operativo</th><th>Técnico actual</th><th class="text-end">Acciones</th></tr></thead>
                                <tbody>
                                    @forelse($todasHerramientas as $herramienta)
                                        <tr>
                                            <td>{{ $herramienta->nombre }}</td>
                                            <td>{{ $herramienta->codigo ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $claseEstadoHerramienta = match ($herramienta->estado_disponibilidad) {
                                                        'DISPONIBLE' => 'badge-light-success',
                                                        'ASIGNADA' => 'badge-light-primary',
                                                        'MANTENIMIENTO' => 'badge-light-warning',
                                                        default => 'badge-light-danger',
                                                    };
                                                @endphp
                                                <span class="badge {{ $claseEstadoHerramienta }}">{{ $herramienta->estado_disponibilidad }}</span>
                                            </td>
                                            <td>{{ $herramienta->asignacionActiva?->tecnico?->nombres ?? '-' }}</td>
                                            <td class="text-end">
                                                @if($herramienta->estado_disponibilidad !== 'ASIGNADA')
                                                    <button type="button" class="btn btn-sm btn-light-primary btnCambiarEstadoHerramienta" data-id="{{ $herramienta->id }}" data-nombre="{{ $herramienta->nombre }}" data-estado="{{ $herramienta->estado_disponibilidad }}">Cambiar estado</button>
                                                @else
                                                    <span class="text-muted">Asignada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <h4>No hay herramientas registradas.</h4>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endunless
        </div>
    </div>
</div>

@unless($esTecnico)
    <div class="modal fade" id="modalAsignarHerramienta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h3 class="modal-title fw-bold">Asignar herramienta</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="tecnicoAsignacion" class="form-label">Técnico</label>
                        <select id="tecnicoAsignacion" class="form-select"><option value="">Seleccione un técnico</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}">{{ trim($tecnico->nombres . ' ' . $tecnico->ap_paterno . ' ' . $tecnico->ap_materno) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="herramientaAsignacion" class="form-label">Herramienta disponible</label>
                        <select id="herramientaAsignacion" class="form-select"><option value="">Seleccione una herramienta</option>
                            @foreach($herramientas as $herramienta)
                                <option value="{{ $herramienta->id }}">{{ $herramienta->codigo }} - {{ $herramienta->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4"><label for="fechaAsignacion" class="form-label">Fecha de asignación</label><input type="datetime-local" id="fechaAsignacion" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}"></div>
                    <div><label for="observacionesAsignacion" class="form-label">Observaciones</label><textarea id="observacionesAsignacion" class="form-control" rows="3" maxlength="2000"></textarea></div>
                    <div id="errorAsignacionHerramienta" class="alert alert-danger d-none mt-4 mb-0"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnGuardarAsignacionHerramienta">Asignar</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEstadoHerramienta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header"><h3 class="modal-title fw-bold">Disponibilidad</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body">
                    <input type="hidden" id="estadoHerramientaId">
                    <div class="info-box mb-4"><div class="info-box-title">Herramienta</div><div class="info-box-value" id="estadoHerramientaNombre"></div></div>
                    <label for="nuevoEstadoHerramienta" class="form-label">Estado operativo</label>
                    <select id="nuevoEstadoHerramienta" class="form-select">
                        <option value="DISPONIBLE">Disponible</option>
                        <option value="MANTENIMIENTO">Mantenimiento</option>
                        <option value="BAJA">Baja</option>
                    </select>
                    <div id="errorEstadoHerramienta" class="alert alert-danger d-none mt-4 mb-0"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnGuardarEstadoHerramienta">Guardar</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHistorialHerramienta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header"><div><h3 class="modal-title fw-bold">Historial de herramienta</h3><div class="text-muted" id="subtituloHistorialHerramienta"></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body"><div class="table-responsive"><table class="table table-row-dashed align-middle"><thead><tr><th>Técnico</th><th>Asignación</th><th>Devolución</th><th>Asignó</th><th>Recibió devolución</th><th>Estado</th><th>Observaciones</th></tr></thead><tbody id="historialHerramientaBody"><tr><td colspan="7" class="text-center text-muted">Cargando historial...</td></tr></tbody></table></div></div>
            </div>
        </div>
    </div>
@endunless
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(function() {
            $('#tablaAsignacionesHerramientas').DataTable({
                order: [],
                pageLength: 25,
                responsive: true,
                language: {
                    paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    emptyTable: 'No hay datos disponibles'
                }
            });
            @unless($esTecnico)
                $('#tablaDisponibilidadHerramientas').DataTable({
                    order: [[0, 'asc']],
                    pageLength: 10,
                    responsive: true,
                    language: {
                        paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
                        search: 'Buscar:',
                        lengthMenu: 'Mostrar _MENU_ registros',
                        info: 'Mostrando _START_ a _END_ de _TOTAL_ herramientas',
                        emptyTable: 'No hay datos disponibles'
                    }
                });
            @endunless
        });

        function textoErrorAsignacion(xhr) {
            const errores = Object.values(xhr.responseJSON?.errors ?? {}).flat();
            return xhr.responseJSON?.message ?? errores[0] ?? 'No se pudo completar la operación.';
        }

        function nombreCompleto(persona) {
            return [persona?.nombres, persona?.ap_paterno, persona?.ap_materno].filter(Boolean).join(' ') || '-';
        }

        function escaparHtml(valor) {
            return $('<div>').text(valor ?? '').html();
        }

        function fechaLegible(valor) {
            if (!valor) return '-';
            const fecha = new Date(String(valor).replace(' ', 'T'));
            return Number.isNaN(fecha.getTime()) ? '-' : fecha.toLocaleString('es-BO');
        }

        @unless($esTecnico)
            $('#btnGuardarAsignacionHerramienta').on('click', function() {
                const btn = $(this);
                const error = $('#errorAsignacionHerramienta');
                error.addClass('d-none').empty();
                btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('asignacionesHerramientas.guardar') }}",
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    data: {
                        tecnico_id: $('#tecnicoAsignacion').val(),
                        herramienta_id: $('#herramientaAsignacion').val(),
                        fecha_asignacion: $('#fechaAsignacion').val(),
                        observaciones: $('#observacionesAsignacion').val()
                    }
                }).done(function(response) {
                    Swal.fire({ icon: 'success', title: 'Asignación registrada', text: response.message, timer: 1600, showConfirmButton: false })
                        .then(function() { window.location.reload(); });
                }).fail(function(xhr) {
                    error.removeClass('d-none').text(textoErrorAsignacion(xhr));
                }).always(function() {
                    btn.prop('disabled', false);
                });
            });

            $('#tablaAsignacionesHerramientas').on('click', '.btnDevolverAsignacion', function() {
                const boton = $(this);
                Swal.fire({
                    title: '¿Devolver herramienta?',
                    text: 'La herramienta será marcada nuevamente como disponible.',
                    input: 'textarea',
                    inputLabel: 'Observaciones de devolución (opcional)',
                    inputPlaceholder: 'Escriba una observación si corresponde',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, devolver',
                    cancelButtonText: 'Cancelar'
                }).then(function(resultado) {
                    if (!resultado.isConfirmed) return;

                    boton.prop('disabled', true);
                    $.ajax({
                        url: `{{ url('/asignaciones-herramientas') }}/${boton.data('id')}/devolver`,
                        method: 'PUT',
                        headers: { 'Accept': 'application/json' },
                        data: { observaciones_devolucion: resultado.value }
                    }).done(function(response) {
                        Swal.fire({ icon: 'success', title: 'Herramienta devuelta', text: response.message, timer: 1600, showConfirmButton: false })
                            .then(function() { window.location.reload(); });
                    }).fail(function(xhr) {
                        Swal.fire({ icon: 'error', title: 'No se pudo devolver', text: textoErrorAsignacion(xhr) });
                        boton.prop('disabled', false);
                    });
                });
            });

            $('#tablaDisponibilidadHerramientas').on('click', '.btnCambiarEstadoHerramienta', function() {
                $('#estadoHerramientaId').val($(this).data('id'));
                $('#estadoHerramientaNombre').text($(this).data('nombre'));
                $('#nuevoEstadoHerramienta').val($(this).data('estado'));
                $('#errorEstadoHerramienta').addClass('d-none').empty();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEstadoHerramienta')).show();
            });

            $('#btnGuardarEstadoHerramienta').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('/asignaciones-herramientas/herramienta') }}/${$('#estadoHerramientaId').val()}/estado`,
                    method: 'PUT',
                    headers: { 'Accept': 'application/json' },
                    data: { estado_disponibilidad: $('#nuevoEstadoHerramienta').val() }
                }).done(function(response) {
                    Swal.fire({ icon: 'success', title: 'Estado actualizado', text: response.message, timer: 1400, showConfirmButton: false })
                        .then(function() { window.location.reload(); });
                }).fail(function(xhr) {
                    $('#errorEstadoHerramienta').removeClass('d-none').text(textoErrorAsignacion(xhr));
                }).always(function() {
                    btn.prop('disabled', false);
                });
            });

            $('#tablaAsignacionesHerramientas').on('click', '.btnHistorialHerramienta', function() {
                const id = $(this).data('id');
                $('#historialHerramientaBody').html('<tr><td colspan="7" class="text-center text-muted">Cargando historial...</td></tr>');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHistorialHerramienta')).show();

                $.get(`{{ url('/asignaciones-herramientas/herramienta') }}/${id}/historial`)
                    .done(function(response) {
                        const herramienta = response.data.herramienta;
                        $('#subtituloHistorialHerramienta').text(`${herramienta.codigo ?? '-'} · ${herramienta.nombre ?? ''}`);
                        const asignaciones = response.data.asignaciones ?? [];
                        if (!asignaciones.length) {
                            $('#historialHerramientaBody').html('<tr><td colspan="7" class="text-center text-muted">Sin historial de asignaciones.</td></tr>');
                            return;
                        }
                        $('#historialHerramientaBody').html(asignaciones.map(function(asignacion) {
                            const estado = ['ASIGNADA', 'ACTIVA'].includes(asignacion.estado) ? 'ACTIVA' : 'DEVUELTA';
                            return `<tr>
                                <td>${escaparHtml(nombreCompleto(asignacion.tecnico))}</td>
                                <td>${escaparHtml(fechaLegible(asignacion.fecha_asignacion))}</td>
                                <td>${escaparHtml(fechaLegible(asignacion.fecha_devolucion))}</td>
                                <td>${escaparHtml(asignacion.usuario_asignador?.nombres ?? '-')}</td>
                                <td>${escaparHtml(asignacion.usuario_devolucion?.nombres ?? '-')}</td>
                                <td>${escaparHtml(estado)}</td>
                                <td>${escaparHtml(asignacion.observaciones ?? '-')}</td>
                            </tr>`;
                        }).join(''));
                    }).fail(function(xhr) {
                        $('#historialHerramientaBody').html(`<tr><td colspan="7" class="text-danger">${escaparHtml(textoErrorAsignacion(xhr))}</td></tr>`);
                    });
            });
        @endunless
    </script>
@endsection