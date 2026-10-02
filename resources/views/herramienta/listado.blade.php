@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
<div class="modal fade" id="modalHerramienta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">FORMULARIO DE HERRAMIENTA</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioHerramienta" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="herramienta_id" value="0">
                    <div class="row">
                        <div class="col-md-6 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Código</label>
                            <input type="text" class="form-control form-control-sm" name="codigo" id="herramienta_codigo">
                        </div>
                        <div class="col-md-6 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                            <input type="text" class="form-control form-control-sm" name="nombre" id="herramienta_nombre">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Categoría</label>
                            <select class="form-select form-select-sm" name="categoria_id" id="herramienta_categoria_id">
                                <option value="">Seleccione una categoría...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Marca</label>
                            <select class="form-select form-select-sm" name="marca_id" id="herramienta_marca_id">
                                <option value="">Seleccione una marca...</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Unidad de medida</label>
                            <select class="form-select form-select-sm" name="unidad_medida" id="herramienta_unidad_medida">
                                <option value="">Seleccione...</option>
                                <option value="UNIDAD">Unidad</option>
                                <option value="JUEGO">Juego</option>
                                <option value="PAR">Par</option>
                            </select>
                        </div>
                        <div class="col-md-4 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                            <input type="number" class="form-control form-control-sm" name="cantidad" id="herramienta_cantidad" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4 fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Stock mínimo</label>
                            <input type="number" class="form-control form-control-sm" name="stock_minimo" id="herramienta_stock_minimo" min="0" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Imagen</label>
                            <input type="file" class="form-control form-control-sm" name="imagen" id="herramienta_imagen" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-md-6 fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Estado</label>
                            <select class="form-select form-select-sm" name="estado" id="herramienta_estado">
                                <option value="ACTIVO">Activo</option>
                                <option value="INACTIVO">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Descripción</label>
                        <textarea class="form-control form-control-sm" name="descripcion" id="herramienta_descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-success" onclick="guardarHerramienta()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Herramientas</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevaHerramienta()">
                            <i class="fa fa-plus"></i>
                            Nueva Herramienta
                        </button>
                    </div>
                </div>
                <div class="card-body py-4" id="table_herramientas"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            ajaxListadoHerramientas();
        });

        function ajaxListadoHerramientas() {
            $.ajax({
                url: "{{ route('herramienta.ajaxListado') }}",
                method: 'POST',
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#table_herramientas').html(resultado.data.listado);
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el listado de herramientas.' });
                }
            });
        }

        function limpiarErroresHerramienta() {
            $('#formularioHerramienta .is-invalid').removeClass('is-invalid');
            $('#formularioHerramienta .invalid-feedback').remove();
        }

        function mostrarErroresHerramienta(xhr) {
            limpiarErroresHerramienta();
            const errores = xhr.responseJSON?.errors || {};
            Object.keys(errores).forEach(function (campo) {
                const input = $('#formularioHerramienta [name="' + campo + '"]');
                input.addClass('is-invalid');
                $('<div class="invalid-feedback"></div>').text(errores[campo][0]).insertAfter(input);
            });
        }

        function modalNuevaHerramienta() {
            limpiarErroresHerramienta();
            $('#formularioHerramienta')[0].reset();
            $('#herramienta_id').val(0);
            $('#herramienta_cantidad, #herramienta_stock_minimo').val(0);
            $('#herramienta_estado').val('ACTIVO');
            $('#modalHerramienta').modal('show');
        }

        function guardarHerramienta() {
            limpiarErroresHerramienta();
            $.ajax({
                url: "{{ route('herramienta.guardar') }}",
                method: 'POST',
                data: new FormData($('#formularioHerramienta')[0]),
                processData: false,
                contentType: false,
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#modalHerramienta').modal('hide');
                        ajaxListadoHerramientas();
                        Swal.fire({ title: 'Registro exitoso', icon: 'success', timer: 2500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: resultado.message || 'No se pudo guardar la herramienta.' });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        mostrarErroresHerramienta(xhr);
                        return;
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error inesperado.' });
                }
            });
        }

        function editarHerramienta(herramienta) {
            limpiarErroresHerramienta();
            $('#herramienta_id').val(herramienta.id);
            $('#herramienta_codigo').val(herramienta.codigo);
            $('#herramienta_nombre').val(herramienta.nombre);
            $('#herramienta_descripcion').val(herramienta.descripcion ?? '');
            $('#herramienta_categoria_id').val(herramienta.categoria_id);
            $('#herramienta_marca_id').val(herramienta.marca_id ?? '');
            $('#herramienta_unidad_medida').val(herramienta.unidad_medida);
            $('#herramienta_cantidad').val(herramienta.cantidad ?? 0);
            $('#herramienta_stock_minimo').val(herramienta.stock_minimo ?? 0);
            $('#herramienta_estado').val(herramienta.estado ?? 'ACTIVO');
            $('#herramienta_imagen').val('');
            $('#modalHerramienta').modal('show');
        }

        function eliminarHerramienta(id, nombre) {
            Swal.fire({
                title: '¿Quieres eliminar esta herramienta?',
                text: nombre,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('herramienta.eliminar') }}",
                    method: 'POST',
                    data: { id: id },
                    success: function (resultado) {
                        if (resultado.estado) {
                            ajaxListadoHerramientas();
                            Swal.fire('Eliminada', 'La herramienta fue eliminada correctamente.', 'success');
                            return;
                        }
                        Swal.fire('Error', resultado.message || 'No se pudo eliminar la herramienta.', 'error');
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error inesperado.' });
                    }
                });
            });
        }
    </script>
@endsection