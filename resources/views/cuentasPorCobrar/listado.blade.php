@extends('layouts.app')
@section('title', 'Cuentas por Cobrar')
@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">
                Cuentas por Cobrar
            </h3>
        </div>
    </div>
    <div class="card-body py-4">
        <div id="table_listado"></div>
    </div>
</div>

<!-- MODAL REGISTRAR PAGO -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">
                    Registrar pago
                </h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
            <form id="formPago">
                <div class="modal-body">
                    <div id="erroresPago"></div>
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <label class="required form-label">
                                Orden de servicio
                            </label>
                            <select class="form-select" id="orden_servicio_id" name="orden_servicio_id">
                                <option value="">
                                    Seleccione una orden
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-5">
                            <label class="form-label">
                                Total cotización
                            </label>
                            <input type="text" class="form-control" id="total_cotizacion" readonly >
                        </div>

                        <div class="col-md-4 mb-5">
                            <label class="form-label">
                                Total pagado
                            </label>
                            <input type="text" class="form-control" id="total_pagado" readonly >
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="form-label">
                                Saldo pendiente
                            </label>
                            <input type="text" class="form-control" id="saldo_pendiente" readonly>
                        </div>
                        <div class="col-md-6 mb-5">
                            <label class="required form-label">
                                Caja
                            </label>
                            <select class="form-select" id="caja_id" name="caja_id">
                                <option value="">
                                    Seleccione una caja
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-5">
                            <label class="required form-label">
                                Método de pago
                            </label>
                            <select class="form-select" id="tipo_pago" name="tipo_pago">
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
                        <div class="col-md-6 mb-5">
                            <label class="required form-label">
                                Monto a pagar
                            </label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto" name="monto">
                        </div>
                        <div class="col-md-6 mb-5">
                            <label class="required form-label">
                                Monto recibido
                            </label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto_recibido" name="monto_recibido">
                        </div>
                        <div class="col-md-6 mb-5">
                            <label class="form-label">
                                Cambio
                            </label>
                            <input type="text" class="form-control" id="cambio" readonly>

                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label">
                                Descripción
                            </label>
                            <textarea
                                class="form-control"
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>
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
                        type="submit"
                        class="btn btn-primary"
                        id="btnGuardarPago"
                    >
                        Registrar pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('css')
<link
    href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
    rel="stylesheet"
    type="text/css"
/>
@endsection
@section('js')

<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>

const BASE_URL = "{{ url('/') }}";

let ordenPagoSeleccionada = null;


/* ============================================================
   LISTADO
============================================================ */

function ajaxListado() {

    $.ajax({

        url: "{{ route('cuentasPorCobrar.ajaxListado') }}",

        type: "POST",

        dataType: "json",

        success: function(response) {

            if (response.estado) {

                $('#table_listado').html(
                    response.data.listado
                );

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });

            }

        },

        error: function(xhr) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message ??
                    'No se pudo cargar las cuentas por cobrar.'
            });

        }

    });

}


/* ============================================================
   REGISTRAR PAGO
============================================================ */

function registrarPago(idOrden) {

    limpiarFormularioPago();

    ordenPagoSeleccionada = idOrden;

    cargarDatosPago(idOrden);

    $('#modalPago').modal('show');
}


/* ============================================================
   LIMPIAR FORMULARIO
============================================================ */

function limpiarFormularioPago() {

    $('#formPago')[0].reset();

    $('#orden_servicio_id').html(`
        <option value="">
            Seleccione una orden
        </option>
    `);

    $('#caja_id').html(`
        <option value="">
            Seleccione una caja
        </option>
    `);

    $('#total_cotizacion').val('');
    $('#total_pagado').val('');
    $('#saldo_pendiente').val('');
    $('#cambio').val('');

    $('#erroresPago').html('');

    ordenPagoSeleccionada = null;
}


/* ============================================================
   CARGAR DATOS DEL PAGO
============================================================ */

function cargarDatosPago(idOrden = null) {

    $.ajax({

        url: "{{ route('pagos.crear') }}",

        type: "GET",

        dataType: "json",

        success: function(response) {

            if (!response.estado) {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });

                return;
            }


            /* ORDENES */

            let selectOrden = $('#orden_servicio_id');

            selectOrden.html(`
                <option value="">
                    Seleccione una orden
                </option>
            `);

            response.ordenes.forEach(function(orden) {

                let placa = '';

                if (orden.vehiculo) {

                    placa = orden.vehiculo.placa
                        ? ' - ' + orden.vehiculo.placa
                        : '';
                }

                selectOrden.append(`
                    <option
                        value="${orden.id}"
                        data-total="${orden.total}"
                        data-pagado="${orden.pagado}"
                        data-saldo="${orden.saldo}"
                    >
                        Orden #${orden.numero_orden}${placa}
                    </option>
                `);

            });


            /* CAJAS */

            let selectCaja = $('#caja_id');

            selectCaja.html(`
                <option value="">
                    Seleccione una caja
                </option>
            `);

            response.cajas.forEach(function(caja) {

                selectCaja.append(`
                    <option value="${caja.id}">
                        Caja #${caja.id} -
                        ${caja.sucursal?.nombre ?? ''}
                    </option>
                `);

            });


            /* SELECCIONAR ORDEN */

            if (idOrden) {

                selectOrden.val(idOrden);

                selectOrden.trigger('change');

            }

        },

        error: function(xhr) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message ??
                    'No se pudieron cargar los datos.'
            });

        }

    });

}


/* ============================================================
   CAMBIO DE ORDEN
============================================================ */

$('#orden_servicio_id').on('change', function() {

    let option = $(this).find(':selected');

    let total = parseFloat(
        option.data('total')
    ) || 0;

    let pagado = parseFloat(
        option.data('pagado')
    ) || 0;

    let saldo = parseFloat(
        option.data('saldo')
    ) || 0;


    $('#total_cotizacion').val(
        total.toFixed(2)
    );

    $('#total_pagado').val(
        pagado.toFixed(2)
    );

    $('#saldo_pendiente').val(
        saldo.toFixed(2)
    );

    $('#monto').val('');
    $('#monto_recibido').val('');
    $('#cambio').val('');

});


/* ============================================================
   CAMBIO MÉTODO DE PAGO
============================================================ */

$('#tipo_pago').on('change', function() {

    calcularCambio();

});


/* ============================================================
   CAMBIO MONTO
============================================================ */

$('#monto').on('input', function() {

    calcularCambio();

});


/* ============================================================
   CAMBIO RECIBIDO
============================================================ */

$('#monto_recibido').on('input', function() {

    calcularCambio();

});


/* ============================================================
   CALCULAR CAMBIO
============================================================ */

function calcularCambio() {

    let tipoPago = $('#tipo_pago').val();

    let monto = parseFloat(
        $('#monto').val()
    ) || 0;

    let recibido = parseFloat(
        $('#monto_recibido').val()
    ) || 0;


    if (tipoPago === 'EFECTIVO') {

        let cambio = recibido - monto;

        if (cambio < 0) {
            cambio = 0;
        }

        $('#cambio').val(
            cambio.toFixed(2)
        );

    } else {

        $('#monto_recibido').val(
            monto > 0
                ? monto.toFixed(2)
                : ''
        );

        $('#cambio').val('0.00');

    }

}


/* ============================================================
   GUARDAR PAGO
============================================================ */

$('#formPago').on('submit', function(e) {

    e.preventDefault();

    $('#erroresPago').html('');

    let btn = $('#btnGuardarPago');

    btn.prop('disabled', true);

    $.ajax({

        url: "{{ route('pagos.guardar') }}",

        type: "POST",

        data: {

            _token: "{{ csrf_token() }}",

            orden_servicio_id:
                $('#orden_servicio_id').val(),

            caja_id:
                $('#caja_id').val(),

            tipo_pago:
                $('#tipo_pago').val(),

            monto:
                $('#monto').val(),

            monto_recibido:
                $('#monto_recibido').val(),

            descripcion:
                $('#descripcion').val()

        },

        dataType: "json",

        success: function(response) {

            if (response.estado) {

                $('#modalPago').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Pago registrado',
                    text: response.message,
                    timer: 1800,
                    showConfirmButton: false
                });

                ajaxListado();

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });

            }

        },

        error: function(xhr) {

            if (xhr.status === 422) {

                let errores = xhr.responseJSON?.errors;

                let html = '';

                if (errores) {

                    html += `
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                    `;

                    Object.values(errores).forEach(function(error) {

                        error.forEach(function(mensaje) {

                            html += `<li>${mensaje}</li>`;

                        });

                    });

                    html += `
                            </ul>
                        </div>
                    `;

                } else {

                    html = `
                        <div class="alert alert-danger">
                            ${xhr.responseJSON?.message ??
                            'Datos inválidos.'}
                        </div>
                    `;
                }

                $('#erroresPago').html(html);

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message ??
                        'No se pudo registrar el pago.'
                });

            }

        },

        complete: function() {

            btn.prop('disabled', false);

        }

    });

});


/* ============================================================
   INICIO
============================================================ */

$(document).ready(function() {

    ajaxListado();

});

</script>

@endsection