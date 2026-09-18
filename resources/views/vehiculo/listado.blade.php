@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style> .tamanio_boton { font-size: 6px;  } </style>

@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />

@endsection
@section('content')

<div class="modal fade" id="modalVehiculo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    FORMULARIO DE VEHÍCULO
                </h3>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body scroll-y">
                <form id="formularioVehiculo">
                    <input type="hidden" name="id" id="id" value="0" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Cliente
                                </label>
                                <select
                                    class="form-select form-select-sm"
                                    id="cliente_id"
                                    name="cliente_id"
                                    required
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio.')"
                                    oninput="this.setCustomValidity('')"
                                >
                                    <option value="">
                                        Seleccione un cliente
                                    </option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->user->nombres ?? '' }}
                                            {{ $cliente->user->ap_paterno ?? '' }}
                                            {{ $cliente->user->ap_materno ?? '' }}
                                            @if ($cliente->nit)
                                                - NIT: {{ $cliente->nit }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Marca
                                </label>
                                <select
                                    class="form-select form-select-sm"
                                    id="marca_id"
                                    name="marca_id"
                                    required
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio.')"
                                    oninput="this.setCustomValidity('')"
                                >
                                    <option value="">
                                        Seleccione una marca
                                    </option>
                                    @foreach ($marcas as $marca)
                                        <option value="{{ $marca->id }}">
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Modelo
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="modelo"
                                    name="modelo"
                                    required
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio.')"
                                    oninput="this.setCustomValidity('')"
                                >

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Año
                                </label>

                                <input
                                    type="number"
                                    class="form-control form-control-sm"
                                    id="anio"
                                    name="anio"
                                    min="1900"
                                >

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Placa
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm text-uppercase"
                                    id="placa"
                                    name="placa"
                                    required
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio.')"
                                    oninput="this.setCustomValidity('')"
                                >
                            </div>
                        </div>
                    </div>


                    {{-- COLOR / TIPO --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Color
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="color"
                                    name="color"
                                >

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">
                                    Tipo de vehículo
                                </label>
                                <select
                                    class="form-select form-select-sm"
                                    id="tipo_vehiculo"
                                    name="tipo_vehiculo"
                                    required
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio.')"
                                    oninput="this.setCustomValidity('')"
                                >
                                    <option value="AUTOMOVIL">
                                        Automóvil
                                    </option>
                                    <option value="MOTOCICLETA">
                                        Motocicleta
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label fw-bold">
                                Tipo de propulsión
                            </label>

                            <select
                                name="tipo_propulsion"
                                id="tipo_propulsion"
                                class="form-select">

                                <option value="">
                                    Seleccione
                                </option>

                                <option value="COMBUSTION">
                                    Combustión
                                </option>

                                <option value="ELECTRICO">
                                    Eléctrico
                                </option>

                                <option value="HIBRIDO">
                                    Híbrido
                                </option>

                            </select>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    VIN
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm text-uppercase"
                                    id="vin"
                                    name="vin"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Número de motor
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="numero_motor"
                                    name="numero_motor"
                                >

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">
                                    Observaciones
                                </label>
                                <textarea
                                    class="form-control form-control-sm"
                                    id="observaciones"
                                    name="observaciones"
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
                            onclick="guardarVehiculo()"
                        >
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content"
        class="app-content flex-column-fluid">

        <div id="kt_app_content_container"
            class="app-container container-xxlg">
            <div class="card shadow-sm">

                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">

                    <h3 class="card-title fw-bold">
                        Listado de Vehículos
                    </h3>

                    <div class="card-toolbar">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="modalNuevoVehiculo()"
                        >

                            <i class="fa fa-plus"></i>
                            Nuevo Vehículo
                        </button>
                    </div>
                </div>
                <div class="card-body py-4"
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
            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function () {
        ajaxListado();

    });

    function ajaxListado()
    {

        let datos = {};

        $.ajax({
            url: "{{ route('vehiculo.ajaxListado') }}",
            method: "POST",
            data: datos,
            success: function (resultado) {
                if (resultado.estado) {
                    $('#table_listado').html(resultado.data.listado);
                }
            },

            error: function (xhr) {
                console.log(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:'No se pudo cargar el listado de vehículos.'
                });
            }
        });
    }

    function modalNuevoVehiculo()
    {

        limpiarErrores();
        $('#formularioVehiculo')[0].reset();
        $('#id').val(0);
        $('#estado').val('ACTIVO');
        $('#tipo_vehiculo').val('AUTOMOVIL');
        $('#modalVehiculo').modal('show');
    }

    function guardarVehiculo()
    {
        limpiarErrores();
        let datos = $('#formularioVehiculo').serializeArray();

        $.ajax({
            url: "{{ route('vehiculo.guardar') }}",
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
                    $('#modalVehiculo').modal('hide');

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultado.mensaje || 'No se pudo guardar el vehículo.'

                    });
                }
            },

            error: function (xhr) {
                limpiarErrores();
                if (xhr.status === 422) {
                    let errores = xhr.responseJSON.errors;
                    for (let campo in errores) {
                        let mensaje = errores[campo][0];
                        let input =  $(`[name="${campo}"]`);
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
                        text:'Ocurrió un error inesperado.'
                    });
                }
            }
        });
    }


    function editarVehiculo(vehiculo)
    {
        limpiarErrores();
        $('#id').val(vehiculo.id);
        $('#cliente_id').val(
            vehiculo.cliente_id
        );
        $('#marca_id').val(
            vehiculo.marca_id
        );

        $('#modelo').val(
            vehiculo.modelo ?? ''
        );

        $('#anio').val(
            vehiculo.anio ?? ''
        );

        $('#placa').val(
            vehiculo.placa ?? ''
        );

        $('#color').val(
            vehiculo.color ?? ''
        );

        $('#tipo_vehiculo').val(
            vehiculo.tipo_vehiculo ?? 'AUTOMOVIL'
        );

        $('#vin').val(
            vehiculo.vin ?? ''
        );

        $('#numero_motor').val(
            vehiculo.numero_motor ?? ''
        );

        $('#observaciones').val(
            vehiculo.observaciones ?? ''
        );

        $('#estado').val(
            vehiculo.estado ?? 'ACTIVO'
        );


        $('#modalVehiculo').modal('show');

    }

    function eliminarVehiculo(id, placa)
    {

        Swal.fire({
            title:
                "¿Quieres eliminar el vehículo " +
                placa +
                "?",

            text:
                "¡No podrás recuperarlo!",

            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText:
                "Sí, borrar",
            cancelButtonText:
                "No, cancelar",

            reverseButtons: true

        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url:
                        "{{ route('vehiculo.eliminar') }}",

                    method: "POST",
                    data: {
                        id: id
                    },
                    success: function (resultado) {
                        if (resultado.estado) {
                            ajaxListado();
                            Swal.fire(
                                'Eliminado!',
                                'El vehículo ha sido eliminado correctamente.',
                                'success'
                            );

                        } else {
                            Swal.fire(
                                'Error',
                                resultado.mensaje ||
                                'No se pudo eliminar el vehículo.',
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

    function limpiarErrores()
    {
        $('.is-invalid')
            .removeClass('is-invalid');

        $('.invalid-feedback')
            .remove();

    }

</script>

@endsection