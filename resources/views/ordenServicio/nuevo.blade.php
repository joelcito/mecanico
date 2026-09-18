@extends('layouts.app')

@section('css')

<link
    href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
    rel="stylesheet"
    type="text/css"
/>

<style>
    .info-box {
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
    }

    .info-box-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #7e8299;
        margin-bottom: 8px;
    }

    .info-box-value {
        font-size: 15px;
        font-weight: 600;
        color: #181c32;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #181c32;
    }

    .required::after {
        content: " *";
        color: #f1416c;
    }
</style>

@endsection

@section('metadatos')

<meta
 name="csrf-token"
 content="{{ csrf_token() }}"
/>

@endsection

@section('content')

<div class="d-flex flex-column flex-column-fluid">

```
<div id="kt_app_content" class="app-content flex-column-fluid">

    <div
        id="kt_app_content_container"
        class="app-container container-xxlg"
    >

        {{-- ENCABEZADO --}}
        <div class="card shadow-sm mb-5">

            <div class="card-header bg-light-info py-4">

                <div class="card-title d-flex align-items-center">

                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label bg-primary">
                            <i class="fa fa-car text-white fs-3"></i>
                        </span>
                    </div>

                    <div>
                        <h3 class="fw-bold mb-1">
                            Nueva Orden de Servicio
                        </h3>

                        <span class="text-muted fs-7">
                            Registro de recepción del vehículo
                        </span>
                    </div>

                </div>

                <div class="card-toolbar">

                    <a
                        href="{{ route('ordenServicio.listado') }}"
                        class="btn btn-light btn-sm"
                    >
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </a>

                </div>

            </div>

        </div>


        {{-- FORMULARIO --}}
        <form id="formOrdenServicio">

            {{-- DATOS DEL VEHÍCULO --}}
            <div class="card shadow-sm mb-5">

                <div class="card-header">

                    <div class="card-title">
                        <span class="section-title">
                            <i class="fa fa-car me-2 text-primary"></i>
                            Vehículo
                        </span>
                    </div>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-8 mb-5">

                            <label class="form-label required">
                                Seleccionar vehículo
                            </label>

                            <select
                                class="form-select"
                                id="vehiculo_id"
                                name="vehiculo_id"
                                data-control="select2"
                                data-placeholder="Seleccione un vehículo"
                            >

                                <option value="">
                                    Seleccione un vehículo
                                </option>

                                @foreach ($vehiculos as $vehiculo)

                                    @php
                                        $cliente = $vehiculo->cliente;
                                        $usuario = $cliente?->user;
                                    @endphp

                                    <option
                                        value="{{ $vehiculo->id }}"
                                        data-cliente-nombre="{{ $usuario ? trim($usuario->nombres . ' ' . $usuario->ap_paterno . ' ' . $usuario->ap_materno) : '-' }}"
                                        data-cliente-cedula="{{ $usuario->cedula ?? '-' }}"
                                        data-cliente-celular="{{ $usuario->celular ?? '-' }}"
                                        data-marca="{{ $vehiculo->marca?->nombre ?? '-' }}"
                                        data-modelo="{{ $vehiculo->modelo }}"
                                        data-placa="{{ $vehiculo->placa }}"
                                        data-anio="{{ $vehiculo->anio ?? '-' }}"
                                        data-tipo="{{ $vehiculo->tipo_vehiculo }}"
                                    >

                                        
                                        {{ $vehiculo->placa }}
                                        -
                                        {{ $vehiculo->marca?->nombre ?? '' }}
                                        {{ $vehiculo->modelo }}
                                         -
                {{ $usuario ? trim($usuario->nombres . ' ' . $usuario->ap_paterno . ' ' . $usuario->ap_materno) : '-' }}


                                    </option>

                                @endforeach

                            </select>

                            <span
                                class="text-danger error-text"
                                id="error-vehiculo_id"
                            ></span>

                        </div>

                    </div>


                    {{-- INFORMACIÓN CLIENTE --}}
                    <div class="separator separator-dashed my-5"></div>

                    <h4 class="section-title mb-4">
                        <i class="fa fa-user me-2 text-primary"></i>
                        Información del cliente
                    </h4>

                    <div class="row">

                        <div class="col-md-4 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Cliente
                                </div>

                                <div
                                    class="info-box-value"
                                    id="cliente_nombre"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Cédula
                                </div>

                                <div
                                    class="info-box-value"
                                    id="cliente_cedula"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Celular
                                </div>

                                <div
                                    class="info-box-value"
                                    id="cliente_celular"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- INFORMACIÓN VEHÍCULO --}}
                    <h4 class="section-title mb-4 mt-5">

                        <i class="fa fa-car-side me-2 text-primary"></i>
                        Información del vehículo

                    </h4>

                    <div class="row">

                        <div class="col-md-3 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Marca
                                </div>

                                <div
                                    class="info-box-value"
                                    id="vehiculo_marca"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-3 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Modelo
                                </div>

                                <div
                                    class="info-box-value"
                                    id="vehiculo_modelo"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-2 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Placa
                                </div>

                                <div
                                    class="info-box-value"
                                    id="vehiculo_placa"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-2 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Año
                                </div>

                                <div
                                    class="info-box-value"
                                    id="vehiculo_anio"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="col-md-2 mb-4">

                            <div class="info-box">

                                <div class="info-box-title">
                                    Tipo
                                </div>

                                <div
                                    class="info-box-value"
                                    id="vehiculo_tipo"
                                >
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- DATOS DE RECEPCIÓN --}}
            <div class="card shadow-sm mb-5">

                <div class="card-header">

                    <div class="card-title">

                        <span class="section-title">

                            <i class="fa fa-clipboard-list me-2 text-primary"></i>

                            Datos de recepción

                        </span>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row">

                        {{-- FECHA --}}
                        <div class="col-md-4 mb-5">

                            <label class="form-label required">
                                Fecha y hora de recepción
                            </label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                name="fecha_recepcion"
                                id="fecha_recepcion"
                                value="{{ now()->format('Y-m-d\TH:i') }}"
                            >

                            <span
                                class="text-danger error-text"
                                id="error-fecha_recepcion"
                            ></span>

                        </div>


                        {{-- KILOMETRAJE --}}
                        <div class="col-md-4 mb-5">

                            <label class="form-label">
                                Kilometraje
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                name="kilometraje"
                                id="kilometraje"
                                min="0"
                                placeholder="Ej. 85000"
                            >

                            <span
                                class="text-danger error-text"
                                id="error-kilometraje"
                            ></span>

                        </div>


                        {{-- COMBUSTIBLE --}}
                        <div class="col-md-4 mb-5">

                            <label class="form-label">
                                Nivel de combustible (%)
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    class="form-control"
                                    name="nivel_combustible"
                                    id="nivel_combustible"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="Ej. 50"
                                >

                                <span class="input-group-text">
                                    %
                                </span>

                            </div>

                            <span
                                class="text-danger error-text"
                                id="error-nivel_combustible"
                            ></span>

                        </div>


                        {{-- MOTIVO --}}
                        <div class="col-md-12 mb-5">

                            <label class="form-label">
                                Motivo de ingreso
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="motivo_ingreso"
                                id="motivo_ingreso"
                                maxlength="255"
                                placeholder="Ej. Mantenimiento preventivo, cambio de aceite, revisión de frenos..."
                            >

                            <span
                                class="text-danger error-text"
                                id="error-motivo_ingreso"
                            ></span>

                        </div>


                        {{-- OBSERVACIONES --}}
                        <div class="col-md-12 mb-5">

                            <label class="form-label">
                                Observaciones
                            </label>

                            <textarea
                                class="form-control"
                                name="observaciones"
                                id="observaciones"
                                rows="4"
                                placeholder="Ingrese cualquier observación importante sobre el vehículo..."
                            ></textarea>

                            <span
                                class="text-danger error-text"
                                id="error-observaciones"
                            ></span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- BOTONES --}}
            <div class="card shadow-sm">

                <div class="card-body d-flex justify-content-end gap-3">

                    <a
                        href="{{ route('ordenServicio.listado') }}"
                        class="btn btn-light"
                    >
                        <i class="fa fa-times"></i>
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btnGuardar"
                    >

                        <i class="fa fa-save"></i>

                        Guardar recepción

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

</div>

@endsection

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]').attr('content')

        }

    });


    $(document).ready(function () {

        /*
         * Inicializar Select2
         */
        if ($('#vehiculo_id').data('control') === 'select2') {

            $('#vehiculo_id').select2({
                width: '100%',
                placeholder: 'Seleccione un vehículo',
                allowClear: true
            });

        }


        /*
         * Cambio de vehículo
         */
        $('#vehiculo_id').on('change', function () {

            const option = $(this).find('option:selected');

            if (!option.val()) {

                limpiarInformacion();

                return;
            }


            $('#cliente_nombre').text(
                option.data('cliente-nombre') || '-'
            );

            $('#cliente_cedula').text(
                option.data('cliente-cedula') || '-'
            );

            $('#cliente_celular').text(
                option.data('cliente-celular') || '-'
            );


            $('#vehiculo_marca').text(
                option.data('marca') || '-'
            );

            $('#vehiculo_modelo').text(
                option.data('modelo') || '-'
            );

            $('#vehiculo_placa').text(
                option.data('placa') || '-'
            );

            $('#vehiculo_anio').text(
                option.data('anio') || '-'
            );

            $('#vehiculo_tipo').text(
                option.data('tipo') || '-'
            );

        });


        /*
         * Guardar orden
         */
        $('#formOrdenServicio').on('submit', function (e) {

            e.preventDefault();

            limpiarErrores();


            const boton = $('#btnGuardar');

            boton
                .prop('disabled', true)
                .html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>' +
                    'Guardando...'
                );


            $.ajax({

                url: "{{ route('ordenServicio.guardar') }}",

                method: "POST",

                data: $(this).serialize(),

                success: function (resultado) {

                    if (resultado.estado) {

                        Swal.fire({

                            icon: 'success',

                            title: 'Registro exitoso',

                            text:
                                resultado.mensaje ||
                                'Orden de servicio registrada correctamente',

                            confirmButtonText: 'Aceptar'

                        }).then(function () {

                            // window.location.href =
                            //     "{{ route('ordenServicio.listado') }}";

                            window.location.href =
                                    "{{ url('/ordenServicio') }}/" + resultado.data.id;

                        });

                    } else {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text:
                                resultado.mensaje ||
                                'No se pudo registrar la orden.'

                        });

                        restaurarBoton();

                    }

                },

                error: function (xhr) {

                    console.log(xhr);

                    if (xhr.status === 422) {

                        const errores =
                            xhr.responseJSON.errors;

                        $.each(
                            errores,
                            function (campo, mensajes) {

                                $('#error-' + campo).text(
                                    mensajes[0]
                                );

                            }
                        );

                        Swal.fire({

                            icon: 'warning',

                            title: 'Revise los datos',

                            text:
                                'Hay campos que requieren atención.'

                        });

                    } else {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text:
                                'Ocurrió un error al registrar la orden.'

                        });

                    }

                    restaurarBoton();

                }

            });

        });

    });


    /*
     * Limpiar información del vehículo
     */
    function limpiarInformacion() {

        $('#cliente_nombre').text('-');
        $('#cliente_cedula').text('-');
        $('#cliente_celular').text('-');

        $('#vehiculo_marca').text('-');
        $('#vehiculo_modelo').text('-');
        $('#vehiculo_placa').text('-');
        $('#vehiculo_anio').text('-');
        $('#vehiculo_tipo').text('-');

    }


    /*
     * Limpiar errores
     */
    function limpiarErrores() {

        $('.error-text').text('');

    }


    /*
     * Restaurar botón
     */
    function restaurarBoton() {

        $('#btnGuardar')
            .prop('disabled', false)
            .html(
                '<i class="fa fa-save"></i> Guardar recepción'
            );

    }

</script>

@endsection
