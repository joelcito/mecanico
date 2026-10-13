@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Diagnóstico del vehículo</h2>
                    <div class="text-muted">
                        Orden: <strong>{{ $orden->numero_orden }}</strong>
                    </div>
                </div>

                <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
                   class="btn btn-light">
                    <i class="fa fa-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Información del vehículo --}}
    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-car me-2"></i> Información del vehículo
            </h3>
        </div>

        <div class="card-body">
            @php
                $usuario = $orden->vehiculo->cliente->user ?? null;
            @endphp

            <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">Cliente</label>
                    <div>
                        {{ $usuario ? trim($usuario->nombres . ' ' . $usuario->ap_paterno . ' ' . $usuario->ap_materno) : '-' }}
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">Cédula</label>
                    <div>{{ $usuario->cedula ?? '-' }}</div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">Placa</label>
                    <div>
                        <span class="badge badge-light-primary fs-6">
                            {{ $orden->vehiculo->placa }}
                        </span>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">Marca</label>
                    <div>{{ $orden->vehiculo->marca->nombre ?? '-' }}</div>
                </div>

                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">Modelo</label>
                    <div>{{ $orden->vehiculo->modelo }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario de diagnóstico --}}
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ route('ordenServicio.diagnostico.guardar', $orden->id) }}"
          id="formDiagnostico">

        @csrf

        {{-- Diagnóstico general --}}
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">Diagnóstico general</h3>
            </div>

            <div class="card-body">
                <div class="mb-5">
                    <label class="form-label fw-bold">
                        Descripción del diagnóstico
                    </label>

                    <textarea name="descripcion"
                              class="form-control"
                              rows="5"
                              maxlength="10000"
                              required
                              placeholder="Describa el diagnóstico general del vehículo...">{{ old('descripcion') }}</textarea>

                    @error('descripcion')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label fw-bold">
                        Observaciones generales
                    </label>

                    <textarea name="observaciones"
                              class="form-control"
                              rows="4"
                              maxlength="10000"
                              placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Hallazgos --}}
        <div class="card mb-5">
            <div class="card-header">
                <div>
                    <h3 class="card-title mb-1">
                        Hallazgos y recomendaciones
                    </h3>
                    <span class="text-muted">
                        Registre los problemas encontrados, las recomendaciones y las fotografías correspondientes a cada detalle.
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div id="contenedorDetalles">

                    {{-- Primer detalle --}}
                    <div class="detalle-diagnostico border rounded p-4 mb-4"
                         data-detalle-index="0">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <strong class="text-primary titulo-detalle">
                                <i class="fa fa-clipboard-list me-2"></i>
                                Hallazgo o recomendación 1
                            </strong>
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">Descripción</label>

                                <textarea name="detalles[0][descripcion]"
                                          class="form-control"
                                          rows="3"
                                          maxlength="1000"
                                          required
                                          placeholder="Ej.: Pastillas de freno delanteras desgastadas"></textarea>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">Tipo</label>

                                <select name="detalles[0][tipo]"
                                        class="form-select"
                                        required>
                                    <option value="HALLAZGO">Hallazgo</option>
                                    <option value="RECOMENDACION">Recomendación</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">Prioridad</label>

                                <select name="detalles[0][prioridad]"
                                        class="form-select"
                                        required>
                                    <option value="BAJA">Baja</option>
                                    <option value="MEDIA" selected>Media</option>
                                    <option value="ALTA">Alta</option>
                                    <option value="CRITICA">Crítica</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Observación</label>

                                <input type="text"
                                       name="detalles[0][observacion]"
                                       class="form-control"
                                       maxlength="1000"
                                       placeholder="Detalle adicional">
                            </div>
                        </div>

                        {{-- Fotografías del detalle --}}
                        <div class="border-top pt-4 mt-2">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        <i class="fa fa-camera text-primary me-2"></i>
                                        Fotografías del hallazgo
                                    </h5>
                                    <small class="text-muted">
                                        JPG, PNG o WEBP. Máximo 5 MB por fotografía.
                                    </small>
                                </div>

                                <button type="button"
                                        class="btn btn-sm btn-light-primary btnAgregarFoto">
                                    <i class="fa fa-plus me-1"></i>
                                    Agregar fotografía
                                </button>
                            </div>

                            <div class="contenedor-fotos" data-fotos-index="0">
                                <div class="text-muted texto-sin-fotos">
                                    No se han agregado fotografías.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <button type="button"
                        class="btn btn-light-primary"
                        id="btnAgregarDetalle">
                    <i class="fa fa-plus me-2"></i>
                    Agregar hallazgo o recomendación
                </button>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="d-flex justify-content-end gap-3 mb-10">
            <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
               class="btn btn-light">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-primary"
                    id="btnGuardarDiagnostico">
                <i class="fa fa-save me-2"></i>
                Finalizar diagnóstico
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contenedorDetalles = document.getElementById('contenedorDetalles');
    const btnAgregarDetalle = document.getElementById('btnAgregarDetalle');
    const form = document.getElementById('formDiagnostico');
    const btnGuardar = document.getElementById('btnGuardarDiagnostico');

    let detalleIndex = 1;
    let fotoIndices = { 0: 0 };

    const MAX_FOTOS_POR_DETALLE = 10;
    const MAX_TAMANO_FOTO = 5 * 1024 * 1024;
    const TIPOS_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];

    function actualizarTitulos() {
        const detalles = contenedorDetalles.querySelectorAll('.detalle-diagnostico');

        detalles.forEach(function (detalle, indice) {
            const titulo = detalle.querySelector('.titulo-detalle');

            if (titulo) {
                titulo.innerHTML =
                    '<i class="fa fa-clipboard-list me-2"></i> Hallazgo o recomendación ' +
                    (indice + 1);
            }
        });
    }

    function actualizarEstadoFotos(contenedor) {
        const filas = contenedor.querySelectorAll('.fila-foto');
        const mensaje = contenedor.querySelector('.texto-sin-fotos');

        if (mensaje) {
            mensaje.classList.toggle('d-none', filas.length > 0);
        }

        const tarjeta = contenedor.closest('.detalle-diagnostico');
        const botonAgregar = tarjeta.querySelector('.btnAgregarFoto');

        if (botonAgregar) {
            botonAgregar.disabled = filas.length >= MAX_FOTOS_POR_DETALLE;
            botonAgregar.title = filas.length >= MAX_FOTOS_POR_DETALLE
                ? 'Máximo 10 fotografías por detalle'
                : 'Agregar fotografía';
        }
    }

    function crearFilaFoto(indiceDetalle) {
        const tarjeta = contenedorDetalles.querySelector(
            '.detalle-diagnostico[data-detalle-index="' + indiceDetalle + '"]'
        );

        if (!tarjeta) return;

        const contenedorFotos = tarjeta.querySelector('.contenedor-fotos');
        const cantidadActual = contenedorFotos.querySelectorAll('.fila-foto').length;

        if (cantidadActual >= MAX_FOTOS_POR_DETALLE) {
            alert('Puedes agregar como máximo 10 fotografías por hallazgo o recomendación.');
            return;
        }

        const indiceFoto = fotoIndices[indiceDetalle] ?? 0;
        fotoIndices[indiceDetalle] = indiceFoto + 1;

        const fila = document.createElement('div');
        fila.className = 'fila-foto border rounded p-3 mb-3';

        fila.innerHTML = `
            <div class="row align-items-start">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Seleccionar fotografía</label>
                    <input
                        type="file"
                        class="form-control input-foto"
                        name="detalles[${indiceDetalle}][fotos][${indiceFoto}][archivo]"
                        accept="image/jpeg,image/png,image/webp"
                        required>
                    <small class="text-muted">Máximo 5 MB.</small>

                    <div class="vista-previa-foto mt-3 d-none">
                        <img
                            src=""
                            alt="Vista previa de la fotografía"
                            class="img-thumbnail"
                            style="max-height: 190px; max-width: 100%; object-fit: contain;">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Descripción de la fotografía</label>
                    <input
                        type="text"
                        class="form-control"
                        name="detalles[${indiceDetalle}][fotos][${indiceFoto}][descripcion]"
                        maxlength="255"
                        placeholder="Ej.: Desgaste visible en la pastilla de freno">
                    <small class="text-muted">
                        Describe qué se observa en esta imagen.
                    </small>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-light-danger btnEliminarFoto w-100">
                        <i class="fa fa-trash me-1"></i>
                        Quitar
                    </button>
                </div>
            </div>
        `; 
         const mensaje = contenedorFotos.querySelector('.texto-sin-fotos');

        if (mensaje) {
            mensaje.classList.add('d-none');
        }

        contenedorFotos.appendChild(fila);
        actualizarEstadoFotos(contenedorFotos);
    }

    btnAgregarDetalle.addEventListener('click', function () {
        const indice = detalleIndex++;

        fotoIndices[indice] = 0;

        const html = `
            <div class="detalle-diagnostico border rounded p-4 mb-4"
                 data-detalle-index="${indice}">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <strong class="text-primary titulo-detalle">
                        <i class="fa fa-clipboard-list me-2"></i>
                        Hallazgo o recomendación
                    </strong>

                    <button type="button"
                            class="btn btn-sm btn-light-danger btnEliminarDetalle">
                        <i class="fa fa-trash me-1"></i>
                        Eliminar detalle
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea
                            name="detalles[${indice}][descripcion]"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            required
                            placeholder="Describe el problema o trabajo recomendado"></textarea>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Tipo</label>
                        <select name="detalles[${indice}][tipo]"
                                class="form-select"
                                required>
                            <option value="HALLAZGO">Hallazgo</option>
                            <option value="RECOMENDACION">Recomendación</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Prioridad</label>
                        <select name="detalles[${indice}][prioridad]"
                                class="form-select"
                                required>
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA" selected>Media</option>
                            <option value="ALTA">Alta</option>
                            <option value="CRITICA">Crítica</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Observación</label>
                        <input
                            type="text"
                            name="detalles[${indice}][observacion]"
                            class="form-control"
                            maxlength="1000"
                            placeholder="Detalle adicional">
                    </div>
                </div>

                <div class="border-top pt-4 mt-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="fa fa-camera text-primary me-2"></i>
                                Fotografías del hallazgo
                            </h5>
                            <small class="text-muted">
                                JPG, PNG o WEBP. Máximo 5 MB por fotografía.
                            </small>
                        </div>

                        <button type="button"
                                class="btn btn-sm btn-light-primary btnAgregarFoto">
                            <i class="fa fa-plus me-1"></i>
                            Agregar fotografía
                        </button>
                    </div>

                    <div class="contenedor-fotos" data-fotos-index="${indice}">
                        <div class="text-muted texto-sin-fotos">
                            No se han agregado fotografías.
                        </div>
                    </div>
                </div>
            </div>
        `;

        contenedorDetalles.insertAdjacentHTML('beforeend', html);
        actualizarTitulos();
    });

    // Agregar fotografía al detalle correspondiente.
    contenedorDetalles.addEventListener('click', function (event) {
        const botonAgregarFoto = event.target.closest('.btnAgregarFoto');

        if (botonAgregarFoto) {
            const tarjeta = botonAgregarFoto.closest('.detalle-diagnostico');
            crearFilaFoto(Number(tarjeta.dataset.detalleIndex));
            return;
        }

        const botonEliminarFoto = event.target.closest('.btnEliminarFoto');

        if (botonEliminarFoto) {
            const fila = botonEliminarFoto.closest('.fila-foto');
            const contenedorFotos = fila.closest('.contenedor-fotos');

            const imagen = fila.querySelector('.vista-previa-foto img');

            if (imagen?.dataset.urlTemporal) {
                URL.revokeObjectURL(imagen.dataset.urlTemporal);
            }

            fila.remove();
            actualizarEstadoFotos(contenedorFotos);
            return;
        }

        const botonEliminarDetalle = event.target.closest('.btnEliminarDetalle');

        if (botonEliminarDetalle) {
            const tarjeta = botonEliminarDetalle.closest('.detalle-diagnostico');

            tarjeta.querySelectorAll('.vista-previa-foto img').forEach(function (imagen) {
                if (imagen.dataset.urlTemporal) {
                    URL.revokeObjectURL(imagen.dataset.urlTemporal);
                }
            });

            tarjeta.remove();
            actualizarTitulos();
        }
    });

    // Vista previa y validación de cada fotografía.
    contenedorDetalles.addEventListener('change', function (event) {
        if (!event.target.matches('.input-foto')) return;

        const input = event.target;
        const fila = input.closest('.fila-foto');
        const vistaPrevia = fila.querySelector('.vista-previa-foto');
        const imagen = vistaPrevia.querySelector('img');

        if (imagen.dataset.urlTemporal) {
            URL.revokeObjectURL(imagen.dataset.urlTemporal);
            delete imagen.dataset.urlTemporal;
        }

        vistaPrevia.classList.add('d-none');
        imagen.removeAttribute('src');

        const archivo = input.files?.[0];

        if (!archivo) return;

        if (!TIPOS_PERMITIDOS.includes(archivo.type)) {
            alert('Formato no permitido. Selecciona una imagen JPG, PNG o WEBP.');
            input.value = '';
            return;
        }

        if (archivo.size > MAX_TAMANO_FOTO) {
            alert('La fotografía supera los 5 MB permitidos.');
            input.value = '';
            return;
        }

        const urlTemporal = URL.createObjectURL(archivo);

        imagen.src = urlTemporal;
        imagen.dataset.urlTemporal = urlTemporal;
        vistaPrevia.classList.remove('d-none');
    });

    // Evitar envíos repetidos.
    form.addEventListener('submit', function (event) {
        const detalles = contenedorDetalles.querySelectorAll('.detalle-diagnostico');

        if (detalles.length === 0) {
            event.preventDefault();
            alert('Debes registrar al menos un hallazgo o recomendación.');
            return;
        }

        let fotoInvalida = false;

        contenedorDetalles.querySelectorAll('.input-foto').forEach(function (input) {
            const archivo = input.files?.[0];

            if (!archivo) {
                fotoInvalida = true;
                return;
            }

            if (!TIPOS_PERMITIDOS.includes(archivo.type) ||
                archivo.size > MAX_TAMANO_FOTO) {
                fotoInvalida = true;
            }
        });

        if (fotoInvalida) {
            event.preventDefault();
            alert('Revisa las fotografías: cada una debe ser JPG, PNG o WEBP y pesar máximo 5 MB.');
            return;
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        btnGuardar.disabled = true;
        btnGuardar.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span> Guardando diagnóstico...';
    });

    actualizarTitulos();
});
</script>
@endsection