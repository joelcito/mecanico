@extends('layouts.app')

@section('css')
<style>
    .grupo-dictado {
        position: relative;
    }

    .grupo-dictado .campo-dictado {
        padding-right: 48px;
    }

    .btn-dictado {
        position: absolute;
        top: 7px;
        right: 7px;
        z-index: 2;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 6px;
        background: #eef3ff;
        color: #3699ff;
        cursor: pointer;
    }

    .btn-dictado:hover {
        background: #3699ff;
        color: white;
    }

    .btn-dictado.escuchando {
        background: #f1416c;
        color: white;
        animation: pulso-microfono 1s infinite;
    }

    @keyframes pulso-microfono {
        50% {
            box-shadow: 0 0 0 6px rgba(241, 65, 108, 0.18);
        }
    }
</style>

@endsection

@section('content')
<div class="container-fluid">
    @php
        $diagnostico = $orden->diagnosticoActual;
        $detallesGuardados = $diagnostico?->detalles ?? collect();
    @endphp
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

    <div class="grupo-dictado">
        <textarea name="descripcion"
                  class="form-control campo-dictado"
                  rows="5"
                  maxlength="10000"
                  required
                  placeholder="Describa el diagnóstico del vehículo...">{{ old('descripcion', $diagnostico?->descripcion) }}</textarea>

        <button type="button"
                class="btn-dictado"
                title="Dictar descripción"
                aria-label="Dictar descripción">
            <i class="fa fa-microphone"></i>
        </button>
    </div>

    @error('descripcion')
        <div class="text-danger mt-2">{{ $message }}</div>
    @enderror
        </div>

        <div>
            <label class="form-label fw-bold">
                Observaciones generales
            </label>

            <div class="grupo-dictado">
                <textarea name="observaciones"
                        class="form-control campo-dictado"
                        rows="4"
                        maxlength="10000"
                        placeholder="Observaciones adicionales...">{{ old('observaciones', $diagnostico?->observaciones) }}</textarea>

                <button type="button"
                        class="btn-dictado"
                        title="Dictar observaciones"
                        aria-label="Dictar observaciones">
                    <i class="fa fa-microphone"></i>
                </button>
            </div>
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

                    @forelse($detallesGuardados as $indice => $detalle)
                    <div class="detalle-diagnostico border rounded p-4 mb-4"
                        data-detalle-index="{{ $indice }}">

                        <input type="hidden"
                            name="detalles[{{ $indice }}][id]"
                            value="{{ $detalle->id }}">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <strong class="text-primary titulo-detalle">
                                <i class="fa fa-clipboard-list me-2"></i>
                                Hallazgo o recomendación {{ $indice + 1 }}
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

                                <div class="grupo-dictado">
                                    <textarea name="detalles[{{ $indice }}][descripcion]"
                                            class="form-control campo-dictado"
                                            rows="3"
                                            maxlength="1000"
                                            >{{ old("detalles.$indice.descripcion", $detalle->descripcion) }}</textarea>

                                    <button type="button"
                                            class="btn-dictado"
                                            title="Dictar descripción del hallazgo"
                                            aria-label="Dictar descripción del hallazgo">
                                        <i class="fa fa-microphone"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">Tipo</label>
                                <select name="detalles[{{ $indice }}][tipo]"
                                        class="form-select"
                                        required>
                                    <option value="HALLAZGO"
                                        @selected(old("detalles.$indice.tipo", $detalle->tipo) === 'HALLAZGO')>
                                        Hallazgo
                                    </option>
                                    <option value="RECOMENDACION"
                                        @selected(old("detalles.$indice.tipo", $detalle->tipo) === 'RECOMENDACION')>
                                        Recomendación
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">Prioridad</label>
                                <select name="detalles[{{ $indice }}][prioridad]"
                                        class="form-select"
                                        required>
                                    @foreach(['BAJA' => 'Baja', 'MEDIA' => 'Media', 'ALTA' => 'Alta', 'CRITICA' => 'Crítica'] as $valor => $texto)
                                        <option value="{{ $valor }}"
                                            @selected(old("detalles.$indice.prioridad", $detalle->prioridad) === $valor)>
                                            {{ $texto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Observación</label>

                                <div class="grupo-dictado">
                                    <input type="text"
                                        name="detalles[{{ $indice }}][observacion]"
                                        class="form-control campo-dictado"
                                        maxlength="1000"
                                        value="{{ old("detalles.$indice.observacion", $detalle->observacion) }}">

                                    <button type="button"
                                            class="btn-dictado"
                                            title="Dictar observación"
                                            aria-label="Dictar observación">
                                        <i class="fa fa-microphone"></i>
                                    </button>
                                </div>
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

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-primary btnTomarFoto">
                                        <i class="fa fa-camera me-1"></i>
                                        Tomar foto
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-light-primary btnAgregarFoto">
                                        <i class="fa fa-images me-1"></i>
                                        Elegir de galería
                                    </button>
                                </div>
                             </div>

                            <div class="contenedor-fotos" data-fotos-index="{{ $indice }}">
                                <div class="text-muted texto-sin-fotos
                                    {{ $detalle->fotos->isNotEmpty() ? 'd-none' : '' }}">
                                    No se han agregado fotografías.
                                </div>

                                @foreach($detalle->fotos as $foto)
                                    <div class="fila-foto border rounded p-3 mb-3">
                                        <div class="row align-items-start">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Fotografía guardada</label>

                                                <div>
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($foto->ruta) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer">
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($foto->ruta) }}"
                                                            alt="Fotografía del diagnóstico"
                                                            class="img-thumbnail"
                                                            style="max-height:190px;max-width:100%;object-fit:contain;">
                                                    </a>
                                                </div>

                                                <small class="text-muted">
                                                    La fotografía existente se conserva al guardar.
                                                </small>
                                            </div>

                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Descripción</label>
                                                <div class="form-control-plaintext">
                                                    {{ $foto->descripcion ?: 'Sin descripción' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
               

                    
@empty
    <div class="detalle-diagnostico border rounded p-4 mb-4"
         data-detalle-index="0">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <strong class="text-primary titulo-detalle">
                <i class="fa fa-clipboard-list me-2"></i>
                Hallazgo o recomendación 1
            </strong>

            <button type="button"
                    class="btn btn-sm btn-light-danger btnEliminarDetalle">
                <i class="fa fa-trash me-1"></i>
                Eliminar detalle
            </button>
        </div>

        <div class="row">
            {{-- DESCRIPCIÓN --}}
            <div class="col-md-5 mb-3">
                <label class="form-label fw-bold">Descripción</label>

                <div class="grupo-dictado">
                    <textarea name="detalles[0][descripcion]"
                              class="form-control campo-dictado"
                              rows="3"
                              maxlength="1000"
                              required
                              placeholder="Describe el hallazgo o recomendación"></textarea>

                    <button type="button"
                            class="btn-dictado"
                            title="Dictar descripción"
                            aria-label="Dictar descripción">
                        <i class="fa fa-microphone"></i>
                    </button>
                </div>
            </div>

            {{-- TIPO --}}
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Tipo</label>

                <select name="detalles[0][tipo]"
                        class="form-select"
                        required>
                    <option value="HALLAZGO">Hallazgo</option>
                    <option value="RECOMENDACION">Recomendación</option>
                </select>
            </div>

            {{-- PRIORIDAD --}}
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

            {{-- OBSERVACIÓN --}}
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Observación</label>

                <div class="grupo-dictado">
                    <input type="text"
                           name="detalles[0][observacion]"
                           class="form-control campo-dictado"
                           maxlength="1000"
                           placeholder="Detalle adicional">

                    <button type="button"
                            class="btn-dictado"
                            title="Dictar observación"
                            aria-label="Dictar observación">
                        <i class="fa fa-microphone"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- FOTOGRAFÍAS --}}
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

                <div class="d-flex flex-wrap gap-2">
                    <button type="button"
                            class="btn btn-sm btn-primary btnTomarFoto">
                        <i class="fa fa-camera me-1"></i>
                        Tomar foto
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-light-primary btnAgregarFoto">
                        <i class="fa fa-images me-1"></i>
                        Elegir de galería
                    </button>
                </div>
            </div>

            <div class="contenedor-fotos" data-fotos-index="0">
                <div class="text-muted texto-sin-fotos">
                    No se han agregado fotografías.
                </div>
            </div>
        </div>
    </div>
@endforelse

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


<div class="modal fade" id="modalCamaraDiagnostico" tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-camera me-2"></i>Fotografía del diagnóstico
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <video id="videoDiagnostico" autoplay playsinline
                       style="width:100%;max-height:60vh;background:#111;object-fit:contain"></video>

                <canvas id="canvasDiagnostico" class="d-none"></canvas>

                <img id="capturaDiagnostico" class="d-none w-100"
                     style="max-height:60vh;object-fit:contain"
                     alt="Vista previa de la captura">

                <div id="errorCamaraDiagnostico"
                     class="alert alert-danger d-none mt-3"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-primary"
                        id="capturarDiagnostico">
                    <i class="fa fa-camera me-1"></i>Capturar
                </button>

                <button type="button" class="btn btn-light-primary d-none"
                        id="repetirDiagnostico">Repetir</button>

                <button type="button" class="btn btn-success d-none"
                        id="usarCapturaDiagnostico">
                    <i class="fa fa-check me-1"></i>Usar foto
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contenedorDetalles = document.getElementById('contenedorDetalles');
    const btnAgregarDetalle = document.getElementById('btnAgregarDetalle');
    const form = document.getElementById('formDiagnostico');
    const btnGuardar = document.getElementById('btnGuardarDiagnostico');

    const detallesIniciales = contenedorDetalles.querySelectorAll('.detalle-diagnostico');

    let detalleIndex = detallesIniciales.length;
    let fotoIndices = {};

    detallesIniciales.forEach(function (detalle) {
        const indice = Number(detalle.dataset.detalleIndex);

        fotoIndices[indice] = 0;
    });

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

  
function crearFilaFoto(indice, archivoInicial = null) {
    const tarjeta = document.querySelector(
        `.detalle-diagnostico[data-detalle-index="${indice}"]`
    );

    if (!tarjeta) {
        console.error('[Diagnóstico] No se encontró el detalle:', indice);
        return null;
    }

    const contenedor = tarjeta.querySelector('.contenedor-fotos');

    if (!contenedor) return null;

    const filasActuales = contenedor.querySelectorAll('.fila-foto');

    if (filasActuales.length >= MAX_FOTOS_POR_DETALLE) {
        Swal.fire(
            'Límite alcanzado',
            'Máximo 10 fotografías por detalle.',
            'warning'
        );
        return null;
    }

    const numeroFoto = Math.max(
        0,
        ...Array.from(
            contenedor.querySelectorAll('.fila-foto')
        ).map(fila => Number(fila.dataset.numeroFoto ?? -1) + 1)
    );

    const fila = document.createElement('div');
    fila.className = 'fila-foto border rounded p-3 mb-3';
    fila.dataset.numeroFoto = numeroFoto;

   
fila.innerHTML = `
    <div class="row align-items-start">
        <div class="col-md-4 mb-3">
            <label class="form-label fw-bold">Vista previa</label>
            <div class="vista-previa-foto"></div>
            <small class="nombre-foto text-muted d-block mt-2"></small>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label d-block">Acción</label>
            <button type="button"
                    class="btn btn-sm btn-light-danger btnEliminarFoto">
                <i class="fa fa-trash me-1"></i> Quitar
            </button>
        </div>
    </div>

    <input type="file"
        name="detalles[${indice}][fotos][${numeroFoto}][archivo]"
        class="inputFotoDiagnostico d-none"
        accept="image/jpeg,image/png,image/webp">
`;

const input = fila.querySelector('.inputFotoDiagnostico');
const vista = fila.querySelector('.vista-previa-foto');
const nombre = fila.querySelector('.nombre-foto');


function mostrarArchivo(archivo) {
    if (!archivo) return false;

    if (!TIPOS_PERMITIDOS.includes(archivo.type)) {
        Swal.fire(
            'Formato no válido',
            'Selecciona una imagen JPG, PNG o WEBP.',
            'warning'
        );
        return false;
    }

    if (archivo.size > MAX_TAMANO_FOTO) {
        Swal.fire(
            'Archivo demasiado grande',
            'El máximo es 5 MB por fotografía.',
            'warning'
        );
        return false;
    }

    // Mantener el archivo dentro del input para enviarlo al servidor.
    if (input.files.length === 0 || input.files[0] !== archivo) {
        const transferencia = new DataTransfer();
        transferencia.items.add(archivo);
        input.files = transferencia.files;
    }

    const urlAnterior = vista.querySelector('img')?.dataset.urlTemporal;

    if (urlAnterior) {
        URL.revokeObjectURL(urlAnterior);
    }

    vista.innerHTML = '';

    const imagen = document.createElement('img');
    const url = URL.createObjectURL(archivo);

    imagen.src = url;
    imagen.dataset.urlTemporal = url;
    imagen.className = 'img-thumbnail';
    imagen.style.cssText =
        'max-height:190px;max-width:100%;object-fit:contain';

    vista.appendChild(imagen);
    nombre.textContent = archivo.name;

    actualizarEstadoFotos(contenedor);

    console.log('[Foto] Archivo preparado:', {
        nombre: archivo.name,
        tipo: archivo.type,
        bytes: archivo.size,
        input: input.name
    });

    return true;
}

   

    // Detectar cuando se selecciona una fotografía de la galería.
    input.addEventListener('change', function () {
        const archivo = input.files?.[0];

        if (!archivo) {
            fila.remove();
            actualizarEstadoFotos(contenedor);
            return;
        }

        // Validar el archivo y mostrar su vista previa.
        if (!mostrarArchivo(archivo)) {
            input.value = '';
            fila.remove();
            actualizarEstadoFotos(contenedor);
        }
    });
    contenedor.appendChild(fila);
    if (archivoInicial && !mostrarArchivo(archivoInicial)) {
        fila.remove();
        actualizarEstadoFotos(contenedor);
        return null;
    }

    actualizarEstadoFotos(contenedor);
    return fila;

    
  }

document.addEventListener('diagnostico:agregar-foto', function (event) {
    const { indice, archivo } = event.detail;

    crearFilaFoto(indice, archivo);
});


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

                        <div class="grupo-dictado">
                            <textarea
                                name="detalles[${indice}][descripcion]"
                                class="form-control campo-dictado"
                                rows="3"
                                maxlength="1000"
                                required
                                placeholder="Describe el problema o trabajo recomendado"></textarea>

                            <button type="button"
                                    class="btn-dictado"
                                    title="Dictar descripción">
                                <i class="fa fa-microphone"></i>
                            </button>
                        </div>
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

                        <div class="grupo-dictado">
                            <input
                                type="text"
                                name="detalles[${indice}][observacion]"
                                class="form-control campo-dictado"
                                maxlength="1000"
                                placeholder="Detalle adicional">

                            <button type="button"
                                    class="btn-dictado"
                                    title="Dictar observación">
                                <i class="fa fa-microphone"></i>
                            </button>
                        </div>
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

                       <div class="d-flex flex-wrap gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-primary btnTomarFoto">
                                <i class="fa fa-camera me-1"></i>
                                Tomar foto
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-light-primary btnAgregarFoto">
                                <i class="fa fa-images me-1"></i>
                                Elegir de galería
                            </button>
                        </div>
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



contenedorDetalles.addEventListener('click', function (event) {
    const objetivo = event.target;

    if (!(objetivo instanceof Element)) return;

    // ELIMINAR DETALLE
    const botonDetalle = objetivo.closest('.btnEliminarDetalle');

    if (botonDetalle) {
        const detalle = botonDetalle.closest('.detalle-diagnostico');

        if (!detalle) return;

        Swal.fire({
            title: '¿Eliminar detalle?',
            text: 'Se quitará del formulario. Guarda los cambios para confirmar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;

            detalle.querySelectorAll('img[data-url-temporal]').forEach(img => {
                URL.revokeObjectURL(img.dataset.urlTemporal);
            });

            detalle.remove();
            actualizarTitulos();
        });

        return;
    }

    // ELEGIR FOTO DE GALERÍA
    const botonGaleria = objetivo.closest('.btnAgregarFoto');

    if (botonGaleria) {
        const tarjeta = botonGaleria.closest('.detalle-diagnostico');
        const contenedorFotos = tarjeta?.querySelector('.contenedor-fotos');

        if (!tarjeta || !contenedorFotos) return;

        const indice = Number(tarjeta.dataset.detalleIndex);

        if (
            contenedorFotos.querySelectorAll('.fila-foto').length >=
            MAX_FOTOS_POR_DETALLE
        ) {
            Swal.fire(
                'Límite alcanzado',
                'Máximo 10 fotografías por detalle.',
                'warning'
            );
            return;
        }

        const fila = crearFilaFoto(indice);

        if (!fila) return;

        fila.querySelector('.inputFotoDiagnostico')?.click();
        return;
    }

    // ELIMINAR FOTO
    const botonEliminarFoto = objetivo.closest('.btnEliminarFoto');

    if (botonEliminarFoto) {
        const fila = botonEliminarFoto.closest('.fila-foto');
        const tarjeta = botonEliminarFoto.closest('.detalle-diagnostico');

        if (!fila) return;

        fila.querySelectorAll('img[data-url-temporal]').forEach(img => {
            URL.revokeObjectURL(img.dataset.urlTemporal);
        });

        fila.remove();

        const contenedorFotos = tarjeta?.querySelector('.contenedor-fotos');

        if (contenedorFotos) {
            actualizarEstadoFotos(contenedorFotos);
        }
    }
});



   
    actualizarTitulos();
});


(function () {
    const elementoModal = document.getElementById('modalCamaraDiagnostico');
    if (!elementoModal) return;

    const modal = new bootstrap.Modal(elementoModal);
    const video = document.getElementById('videoDiagnostico');
    const canvas = document.getElementById('canvasDiagnostico');
    const imagen = document.getElementById('capturaDiagnostico');
    const botonCapturar = document.getElementById('capturarDiagnostico');
    const botonRepetir = document.getElementById('repetirDiagnostico');
    const botonUsar = document.getElementById('usarCapturaDiagnostico');
    const error = document.getElementById('errorCamaraDiagnostico');

    let stream = null;
    let fotoTemporal = null;
    let detalleDestino = null;

    function detenerCamara() {
        stream?.getTracks().forEach(track => track.stop());
        stream = null;
        video.srcObject = null;
    }

    function reiniciar() {
        video.classList.remove('d-none');
        imagen.classList.add('d-none');
        botonCapturar.classList.remove('d-none');
        botonRepetir.classList.add('d-none');
        botonUsar.classList.add('d-none');
        error.classList.add('d-none');
        fotoTemporal = null;
    }

    async function iniciarCamara() {
        reiniciar();

        if (!navigator.mediaDevices?.getUserMedia) {
            error.textContent =
                'La cámara necesita HTTPS y permiso del navegador.';
            error.classList.remove('d-none');
            return;
        }

        try {
            detenerCamara();

            stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: { facingMode: { ideal: 'environment' } }
            });

            video.srcObject = stream;
            await video.play();
        } catch (e) {
            error.textContent =
                'No se pudo abrir la cámara. Revisa los permisos de Chrome.';
            error.classList.remove('d-none');
        }
    }

    document.addEventListener('click', function (event) {
        const boton = event.target.closest('.btnTomarFoto');
        if (!boton) return;

        const tarjeta = boton.closest('.detalle-diagnostico');
        detalleDestino = Number(tarjeta.dataset.detalleIndex);
        modal.show();
    });

    elementoModal.addEventListener('shown.bs.modal', iniciarCamara);
    elementoModal.addEventListener('hidden.bs.modal', detenerCamara);

    botonCapturar.addEventListener('click', function () {
        if (!video.videoWidth) return;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(
            video, 0, 0, canvas.width, canvas.height
        );

        canvas.toBlob(function (blob) {
            if (!blob) return;

            fotoTemporal = new File(
                [blob],
                'diagnostico-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            imagen.src = URL.createObjectURL(blob);
            imagen.classList.remove('d-none');
            video.classList.add('d-none');
            botonCapturar.classList.add('d-none');
            botonRepetir.classList.remove('d-none');
            botonUsar.classList.remove('d-none');

            detenerCamara();
        }, 'image/jpeg', 0.9);
    });

    botonRepetir.addEventListener('click', iniciarCamara);

    

botonUsar.addEventListener('click', function () {
    console.log('[Cámara] Usar foto pulsado');

    if (!fotoTemporal || detalleDestino === null) {
        console.error('[Cámara] No hay fotografía o detalle seleccionado');
        return;
    }

    // La función está dentro del primer DOMContentLoaded.
    // Se invoca mediante un evento para mantener el alcance correcto.
    document.dispatchEvent(new CustomEvent('diagnostico:agregar-foto', {
        detail: {
            indice: detalleDestino,
            archivo: fotoTemporal
        }
    }));

    fotoTemporal = null;
    modal.hide();
});


})();


(function () {
    console.log('[Dictado] Script cargado');

    const SpeechRecognition =
        window.SpeechRecognition || window.webkitSpeechRecognition;

    let reconocimiento = null;
    let botonActivo = null;
    let campoActivo = null;
    let textoInicial = '';

    function mensaje(texto) {
        if (window.Swal) {
            Swal.fire({
                icon: 'info',
                title: 'Dictado por voz',
                text: texto
            });
        } else {
            alert(texto);
        }
    }

    function estado(texto) {
        if (!botonActivo) return;

        let indicador = botonActivo.parentElement.querySelector(
            '.estado-dictado'
        );

        if (!indicador) {
            indicador = document.createElement('small');
            indicador.className = 'estado-dictado d-block mt-2';
            botonActivo.parentElement.appendChild(indicador);
        }

        indicador.textContent = texto;
    }

    function limpiar() {
        if (botonActivo) {
            botonActivo.classList.remove('escuchando');
            botonActivo.innerHTML = '<i class="fa fa-microphone"></i>';
            botonActivo.title = 'Dictar por voz';
        }

        botonActivo = null;
        campoActivo = null;
        reconocimiento = null;
    }

    document.addEventListener('click', function (event) {
        const boton = event.target.closest('.btn-dictado');

        if (!boton) return;

        event.preventDefault();

        console.log('[Dictado] Botón pulsado');

        if (!SpeechRecognition) {
            mensaje(
                'Este navegador no ofrece reconocimiento de voz. ' +
                'Comprueba que estés usando Chrome actualizado.'
            );
            return;
        }

        if (boton === botonActivo && reconocimiento) {
            reconocimiento.stop();
            return;
        }

        if (reconocimiento) {
            const anterior = reconocimiento;
            reconocimiento = null;

            anterior.onend = null;

            try {
                anterior.abort();
            } catch (error) {}

            limpiar();
        }

        const grupo = boton.closest('.grupo-dictado');
        const campo = grupo
            ? grupo.querySelector('.campo-dictado')
            : null;

        if (!campo) {
            console.error('[Dictado] No se encontró el campo de texto');
            mensaje('No se encontró el campo asociado al micrófono.');
            return;
        }

        botonActivo = boton;
        campoActivo = campo;
        textoInicial = campo.value.trim();

        const motor = new SpeechRecognition();
        reconocimiento = motor;

        motor.lang = 'es-ES';
        motor.continuous = false;
        motor.interimResults = true;
        motor.maxAlternatives = 1;

        let textoConfirmado = '';
        let ultimoIntermedio = '';

        boton.classList.add('escuchando');
        boton.innerHTML = '<i class="fa fa-stop"></i>';
        boton.title = 'Detener dictado';

        estado('Iniciando micrófono...');

        motor.onstart = function () {
            console.log('[Dictado] Reconocimiento iniciado');
            estado('Escuchando... habla ahora.');
        };

        motor.onaudiostart = function () {
            console.log('[Dictado] Audio recibido');
            estado('Micrófono activo. Habla ahora.');
        };

        motor.onspeechstart = function () {
            console.log('[Dictado] Se detectó voz');
            estado('Te estoy escuchando...');
        };

        motor.onspeechend = function () {
            console.log('[Dictado] Terminó de detectar voz');
        };

        motor.onsoundstart = function () {
            console.log('[Dictado] Detectó sonido');
        };

        motor.onsoundend = function () {
            console.log('[Dictado] Terminó el sonido');
        };

        motor.onnomatch = function (event) {
            console.warn('[Dictado] No encontró coincidencia:', event);
        };

        

        motor.onresult = function (event) {
            console.log('[Dictado] Evento recibido:', event);

            let textoNuevo = '';

            for (let i = 0; i < event.results.length; i++) {
                const resultado = event.results[i];
                const texto = resultado[0].transcript.trim();

                if (!texto) continue;

                textoNuevo += (textoNuevo ? ' ' : '') + texto;
            }

            if (textoNuevo && campoActivo) {
                campoActivo.value = [textoInicial, textoNuevo]
                    .filter(Boolean)
                    .join(' ');

                campoActivo.dispatchEvent(
                    new Event('input', { bubbles: true })
                );

                campoActivo.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                textoConfirmado = textoNuevo;
                ultimoIntermedio = '';

                estado('Texto reconocido correctamente.');
            }

            for (let i = 0; i < event.results.length; i++) {
                const resultado = event.results[i];
                const texto = resultado[0].transcript.trim();

                console.log('[Dictado] Resultado:', {
                    texto: texto,
                    final: resultado.isFinal
                });

                if (texto) {
                    textoNuevo += (textoNuevo ? ' ' : '') + texto;
                }
            }

            if (textoNuevo && campoActivo) {
                const textoActual = textoInicial.trim();
                campoActivo.value = [textoActual, textoNuevo]
                    .filter(Boolean)
                    .join(' ');

                campoActivo.dispatchEvent(
                    new Event('input', { bubbles: true })
                );
                campoActivo.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                console.log('[Dictado] Texto escrito:', campoActivo.value);
                estado('Texto reconocido correctamente.');
            }
        };



        motor.onerror = function (event) {
            console.error('[Dictado] Error:', event.error);

            const errores = {
                'not-allowed': 'Chrome no permite acceder al micrófono.',
                'service-not-allowed': 'El servicio de reconocimiento está bloqueado.',
                'network': 'El servicio de voz falló por un problema de red.',
                'no-speech': 'No detecté voz. Habla después de iniciar el micrófono.',
                'audio-capture': 'No se encontró un micrófono disponible.',
                'language-not-supported': 'El idioma de reconocimiento no está disponible.',
                'aborted': 'Se detuvo el reconocimiento.'
            };

            estado('Error: ' + (errores[event.error] || event.error));

            if (event.error !== 'aborted') {
                mensaje(errores[event.error] || 'Error: ' + event.error);
            }
        };

        motor.onend = function () {
            console.log('[Dictado] Reconocimiento finalizado');

            if (campoActivo && !textoConfirmado && !ultimoIntermedio) {
                estado('No se detectó texto. Intenta hablar de nuevo.');
            }

            limpiar();
        };

        try {
            motor.start();
        } catch (error) {
            console.error('[Dictado] No se pudo iniciar:', error);
            limpiar();
            mensaje('No se pudo iniciar el reconocimiento: ' + error.message);
        }
    });
})();

</script>
@endsection