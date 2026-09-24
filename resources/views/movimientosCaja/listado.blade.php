@extends('layouts.app')

@section('css')

    <link
        href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet"
        type="text/css"
    />

@endsection


@section('metadatos')

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    />

@endsection


@section('content')

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
                        Listado de Movimientos de Caja
                    </h3>

                    <div class="card-toolbar">

                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="modalNuevoMovimiento()"
                        >

                            <i class="fa fa-exchange-alt"></i>

                            Nuevo Movimiento

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


{{-- MODAL NUEVO MOVIMIENTO --}}

<div
    class="modal fade"
    id="modalMovimiento"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h3 class="fw-bold">
                    REGISTRAR MOVIMIENTO
                </h3>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <form id="formularioMovimiento">

                    <div class="mb-7">

                        <label class="required fw-semibold fs-6 mb-2">
                            Caja
                        </label>

                        <select
                            class="form-select form-select-sm"
                            id="caja_id"
                            name="caja_id"
                        >

                            <option value="">
                                Seleccione una caja
                            </option>

                        </select>

                    </div>


                    <div class="mb-7">

                        <label class="required fw-semibold fs-6 mb-2">
                            Tipo de movimiento
                        </label>

                        <select
                            class="form-select form-select-sm"
                            id="tipo"
                            name="tipo"
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <option value="INGRESO">
                                Ingreso
                            </option>

                            <option value="EGRESO">
                                Egreso
                            </option>

                        </select>

                    </div>


                    <div class="mb-7">

                        <label class="required fw-semibold fs-6 mb-2">
                            Método de pago
                        </label>

                        <select
                            class="form-select form-select-sm"
                            id="metodo_pago"
                            name="metodo_pago"
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <option value="EFECTIVO">
                                Efectivo
                            </option>

                            <option value="QR">
                                QR
                            </option>

                            <option value="TRANSFERENCIA">
                                Transferencia
                            </option>

                        </select>

                    </div>


                    <div class="mb-7">

                        <label class="required fw-semibold fs-6 mb-2">
                            Monto
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Bs.
                            </span>

                            <input
                                type="number"
                                class="form-control form-control-sm"
                                id="monto"
                                name="monto"
                                min="0.01"
                                step="0.01"
                            >

                        </div>

                    </div>


                    <div class="mb-7">

                        <label class="fw-semibold fs-6 mb-2">
                            Origen del dinero
                        </label>

                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="origen_dinero"
                            name="origen_dinero"
                            placeholder="Ej. Caja chica, ajuste..."
                        >

                    </div>


                    <div class="mb-7">

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

                </form>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn btn-success"
                    onclick="guardarMovimiento()"
                >

                    <i class="fa fa-save"></i>

                    Guardar

                </button>

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

            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]').attr('content')

        }

    });


    $(document).ready(function () {

        ajaxListado();

    });


    function ajaxListado() {

        $.ajax({

            url: "{{ route('movimientosCaja.ajaxListado') }}",

            method: "POST",

            data: {},

            success: function (resultado) {

                if (resultado.estado) {

                    $('#table_listado')
                        .html(resultado.data.listado);

                } else {

                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text: resultado.message ||
                            'No se pudo cargar el listado.'

                    });

                }

            },

            error: function () {

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: 'No se pudo cargar el listado.'

                });

            }

        });

    }


    function modalNuevoMovimiento() {

        limpiarErrores();

        $('#formularioMovimiento')[0].reset();

        cargarCajas();

        $('#modalMovimiento').modal('show');

    }


    function cargarCajas() {

        $.ajax({

            url: "{{ route('movimientosCaja.crear') }}",

            method: "GET",

            success: function (resultado) {

                let html =
                    '<option value="">Seleccione una caja</option>';

                resultado.cajas.forEach(function (caja) {

                    let usuario =
                        caja.usuario?.nombres ?? '';

                    let sucursal =
                        caja.sucursal?.nombre ?? '';

                    html += `
                        <option value="${caja.id}">
                            Caja #${caja.id} -
                            ${sucursal} -
                            ${usuario}
                        </option>
                    `;

                });

                $('#caja_id').html(html);

            },

            error: function () {

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: 'No se pudieron cargar las cajas abiertas.'

                });

            }

        });

    }


    function guardarMovimiento() {

        limpiarErrores();

        let datos =
            $('#formularioMovimiento').serializeArray();


        $.ajax({

            url: "{{ route('movimientosCaja.guardar') }}",

            method: "POST",

            data: datos,

            success: function (resultado) {

                if (resultado.estado) {

                    Swal.fire({

                        title: 'Movimiento registrado',

                        text: resultado.message,

                        icon: 'success',

                        timer: 2000,

                        showConfirmButton: false

                    });

                    $('#modalMovimiento').modal('hide');

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

                limpiarErrores();


                if (xhr.status === 422) {

                    let errores =
                        xhr.responseJSON?.errors;


                    if (errores) {

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

                        Swal.fire({

                            icon: 'warning',

                            title: 'Atención',

                            text:
                                xhr.responseJSON?.message ||
                                'No se pudo registrar el movimiento.'

                        });

                    }

                } else {

                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text:
                            'Ocurrió un error inesperado.'

                    });

                }

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
