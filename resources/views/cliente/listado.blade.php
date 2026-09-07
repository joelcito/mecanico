@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet"
        type="text/css"  />

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
<!-- MODAL CLIENTE -->

<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    FORMULARIO DE CLIENTE
                    <span class="text-info" id="nombre_busqueda"></span>
                </h3>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body scroll-y">
                <form id="formularioCliente">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                     
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Nombres
                                </label>
                                <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" >
                            </div>
                        </div>

                        <!-- APELLIDO PATERNO -->

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Apellido Paterno
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="ap_paterno"
                                    name="ap_paterno"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Apellido Materno
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="ap_materno"
                                    name="ap_materno"
                                >
                            </div>
                        </div>
                        <!-- CEDULA -->
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    C.I.
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="cedula"
                                    name="cedula"
                                >

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Celular
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="celular"
                                    name="celular"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    NIT
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="nit"
                                    name="nit"
                                >
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Dirección
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="direccion"
                                    name="direccion"
                                >

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Correo electrónico
                                </label>
                                <input
                                    type="email"
                                    class="form-control form-control-sm"
                                    id="email"
                                    name="email"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Contraseña
                                </label>
                                <input
                                    type="password"
                                    class="form-control form-control-sm"
                                    id="password"
                                    name="password"
                                >
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-md-12">
                        <button
                            class="btn btn-sm w-100 btn-success"
                            onclick="guardarCliente()">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- LISTADO -->

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container"
            class="app-container container-xxlg">
            <div class="card shadow-sm">

                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">

                    <h3 class="card-title fw-bold">
                        Listado de Clientes
                    </h3>
                    <div class="card-toolbar">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="modalNuevoCliente()">
                            <i class="fa fa-plus"></i>
                            Nuevo Cliente
                        </button>
                    </div>
                </div>
                <div
                    class="card-body py-4"
                    id="table_listado">

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

    });

    $(document).ready(function () {
        ajaxListado();
    });


    function ajaxListado() {
        let datos = {};
        $.ajax({
            url: "{{ route('cliente.ajaxListado') }}",
            method: "POST",
            data: datos,
            success: function (resultado) {
                if (resultado.estado) {
                    $('#table_listado')
                        .html(resultado.data.listado);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultado.mensaje || 'No se pudieron obtener los clientes.'
                    });
                }
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al obtener los clientes.'
                });
            }
        });

    }

    function modalNuevoCliente() {

        $('#formularioCliente')[0].reset();
        $('#id').val(0);
        limpiarErorres();
        $('#modalCliente').modal('show');

    }


    function guardarCliente() {
        let datos = $('#formularioCliente').serializeArray();
        $.ajax({
            url: "{{ route('cliente.guardar') }}",
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
                    $('#modalCliente').modal('hide');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultado.mensaje || 'No se pudo guardar el cliente.'
                    });
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
                        input.after(
                            `<div class="invalid-feedback">${mensaje}</div>`
                        );
                    }

                } else {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.'

                    });

                }

            }

        });

    }


    

    function editarCliente(cliente) {

        $('#id').val(cliente.id);
        $('#nit').val(cliente.nit);
        $('#direccion').val(cliente.direccion);
        $('#nombres').val(cliente.user ? cliente.user.nombres : '');
        $('#ap_paterno').val(cliente.user ? cliente.user.ap_paterno : '');
        $('#ap_materno').val(cliente.user ? cliente.user.ap_materno : '');
        $('#cedula').val(cliente.user ? cliente.user.cedula : '');
        $('#celular').val(cliente.user ? cliente.user.celular : '');
        $('#email').val(cliente.user ? cliente.user.email : '');
        $('#password').val('');
        limpiarErorres();
        $('#modalCliente').modal('show');
    }


    function eliminarCliente(cliente, nombre) {
        Swal.fire({
            title: "¿Quieres eliminar a " + nombre + "?",
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
                    url: "{{ route('cliente.eliminar') }}",
                    method: "POST",
                    data: {
                        cliente: cliente
                    },

                    success: function (resultado) {
                        if (resultado.estado) {
                            ajaxListado();
                            Swal.fire(
                                'Eliminado!',
                                'El cliente ha sido eliminado correctamente.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error',
                                resultado.mensaje || 'No se pudo eliminar el cliente.',
                                'error'
                            );
                        }
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.'

                        });

                    }

                });

            }

        });

    }

    function limpiarErorres() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

</script>

@endsection