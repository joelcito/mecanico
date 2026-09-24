@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
@endsection
@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg" >
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">
                        Listado de Cajas
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevaCaja()" >
                            <i class="fa fa-cash-register"></i>
                            Abrir Caja
                        </button>
                    </div>
                </div>
                <div class="card-body py-4" id="table_listado">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    APERTURA DE CAJA
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formularioCaja">
                    <div class="mb-7">
                        <label class="required fw-semibold fs-6 mb-2">
                            Sucursal
                        </label>
                        <select class="form-select form-select-sm" id="sucursal_id" name="sucursal_id">
                            <option value="">
                                Seleccione una sucursal
                            </option>
                            @foreach(\App\Models\Sucursal::where('estado', 'ACTIVO')->orderBy('nombre')->get() as $sucursal)
                                <option value="{{ $sucursal->id }}">
                                    {{ $sucursal->nombre }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                    <div class="mb-7">
                        <label class="required fw-semibold fs-6 mb-2">
                            Monto de apertura
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                Bs.
                            </span>
                            <input
                                type="number"
                                class="form-control form-control-sm"
                                id="monto_apertura"
                                name="monto_apertura"
                                min="0"
                                step="0.01"
                                value="0"
                            >
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="abrirCaja()">
                    <i class="fa fa-cash-register"></i>
                    Abrir Caja
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerCaja" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

        </div>

    </div>

</div>

@stop()
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
            ajaxListado();
        });

        function ajaxListado() {
            let datos = {};
            $.ajax({
                url: "{{ route('cajas.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {
                        $('#table_listado').html(
                            resultado.data.listado
                        );

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resultado.message ||
                                'No se pudo cargar el listado de cajas.'
                        });
                    }
                },

                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el listado de cajas.'
                    });
                }
            });
        }


        function modalNuevaCaja() {
            limpiarErrores();
            $('#formularioCaja')[0].reset();
            $('#monto_apertura').val('0');
            $('#modalCaja').modal('show');
        }


        function abrirCaja() {
            limpiarErrores();
            let datos = $('#formularioCaja').serializeArray();
            $.ajax({
                url: "{{ route('cajas.abrir') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "CAJA ABIERTA",
                            text: resultado.message,
                            icon: "success",
                            timer: 2500,
                            showConfirmButton: false
                        });
                        $('#modalCaja').modal('hide');
                        ajaxListado();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resultado.message ||
                                'No se pudo abrir la caja.'
                        });
                    }
                },
                error: function (xhr) {
                    limpiarErrores();
                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON?.errors;
                        if (errores) {
                            for (let campo in errores) {
                                let mensaje = errores[campo][0];
                                let input = $(`[name="${campo}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback">
                                        ${mensaje}
                                    </div>`
                                );
                            }
                        } else {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Atención',
                                text: xhr.responseJSON?.message ||
                                    'No se pudo abrir la caja.'
                            });

                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.'
                        });
                    }
                }
            });
        }
        function limpiarErrores() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

        }

        function verCaja(id) {

    $.ajax({

        url: "{{ url('cajas') }}/" + id + "/actual",

        method: "GET",

        success: function (resultado) {

            $('#modalVerCaja .modal-content').html(resultado);

            $('#modalVerCaja').modal('show');

        },

        error: function (xhr) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message ||
                    'No se pudo obtener la información de la caja.'
            });

        }

    });

}

        function cerrarCaja(id) {
            Swal.fire({
                title: '¿Cerrar caja?',
                text: 'Una vez cerrada no podrá recibir nuevos movimientos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar caja',
                cancelButtonText: 'Cancelar'
            }).then((resultado) => {
                if (!resultado.isConfirmed) {
                    return;
                }
                $.ajax({
                    url: "{{ url('cajas') }}/" + id + "/cerrar",
                    method: "POST",
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Caja cerrada',
                                text: resultado.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            ajaxListado();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resultado.message
                            });
                        }
                    },

                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message ||
                                'No se pudo cerrar la caja.'
                        });
                    }
                });
            });
        }

    </script>
@endsection