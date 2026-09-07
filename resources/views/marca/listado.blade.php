@extends('layouts.app')
@section('css')

    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet"
        type="text/css"  />
    <style> .tamanio_boton { font-size: 6px;}
    </style>

@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
@endsection
@section('content')

{{-- MODAL MARCA --}}
<div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    FORMULARIO DE MARCA
                    <span class="text-info" id="nombre_busqueda"></span>
                </h3>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body scroll-y">
                <form id="formularioMarca">
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Nombre
                                </label>
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre"  >

                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">

                                <label class="fw-semibold fs-6 mb-2">
                                    Descripción
                                </label>

                                <textarea
                                    class="form-control form-control-sm"
                                    id="descripcion"
                                    name="descripcion"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Estado
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="estado"
                                    name="estado"
                                >
                                    <option value="ACTIVO">
                                        Activo
                                    </option>

                                    <option value="INACTIVO">
                                        Inactivo
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-md-12">
                        <button
                            type="button"
                            class="btn btn-sm w-100 btn-success"
                            onclick="guardarMarca()"
                        >
                            Guardar
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>


{{-- LISTADO --}}
<div class="d-flex flex-column flex-column-fluid">

    <div
        id="kt_app_content"
        class="app-content flex-column-fluid"
    >

        <div
            id="kt_app_content_container"
            class="app-container container-xxlg"
        >

            <div class="card shadow-sm">

                <div
                    class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between"
                >

                    <h3 class="card-title fw-bold">
                        Listado de Marcas
                    </h3>


                    <div class="card-toolbar">

                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="modalNuevaMarca()"
                        >

                            <i class="fa fa-plus"></i>

                            Nueva Marca

                        </button>

                    </div>

                </div>


                <div
                    class="card-body py-4"
                    id="table_listado"
                >

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

                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content')

            }
        });

        $(document).ready(function () {
            ajaxListado();
        });

        function ajaxListado() {

            let datos = {};
            $.ajax({

                url: "{{ route('marca.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (resultado) {

                    if (resultado.estado) {

                        $('#table_listado')
                            .html(resultado.data.listado);

                    }

                },

                error: function (xhr) {
                    console.log(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el listado de marcas.'

                    });

                }
            });
        }


        function modalNuevaMarca() {
            limpiarErrores();
            $('#formularioMarca')[0].reset();
            $('#id').val(0);
            $('#estado').val('ACTIVO');
            $('#modalMarca').modal('show');

        }

        function guardarMarca() {
            limpiarErrores();
            let datos =
                $('#formularioMarca').serializeArray();


            $.ajax({

                url: "{{ route('marca.guardar') }}",
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
                        $('#modalMarca').modal('hide');

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resultado.message ||
                                'No se pudo guardar la marca.'

                        });

                    }

                },


                error: function (xhr) {

                    limpiarErrores();
                    if (xhr.status === 422) {

                        let errores =
                            xhr.responseJSON.errors;


                        for (let campo in errores) {

                            let mensaje =
                                errores[campo][0];
                            let input =
                                $(`[name="${campo}"]`);
                            input.addClass('is-invalid');
                            input.after(
                                `<div class="invalid-feedback">
                                    ${mensaje}
                                </div>`
                            );

                        }

                    } else {

                        console.log(xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.'

                        });

                    }

                }

            });

        }


        function editarMarca(marca) {
            limpiarErrores();

            $('#id').val(marca.id);

            $('#nombre').val(marca.nombre);

            $('#descripcion').val(
                marca.descripcion ?? ''
            );

            $('#estado').val(
                marca.estado ?? 'ACTIVO'
            );

            $('#modalMarca').modal('show');

        }

        function eliminarMarca(id, nombre) {

            Swal.fire({
                title: "¿Quieres eliminar " + nombre + "?",
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

                        url: "{{ route('marca.eliminar') }}",
                        method: "POST",
                        data: {

                            id: id
                        },

                        success: function (resultado) {
                            if (resultado.estado) {
                                ajaxListado();
                                Swal.fire(
                                    'Eliminado!',
                                    'La marca ha sido eliminada correctamente.',
                                    'success'

                                );

                            } else {

                                Swal.fire(

                                    'Error',

                                    resultado.message ||
                                    'No se pudo eliminar la marca.',

                                    'error'

                                );

                            }

                        },


                        error: function (xhr) {

                            console.log(xhr);

                            Swal.fire({

                                icon: 'error',

                                title: 'Error',

                                text: 'Ocurrió un error inesperado.'

                            });

                        }

                    });

                }

                else if (
                    result.dismiss ===
                    Swal.DismissReason.cancel
                ) {

                    Swal.fire(

                        'Cancelado',

                        'La operación fue cancelada',

                        'info'

                    );

                }

            });

        }



        function limpiarErrores() {

            $('.is-invalid')
                .removeClass('is-invalid');

            $('.invalid-feedback')
                .remove();

        }

    </script>

@endsection