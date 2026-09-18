@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        Diagnóstico del vehículo
                    </h2>
                    <div class="text-muted">
                        Orden:
                        <strong>
                            {{ $orden->numero_orden }}
                        </strong>
                    </div>
                </div>
                <div>
                    <a
                        href="{{ route('ordenServicio.detalle', $orden->id) }}"
                        class="btn btn-light">

                        <i class="fa fa-arrow-left me-2"></i>

                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-car me-2"></i>
                Información del vehículo
            </h3>
        </div>
        <div class="card-body">
            @php
                $usuario = $orden->vehiculo->cliente->user ?? null;
            @endphp

            <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">
                        Cliente
                    </label>
                    <div>
                        {{ $usuario
                            ? trim(
                                $usuario->nombres . ' ' .
                                $usuario->ap_paterno . ' ' .
                                $usuario->ap_materno
                            )
                            : '-' }}
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Cédula
                    </label>
                    <div>
                        {{ $usuario->cedula ?? '-' }}
                    </div>

                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Placa
                    </label>
                    <div>
                        <span class="badge badge-light-primary fs-6">
                            {{ $orden->vehiculo->placa }}
                        </span>
                    </div>
                </div>


                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Marca
                    </label>
                    <div>
                        {{ $orden->vehiculo->marca->nombre ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">
                        Modelo
                    </label>
                    <div>
                        {{ $orden->vehiculo->modelo }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <form method="POST" action="{{ route('ordenServicio.diagnostico.guardar', $orden->id) }}">

        @csrf
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    Diagnóstico general
                </h3>
            </div>

            <div class="card-body">
                <div class="mb-5">
                    <label class="form-label fw-bold">
                        Descripción del diagnóstico
                    </label>
                    <textarea
                        name="descripcion"
                        class="form-control"
                        rows="5"
                        required
                        placeholder="Describa el diagnóstico general del vehículo...">{{ old('descripcion') }}</textarea>
                </div>

                <div>
                    <label class="form-label fw-bold">
                        Observaciones generales
                    </label>
                    <textarea
                        name="observaciones"
                        class="form-control"
                        rows="4"
                        placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card mb-5">
            <div class="card-header">
                <div>
                    <h3 class="card-title mb-1">
                        Hallazgos y recomendaciones
                    </h3>
                    <span class="text-muted">
                        Registre los problemas encontrados y trabajos recomendados.
                    </span>
                </div>
            </div>


            <div class="card-body">
                <div id="contenedorDetalles">
                    <div class="detalle-diagnostico border rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">
                                    Descripción
                                </label>
                                <textarea
                                    name="detalles[0][descripcion]"
                                    class="form-control"
                                    rows="2"
                                    required
                                    placeholder="Ej.: Pastillas de freno delanteras desgastadas"></textarea>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">
                                    Tipo
                                </label>

                                <select
                                    name="detalles[0][tipo]"
                                    class="form-select"
                                    required>
                                    <option value="HALLAZGO">
                                        Hallazgo
                                    </option>
                                    <option value="RECOMENDACION">
                                        Recomendación
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">
                                    Prioridad
                                </label>

                                <select  name="detalles[0][prioridad]" class="form-select" required>
                                    <option value="BAJA">
                                        Baja
                                    </option>

                                    <option value="MEDIA" selected>
                                        Media
                                    </option>

                                    <option value="ALTA">
                                        Alta
                                    </option>

                                    <option value="CRITICA">
                                        Crítica
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">
                                    Observación
                                </label>
                                <input type="text" name="detalles[0][observacion]" class="form-control" placeholder="Detalle adicional">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-light-primary" id="btnAgregarDetalle">
                    <i class="fa fa-plus me-2"></i>
                    Agregar hallazgo
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-10">
            <a
                href="{{ route(
                    'ordenServicio.detalle',
                    $orden->id
                ) }}"
                class="btn btn-light">
                Cancelar
            </a>

            <button
                type="submit"
                class="btn btn-primary">
                <i class="fa fa-save me-2"></i>
                Finalizar diagnóstico
            </button>
        </div>
    </form>
</div>

@endsection
@section('js')
<script>
let detalleIndex = 1;
document
    .getElementById('btnAgregarDetalle')
    .addEventListener('click', function () {
        const contenedor = document.getElementById('contenedorDetalles');
        const html = `
            <div class="detalle-diagnostico border rounded p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>
                        Detalle del diagnóstico
                    </strong>
                    <button
                        type="button"
                        class="btn btn-sm btn-light-danger btnEliminarDetalle">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">
                            Descripción
                        </label>
                        <textarea
                            name="detalles[${detalleIndex}][descripcion]"
                            class="form-control"
                            rows="2"
                            required></textarea>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">
                            Tipo
                        </label>
                        <select
                            name="detalles[${detalleIndex}][tipo]"
                            class="form-select"
                            required>

                            <option value="HALLAZGO">
                                Hallazgo
                            </option>
                            <option value="RECOMENDACION">
                                Recomendación
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">
                            Prioridad
                        </label>

                        <select
                            name="detalles[${detalleIndex}][prioridad]"
                            class="form-select"
                            required>

                            <option value="BAJA">
                                Baja
                            </option>

                            <option value="MEDIA" selected>
                                Media
                            </option>

                            <option value="ALTA">
                                Alta
                            </option>

                            <option value="CRITICA">
                                Crítica
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            Observación
                        </label>
                        <input
                            type="text"
                            name="detalles[${detalleIndex}][observacion]"
                            class="form-control">

                    </div>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML(
            'beforeend',
            html
        );
        detalleIndex++;

    });


document.addEventListener(
    'click',
    function (event) {
        const boton =
            event.target.closest(
                '.btnEliminarDetalle'
            );

        if (!boton) {
            return;
        }

        const detalle =
            boton.closest(
                '.detalle-diagnostico'
            );

        detalle.remove();

    }
);

</script>

@endsection