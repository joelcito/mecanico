@extends('layouts.app')
@section('title', 'Pagos')
@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">
                Listado de Pagos
            </h3>
        </div>
        <div class="card-toolbar">
            <button
                type="button"
                class="btn btn-primary"
                onclick="modalNuevoPago()">
                <i class="ki-duotone ki-plus fs-2"></i>
                Nuevo Pago
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <div id="table_listado"></div>
    </div>
</div>

<div class="modal fade"
     id="modalPago"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">
                    Registrar Pago
                </h2>
                <div
                    class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                    data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </div>
            </div>
            <form id="formPago">
                @csrf
                <div class="modal-body">
                    <div id="erroresPago"></div>
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-bold">
                                Orden de Servicio
                            </label>
                            <select
                                class="form-select"
                                id="orden_servicio_id"
                                name="orden_servicio_id">

                                <option value="">
                                    Seleccione una orden
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="form-label fw-bold">
                                Total Cotización
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="total_cotizacion"
                                readonly>
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="form-label fw-bold">
                                Total Pagado
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="total_pagado"
                                readonly>

                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="form-label fw-bold">
                                Saldo Pendiente
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="saldo_pendiente"
                                readonly>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label fw-bold">
                                Caja
                            </label>

                            <select
                                class="form-select"
                                id="caja_id"
                                name="caja_id">
                                <option value="">
                                    Seleccione una caja
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label fw-bold">
                                Método de Pago
                            </label>
                            <select
                                class="form-select"
                                id="tipo_pago"
                                name="tipo_pago">
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
                            <label class="form-label fw-bold">
                                Monto
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                id="monto"
                                name="monto"
                                placeholder="0.00">

                        </div>
                        <div class="col-md-6 mb-5">
                            <label class="form-label fw-bold">
                                Cambio
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                id="cambio"
                                name="cambio"
                                value="0">
                            <div class="form-text">
                                El cambio aplica únicamente para pagos en efectivo.
                            </div>

                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-bold">
                                Descripción
                            </label>
                            <textarea
                                class="form-control"
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                                placeholder="Descripción del pago"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="guardarPago()">
                        <i class="ki-duotone ki-check fs-2"></i>
                        Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade"
     id="modalVerPago"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function ajaxListado() {
        $.ajax({
            url: "{{ route('pagos.ajaxListado') }}",
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
                        'No se pudo cargar el listado de pagos.'
                });
            }
        });
    }

    function modalNuevoPago() {
        limpiarFormularioPago();
        cargarDatosPago();
        $('#modalPago').modal('show');

    }


    function cargarDatosPago() {
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
                            data-saldo="${orden.saldo}">

                            Orden #${orden.numero_orden}${placa}

                        </option>
                    `);

                });

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

        $('#monto').attr(
            'max',
            saldo
        );

    });



    $('#tipo_pago').on('change', function() {
        if ($(this).val() !== 'EFECTIVO') {
            $('#cambio')
                .val('0')
                .prop('readonly', true);

        } else {
            $('#cambio')
                .prop('readonly', false);

        }
    });

    function guardarPago() {
        limpiarErroresPago();
        $.ajax({
            url: "{{ route('pagos.guardar') }}",
            type: "POST",
            data: $('#formPago').serialize(),
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
                    mostrarErroresPago(
                        xhr.responseJSON.errors
                    );
                    Swal.fire({
                        icon: 'warning',
                        title: 'Datos inválidos',
                        text: xhr.responseJSON.message ??
                            'Revise los datos ingresados.'
                    });

                    return;
                }


                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message ??
                        'No se pudo registrar el pago.'
                });

            }

        });

    }


    function verPago(id) {
        $.ajax({
            url: BASE_URL + '/pagos/' + id + '/actual',
            type: 'GET',
            success: function(response) {
                $('#modalVerPago .modal-content')
                    .html(response);

                $('#modalVerPago').modal('show');

            },

            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message ??
                        'No se pudo cargar el pago.'
                });
            }

        });

    }

    function anularPago(id) {
        Swal.fire({
            title: '¿Anular pago?',
            text: 'El pago y su movimiento de caja serán anulados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'

        }).then((result) => {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({
                url: BASE_URL + '/pagos/' + id + '/anular',
                type: 'POST',
                dataType: 'json',
                success: function(response) {

                    if (response.estado) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Pago anulado',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false
                        });

                        $('#modalVerPago').modal('hide');

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

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message ??
                            'No se pudo anular el pago.'
                    });

                }

            });

        });

    }


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

        $('#total_cotizacion').val('0.00');
        $('#total_pagado').val('0.00');
        $('#saldo_pendiente').val('0.00');

        $('#cambio')
            .val('0')
            .prop('readonly', false);

        $('#erroresPago').html('');

    }

    function limpiarErroresPago() {
        $('#erroresPago').html('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }



    function mostrarErroresPago(errors) {
        let html = `
            <div class="alert alert-danger">
                <ul class="mb-0">
        `;

        $.each(errors, function(campo, mensajes) {
            $('#' + campo).addClass('is-invalid');
            mensajes.forEach(function(mensaje) {
                html += `
                    <li>${mensaje}</li>
                `;
            });
        });

        html += `
                </ul>
            </div>
        `;
        $('#erroresPago').html(html);
    }

    $(document).ready(function() {
        ajaxListado();
    });
</script>

@endsection