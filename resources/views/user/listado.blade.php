@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton {
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h3 class="fw-bold">FORMULARIO DE USUARIO <span class="text-info" id="nombre_busqueda"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioUser">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Sucursal</label>
                                <select class="form-control form-control-sm" id="sucursal_id" name="sucursal_id"
                                    required>
                                    <option value="">Seleccione una Sucursal</option>
                                    @forelse($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @empty
                                        <h4 class="text-danger">No hay sucursales registrados</h4>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Rol</label>
                                <select class="form-control form-control-sm" id="rol_id" name="rol_id" required>
                                    <option value="">Seleccione un rol</option>
                                    @forelse($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                    @empty
                                        <h4 class="text-danger">No hay Roles registrado</h4>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre">
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Apellido paterno</label>
                                <input type="text" class="form-control form-control-sm" id="ap_paterno"
                                    name="ap_paterno">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Apellido materno</label>
                                <input type="text" class="form-control form-control-sm" id="ap_materno"
                                    name="ap_materno">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Cedula Identidad</label>
                                <input type="text" class="form-control form-control-sm" id="cedula" name="cedula"
                                    maxlength="10">
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Celular</label>
                                <input type="text" class="form-control form-control-sm" id="celular" name="celular"
                                    maxlength="8">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombre Usuario</label>
                                <input type="text" class="form-control form-control-sm" id="name" name="name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">password</label>
                                <input type="password" class="form-control form-control-sm" id="password"
                                    name="password">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Email</label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email">
                            </div>
                    </div> 

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm w-100 btn-success" onclick="guardarUser()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Usuario</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoUser()">
                            <i class="fa fa-plus"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>

                <div class="card-body py-4" id="table_listado">

                </div>
            </div>
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
        })

        $(document).ready(function () {
            ajaxListado();
        });


        function ajaxListado() {
            let datos = {};
            $.ajax({
                url: "{{ route('user.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }

                }
            })
        }

        function modalNuevoUser() {
            $('#password').val('')
            $('#email').val('')
            $('#name').val('')
            $('#celular').val('')
            $('#cedula').val('')
            $('#ap_materno').val('')
            $('#ap_paterno').val('')
            $('#nombre').val('')
            $('#rol_id').val('')
            $('#sucursal_id').val('')
            $('#id').val(0)
            $('#modalUsuario').modal('show')
        }

        function guardarUser() {

            if ($('#formularioUser')[0].checkValidity()) {
                let datos = $('#formularioUser').serializeArray();

                $.ajax({
                    url: "{{ route('user.guardarUser') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000,
                                showConfirmButton: false
                            });
                            ajaxListado();
                            $('#modalUsuario').modal('hide');
                        } else {

                        }
                    },
                    error: function (xhr) {
                        limpiarErorres();

                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;

                            for (let campo in errores) {
                                let mensaje = errores[campo][0];

                                let input = $(`[name="${campo}"]`);
                                input.addClass("is-invalid");
                                input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.',
                            });
                        }
                    }
                });
            } else {
                $("#formularioUser")[0].reportValidity();
            }

        }

        function editarUser(user) {
            $('#password').val(user.password)
            $('#email').val(user.email)
            $('#name').val(user.name)
            $('#celular').val(user.celular)
            $('#cedula').val(user.cedula)
            $('#ap_materno').val(user.ap_materno)
            $('#ap_paterno').val(user.ap_paterno)
            $('#nombre').val(user.nombres)
            $('#rol_id').val(user.rol_id)
            $('#sucursal_id').val(user.sucursal_id)
            $('#id').val(user.id)
            $('#password').val('')
            $('#modalUsuario').modal('show')
        }

        function eliminarUser(user, name) {
            Swal.fire({
                title: "¿Quieres eliminar " + name + "?",
                text: "¡No podrás recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Sí, borrar",
                cancelButtonText: "No, cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.eliminarUser') }}",
                        method: "POST",
                        data: { user: user },
                        success: function (resultado) {
                            if (resultado.estado) {
                                ajaxListado();
                                Swal.fire(
                                    'Eliminado!',
                                    'El usuario ha sido eliminado correctamente.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error',
                                    resultado.message || 'No se pudo eliminar el usuario.',
                                    'error'
                                );
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado.'
                            });
                        }
                    });


                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La operación fue cancelada',
                        'info'
                    );
                }
            });
        }

    </script>
@endsection