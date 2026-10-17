@extends('layouts.app')
@section('content')
@php
    $inspeccionActual = $orden->inspeccionActual;
@endphp
<div class="container-fluid">
    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        Inspección del vehículo
                    </h2>
                    <div class="text-muted">
                        Orden:
                        <strong>
                            {{ $orden->numero_orden }}
                        </strong>
                    </div>
                </div>
                <div>
                    <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
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
            <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">
                        Cliente
                    </label>
                    <div>
                        @php
                            $usuario = $orden->vehiculo->cliente->user ?? null;
                        @endphp

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

                <div class="col-md-3 mb-4">

                <label class="form-label fw-bold">
                    Tipo
                </label>
                <div>
                    {{ $orden->vehiculo->tipo_vehiculo }}
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <label class="form-label fw-bold">
                    Propulsión
                </label>
                <div>
                    @if($orden->vehiculo->tipo_propulsion === 'ELECTRICO')
                        <span class="badge badge-light-success">
                            Eléctrico
                        </span>
                    @elseif($orden->vehiculo->tipo_propulsion === 'HIBRIDO')
                        <span class="badge badge-light-warning">
                            Híbrido
                        </span>
                    @else
                        <span class="badge badge-light-primary">
                            Combustión
                        </span>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
    <form method="POST"
          action="{{ route('ordenServicio.inspeccion.guardar', $orden->id) }}"
          enctype="multipart/form-data">
        @csrf
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <div>
                        <h3 class="mb-1">
                            Checklist del vehículo
                        </h3>
                        <span class="text-muted">
                            Marque el estado de cada elemento.
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed">
                        <thead>
                            <tr>
                                <th width="35%">
                                    Elemento
                                </th>
                                <th width="20%">
                                    Resultado
                                </th>
                                <th>
                                    Observación
                            </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items->unique('nombre') as $item)
                                <tr>
                                    <td>
                                        <span class="fw-bold">
                                            {{ $item->nombre }}
                                        </span>
                                    </td>
                                    @php
                                    $detalleExistente = $inspeccionActual?->detalles
                                        ?->firstWhere('item', $item->nombre);

                                    $resultadoActual = old(
                                        "items.{$item->id}.resultado",
                                        $detalleExistente->resultado ?? 'NO'
                                    );

                                    $observacionActual = old(
                                        "items.{$item->id}.observacion",
                                        $detalleExistente->observacion ?? ''
                                    );
                                @endphp

                                <td>
                                    <div class="d-flex align-items-center gap-4">
                                        <label class="form-check form-check-inline mb-0">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="items[{{ $item->id }}][resultado]"
                                                value="SI"
                                                {{ $resultadoActual === 'SI' ? 'checked' : '' }}>
                                            <span class="form-check-label">Sí</span>
                                        </label>

                                        <label class="form-check form-check-inline mb-0">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="items[{{ $item->id }}][resultado]"
                                                value="NO"
                                                {{ $resultadoActual === 'NO' ? 'checked' : '' }}>
                                            <span class="form-check-label">No</span>
                                        </label>

                                        <label class="form-check form-check-inline mb-0">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="items[{{ $item->id }}][resultado]"
                                                value="NA"
                                                {{ $resultadoActual === 'NA' ? 'checked' : '' }}>
                                            <span class="form-check-label">N/A</span>
                                        </label>
                                    </div>
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        name="items[{{ $item->id }}][observacion]"
                                        class="form-control form-control-sm"
                                        placeholder="Observación"
                                        value="{{ $observacionActual }}">
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    Observaciones generales
                </h3>
            </div>
            <div class="card-body">
               <textarea
                    name="observaciones"
                    class="form-control"
                    rows="4"
                    placeholder="Ingrese observaciones generales de la inspección...">
                    {{ old('observaciones', $inspeccionActual->observaciones ?? '') }}</textarea>
            </div>
        </div>

        
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-camera me-2"></i>
                    Fotografías de la inspección
                </h3>
            </div>

            <div class="card-body">
                <label class="form-label fw-bold">Agregar fotografías</label>

                <div class="d-flex flex-wrap gap-3 mb-3">
                   <button type="button" class="btn btn-primary" id="btnAbrirCamaraInspeccion">
                        <i class="fa fa-camera me-2"></i>Tomar foto
                    </button>

                    <button type="button" class="btn btn-light-primary"
                        onclick="document.getElementById('fotosInspeccion').click()">
                        <i class="fa fa-images me-2"></i>Elegir de galería
                    </button>
                </div>

                <input
                    type="file"
                    id="fotosInspeccion"
                    name="fotos[]"
                    class="form-control d-none"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                >

            
                <div class="form-text mb-3">
                    Puedes tomar fotos con la cámara o elegir varias imágenes de tu galería.
                    Las imágenes seleccionadas se mostrarán antes de guardar. Máximo 5 MB por foto.
                </div>

                <h5 class="fw-bold mb-3">Fotos nuevas seleccionadas</h5>
                <div id="vistaPreviaFotos" class="row g-3 mb-4"></div>


                @if($inspeccionActual && $inspeccionActual->fotos->isNotEmpty())
                    <h5 class="fw-bold mb-4">Fotografías guardadas</h5>

                    <div class="row g-4" id="galeriaFotosInspeccion">
                        @foreach($inspeccionActual->fotos as $foto)
                            <div
                                class="col-6 col-md-4 col-lg-3"
                                id="foto-inspeccion-{{ $foto->id }}"
                            >
                                <div class="card h-100 border">
                                    <button
                                        type="button"
                                        class="btn p-0 border-0 bg-light"
                                        onclick="ampliarFotoInspeccion(
                                            '{{ asset('storage/' . $foto->ruta) }}'
                                        )"
                                        title="Ampliar fotografía"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $foto->ruta) }}"
                                            alt="Fotografía de inspección"
                                            class="w-100 rounded"
                                            style="height: 150px; object-fit: cover;"
                                            loading="lazy"
                                        >
                                    </button>

                                    <div class="card-body p-2 d-flex justify-content-center">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light-danger"
                                            onclick="eliminarFotoInspeccion(
                                                    '{{ route('ordenServicio.inspeccion.foto.eliminar', [$orden->id, $foto->id]) }}',
                                                    {{ $foto->id }}
                                                )"
                                        >
                                            <i class="fa fa-trash me-1"></i>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light-info mb-0" id="sinFotosInspeccion">
                        <i class="fa fa-images me-2"></i>
                        Todavía no hay fotografías guardadas para esta inspección.
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-10">
            <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
               class="btn btn-light">
                Cancelar
            </a>
           <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-2"></i>
                {{ $inspeccionActual ? 'Guardar cambios' : 'Finalizar inspección' }}
            </button>
        </div>
    
    </form>
</div>



<div class="modal fade" id="modalCamaraInspeccion" tabindex="-1"
     aria-labelledby="tituloCamaraInspeccion" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloCamaraInspeccion">
                    <i class="fa fa-camera me-2"></i>Tomar fotografía
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <video id="videoCamaraInspeccion"
                       autoplay playsinline
                       style="width:100%;max-height:65vh;object-fit:contain;background:#111;">
                </video>

                <canvas id="canvasCamaraInspeccion" class="d-none"></canvas>

                <img id="capturaCamaraInspeccion"
                     class="d-none w-100"
                     style="max-height:65vh;object-fit:contain;"
                     alt="Vista previa de la fotografía">

                <div id="errorCamaraInspeccion"
                     class="alert alert-danger d-none mt-3"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-primary"
                        id="btnCapturarInspeccion">
                    <i class="fa fa-camera me-1"></i>Capturar
                </button>

                <button type="button" class="btn btn-success d-none"
                        id="btnUsarCapturaInspeccion">
                    <i class="fa fa-check me-1"></i>Usar foto
                </button>

                <button type="button" class="btn btn-light-primary d-none"
                        id="btnRepetirCapturaInspeccion">
                    <i class="fa fa-refresh me-1"></i>Repetir
                </button>
            </div>
        </div>
    </div>
</div>



<script>
    function ampliarFotoInspeccion(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Fotografía de inspección',
            showConfirmButton: false,
            showCloseButton: true,
            width: '900px',
            background: '#fff',
            padding: '1rem'
        });
    }

    function eliminarFotoInspeccion(url, fotoId) {
        Swal.fire({
            title: '¿Eliminar fotografía?',
            text: 'Esta fotografía dejará de aparecer en la inspección.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            })
            .done(function(response) {
                const $galeria = $('#galeriaFotosInspeccion');
                const $contenedor = $galeria.parent();

                $('#foto-inspeccion-' + fotoId).remove();

                if ($galeria.children().length === 0) {
                    $galeria.remove();

                    if ($('#sinFotosInspeccion').length === 0) {
                        $contenedor.append(
                            '<div class="alert alert-light-info mt-4" id="sinFotosInspeccion">' +
                            'Todavía no hay fotografías guardadas para esta inspección.' +
                            '</div>'
                        );
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Fotografía eliminada',
                    text: response.message,
                    timer: 1800,
                    showConfirmButton: false
                });
            })
            .fail(function(xhr) {
                const errores = Object.values(
                    xhr.responseJSON?.errors ?? {}
                ).flat();

                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo eliminar',
                    text: errores[0] ??
                        xhr.responseJSON?.message ??
                        'Ocurrió un error al eliminar la fotografía.'
                });
            });
        });
    }


    
document.addEventListener('DOMContentLoaded', function () {
    const inputGaleria = document.getElementById('fotosInspeccion');
    const contenedorPrevia = document.getElementById('vistaPreviaFotos');
    const botonAbrirCamara = document.getElementById('btnAbrirCamaraInspeccion');

    const elementoModal = document.getElementById('modalCamaraInspeccion');
    const video = document.getElementById('videoCamaraInspeccion');
    const canvas = document.getElementById('canvasCamaraInspeccion');
    const captura = document.getElementById('capturaCamaraInspeccion');
    const botonCapturar = document.getElementById('btnCapturarInspeccion');
    const botonUsar = document.getElementById('btnUsarCapturaInspeccion');
    const botonRepetir = document.getElementById('btnRepetirCapturaInspeccion');
    const errorCamara = document.getElementById('errorCamaraInspeccion');

    if (!inputGaleria || !contenedorPrevia) return;

    let archivosSeleccionados = [];
    let urlsPrevia = [];
    let stream = null;
    let archivoCapturado = null;

    const modal = elementoModal && window.bootstrap
        ? new bootstrap.Modal(elementoModal)
        : null;

    function detenerCamara() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        if (video) video.srcObject = null;
    }

    function mostrarErrorCamara(mensaje) {
        errorCamara.textContent = mensaje;
        errorCamara.classList.remove('d-none');
    }

    function agregarArchivos(archivos) {
        const errores = [];

        Array.from(archivos).forEach(function (archivo) {
            if (!/^image\/(jpeg|png|webp)$/.test(archivo.type)) {
                errores.push(archivo.name + ': formato no permitido.');
                return;
            }

            if (archivo.size > 5 * 1024 * 1024) {
                errores.push(archivo.name + ': supera los 5 MB.');
                return;
            }

            const duplicado = archivosSeleccionados.some(function (existente) {
                return existente.name === archivo.name
                    && existente.size === archivo.size
                    && existente.lastModified === archivo.lastModified;
            });

            if (!duplicado) {
                archivosSeleccionados.push(archivo);
            }
        });

        sincronizarArchivos();
        mostrarPrevia();

        if (errores.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Revisa las fotografías',
                text: errores.join(' ')
            });
        }
    }

    function sincronizarArchivos() {
        const transferencia = new DataTransfer();

        archivosSeleccionados.forEach(function (archivo) {
            transferencia.items.add(archivo);
        });

        inputGaleria.files = transferencia.files;
    }

    function mostrarPrevia() {
        urlsPrevia.forEach(url => URL.revokeObjectURL(url));
        urlsPrevia = [];
        contenedorPrevia.innerHTML = '';

        if (archivosSeleccionados.length === 0) {
            contenedorPrevia.innerHTML =
                '<div class="col-12 text-muted small">' +
                'Todavía no seleccionaste fotografías nuevas.' +
                '</div>';
            return;
        }

        archivosSeleccionados.forEach(function (archivo, indice) {
            const columna = document.createElement('div');
            columna.className = 'col-6 col-md-4 col-lg-3';

            const tarjeta = document.createElement('div');
            tarjeta.className = 'card h-100 border';

            const imagen = document.createElement('img');
            const url = URL.createObjectURL(archivo);
            urlsPrevia.push(url);

            imagen.src = url;
            imagen.alt = archivo.name;
            imagen.className = 'card-img-top';
            imagen.style.height = '160px';
            imagen.style.objectFit = 'cover';
            imagen.style.cursor = 'pointer';

            imagen.addEventListener('click', function () {
                ampliarFotoInspeccion(url);
            });

            const cuerpo = document.createElement('div');
            cuerpo.className = 'card-body p-2';

            const nombre = document.createElement('div');
            nombre.className = 'small text-truncate mb-2';
            nombre.title = archivo.name;
            nombre.textContent = archivo.name;

            const botonQuitar = document.createElement('button');
            botonQuitar.type = 'button';
            botonQuitar.className = 'btn btn-sm btn-light-danger w-100';
            botonQuitar.innerHTML =
                '<i class="fa fa-trash me-1"></i>Quitar';

            botonQuitar.addEventListener('click', function () {
                archivosSeleccionados.splice(indice, 1);
                sincronizarArchivos();
                mostrarPrevia();
            });

            cuerpo.append(nombre, botonQuitar);
            tarjeta.append(imagen, cuerpo);
            columna.appendChild(tarjeta);
            contenedorPrevia.appendChild(columna);
        });
    }

    // Elegir fotos desde la galería, pudiendo agregar más después.
    inputGaleria.addEventListener('change', function () {
        agregarArchivos(inputGaleria.files);
        inputGaleria.value = '';
    });

    // Cámara.
    async function iniciarCamara() {
        detenerCamara();

        archivoCapturado = null;
        captura.classList.add('d-none');
        video.classList.remove('d-none');
        botonCapturar.classList.remove('d-none');
        botonUsar.classList.add('d-none');
        botonRepetir.classList.add('d-none');
        errorCamara.classList.add('d-none');

        if (!navigator.mediaDevices?.getUserMedia) {
            mostrarErrorCamara(
                'La cámara necesita HTTPS o localhost. Abre el sistema desde una conexión segura.'
            );
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: { facingMode: { ideal: 'environment' } }
            });

            video.srcObject = stream;
            await video.play();
        } catch (error) {
            mostrarErrorCamara(
                'No se pudo abrir la cámara. Comprueba los permisos de Chrome.'
            );
        }
    }

    if (botonAbrirCamara && modal) {
        botonAbrirCamara.addEventListener('click', function () {
            modal.show();
        });

        elementoModal.addEventListener('shown.bs.modal', iniciarCamara);
        elementoModal.addEventListener('hidden.bs.modal', detenerCamara);
    }

    botonCapturar.addEventListener('click', function () {
        if (!video.videoWidth) {
            mostrarErrorCamara('Espera a que la cámara esté lista.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        canvas.getContext('2d').drawImage(
            video, 0, 0, canvas.width, canvas.height
        );

        canvas.toBlob(function (blob) {
            if (!blob) {
                mostrarErrorCamara('No se pudo capturar la fotografía.');
                return;
            }

            archivoCapturado = new File(
                [blob],
                'inspeccion-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            captura.src = URL.createObjectURL(blob);
            captura.classList.remove('d-none');
            video.classList.add('d-none');

            botonCapturar.classList.add('d-none');
            botonUsar.classList.remove('d-none');
            botonRepetir.classList.remove('d-none');

            detenerCamara();
        }, 'image/jpeg', 0.9);
    });

    botonRepetir.addEventListener('click', iniciarCamara);

    botonUsar.addEventListener('click', function () {
        if (!archivoCapturado) return;

        // Se incorpora a la misma lista que las fotos de la galería.
        agregarArchivos([archivoCapturado]);
        modal.hide();
    });

    mostrarPrevia();
});





(function () {
    const modalElemento = document.getElementById('modalCamaraInspeccion');
    const botonAbrir = document.getElementById('btnAbrirCamaraInspeccion');
    if (!modalElemento || !botonAbrir) return;

    const modal = new bootstrap.Modal(modalElemento);
    const video = document.getElementById('videoCamaraInspeccion');
    const canvas = document.getElementById('canvasCamaraInspeccion');
    const captura = document.getElementById('capturaCamaraInspeccion');
    const botonCapturar = document.getElementById('btnCapturarInspeccion');
    const botonUsar = document.getElementById('btnUsarCapturaInspeccion');
    const botonRepetir = document.getElementById('btnRepetirCapturaInspeccion');
    const error = document.getElementById('errorCamaraInspeccion');

    let stream = null;
    let archivoCapturado = null;

    function detenerCamara() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    function mostrarError(mensaje) {
        error.textContent = mensaje;
        error.classList.remove('d-none');
    }

    function reiniciarVista() {
        captura.classList.add('d-none');
        video.classList.remove('d-none');
        botonCapturar.classList.remove('d-none');
        botonUsar.classList.add('d-none');
        botonRepetir.classList.add('d-none');
        archivoCapturado = null;
    }

    async function iniciarCamara() {
        error.classList.add('d-none');
        reiniciarVista();

        if (!navigator.mediaDevices?.getUserMedia) {
            mostrarError(
                'La cámara requiere HTTPS o localhost. Abre esta página desde una conexión segura.'
            );
            return;
        }

        try {
            detenerCamara();

            stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    facingMode: { ideal: 'environment' }
                }
            });

            video.srcObject = stream;
            await video.play();
        } catch (e) {
            mostrarError(
                'No se pudo abrir la cámara. Verifica los permisos de Chrome y que ninguna otra aplicación la esté usando.'
            );
        }
    }

    botonAbrir.addEventListener('click', function () {
        modal.show();
    });

    modalElemento.addEventListener('shown.bs.modal', iniciarCamara);

    modalElemento.addEventListener('hidden.bs.modal', detenerCamara);

    botonCapturar.addEventListener('click', function () {
        if (!video.videoWidth || !video.videoHeight) {
            mostrarError('Espera a que la cámara esté lista.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const contexto = canvas.getContext('2d');
        contexto.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function (blob) {
            if (!blob) {
                mostrarError('No se pudo capturar la fotografía.');
                return;
            }

            archivoCapturado = new File(
                [blob],
                'inspeccion-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            captura.src = URL.createObjectURL(blob);
            captura.classList.remove('d-none');
            video.classList.add('d-none');

            botonCapturar.classList.add('d-none');
            botonUsar.classList.remove('d-none');
            botonRepetir.classList.remove('d-none');

            detenerCamara();
        }, 'image/jpeg', 0.9);
    });

    botonRepetir.addEventListener('click', iniciarCamara);

    botonUsar.addEventListener('click', function () {
        if (!archivoCapturado) return;

        // Agrega la foto a la misma selección que utiliza el formulario.
        if (typeof agregarArchivos === 'function') {
            agregarArchivos([archivoCapturado]);
        } else {
            mostrarError('No se encontró la función para agregar la foto a la vista previa.');
            return;
        }

        modal.hide();
    });
})();

</script>

@endsection
