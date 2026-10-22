@extends('layouts.app')
@section('title', 'Cotización')
@section('content')

@php
    $cotizacionActual = $orden->cotizacionActual;

    $detallesIniciales = old(
        'detalles',
        $cotizacionActual
            ? $cotizacionActual->detalles->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'tipo' => $detalle->tipo,
                    'producto_id' => $detalle->producto_id,
                    'descripcion' => $detalle->descripcion,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                ];
            })->values()->all()
            : []
    );
@endphp

<div class="container-fluid">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        Cotización de servicio
                    </h2>
                    <div class="text-muted">
                        Orden:
                        <strong>
                            {{ $orden->numero_orden }}
                        </strong>
                    </div>
                </div>
                <div>
                    <span class="badge badge-light-warning fs-6">
                        EN COTIZACIÓN
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h3 class="card-title fw-bold">
                Información del vehículo
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="fw-bold text-muted">
                        Cliente
                    </label>
                    <div class="fs-6">
                        {{ trim(
                            ($orden->vehiculo->cliente->user->nombres ?? '') . ' ' .
                            ($orden->vehiculo->cliente->user->ap_paterno ?? '') . ' ' .
                            ($orden->vehiculo->cliente->user->ap_materno ?? '')
                        ) }}
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <label class="fw-bold text-muted">
                        Cédula
                    </label>
                    <div class="fs-6">
                        {{ $orden->vehiculo->cliente->user->cedula ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <label class="fw-bold text-muted">
                        Vehículo
                    </label>
                    <div class="fs-6">
                        {{ $orden->vehiculo->marca->nombre ?? '-' }}
                        {{ $orden->vehiculo->modelo }}
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <label class="fw-bold text-muted">
                        Placa
                    </label>
                    <div class="fs-6">
                        {{ $orden->vehiculo->placa }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($orden->diagnosticoActual)
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    Diagnóstico
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-5">
                    <label class="fw-bold text-muted">
                        Descripción
                    </label>
                    <div class="mt-2">
                        {{ $orden->diagnosticoActual->descripcion }}
                    </div>
                </div>
                @if($orden->diagnosticoActual->detalles->count())
                    <div>
                        <label class="fw-bold text-muted mb-3">
                            Hallazgos y recomendaciones
                        </label>
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Tipo</th>
                                        <th>Prioridad</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(
                                        $orden->diagnosticoActual->detalles
                                        as $detalle
                                    )
                                        <tr>
                                           <td>
                                            <div class="fw-bold">
                                                {{ $detalle->descripcion }}
                                            </div>

                                            @if($detalle->fotos && $detalle->fotos->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-3 mt-3">
                                                    @foreach($detalle->fotos as $foto)
                                                        <div style="width: 110px;">
                                                            <a
                                                                href="{{ asset('storage/' . ltrim($foto->ruta, '/')) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                            >
                                                                <img
                                                                    src="{{ asset('storage/' . ltrim($foto->ruta, '/')) }}"
                                                                    alt="{{ $foto->descripcion ?: 'Foto del diagnóstico' }}"
                                                                    class="rounded border w-100"
                                                                    style="height: 85px; object-fit: cover;"
                                                                    loading="lazy"
                                                                >
                                                            </a>

                                                            @if($foto->descripcion)
                                                                <small class="text-muted d-block mt-1">
                                                                    {{ $foto->descripcion }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                            <td>
                                                <span class="badge badge-light-info">
                                                    {{ $detalle->tipo }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-warning">
                                                    {{ $detalle->prioridad }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $detalle->observacion ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
    <form method="POST" action="{{ route('ordenServicio.cotizacion.guardar', $orden->id) }}">
    @csrf
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="fw-bold mb-0">
                        Detalle de cotización
                    </h3>
                </div>

                <div class="card-toolbar">
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnAgregarDetalle">
                        <i class="fa fa-plus me-2"></i>
                        Agregar detalle
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle" id="tablaDetalles">
                        <thead>
                            <tr>
                                <th style="width: 140px;">
                                    Tipo
                                </th>
                                <th>
                                    Producto / Servicio
                                </th>
                                <th style="width: 120px;">
                                    Cantidad
                                </th>
                                <th style="width: 150px;">
                                    Precio unitario
                                </th>
                                <th style="width: 150px;">
                                    Subtotal
                                </th>
                                <th style="width: 60px;">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="detalleContainer">
                        </tbody>
                    </table>
                </div>
               <div class="mb-4">
                <label class="form-label fw-bold">
                    Observaciones
                </label>

                <div class="grupo-dictado">
                    <textarea
                        name="observaciones"
                        id="observaciones_cotizacion"
                        class="form-control campo-dictado"
                        rows="3"
                        placeholder="Observaciones de la cotización..."
                    >{{ old('observaciones', $cotizacionActual?->observaciones) }}</textarea>

                    <div class="d-flex align-items-center gap-2 mt-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-light-primary btn-dictado"
                            data-target="observaciones_cotizacion"
                        >
                            <i class="fa fa-microphone me-1"></i>
                            Dictar observaciones
                        </button>

                        <span
                            id="estadoVoz_observaciones_cotizacion"
                            class="text-muted small"
                            aria-live="polite"
                        ></span>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">
                                Subtotal
                            </span>
                            <span>
                                Bs.
                                <span id="subtotal">
                                    0.00
                                </span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label
                                for="descuento"
                                class="fw-bold">
                                Descuento
                            </label>

                            <div class="input-group"
                                style="width: 160px;">
                                <span class="input-group-text">
                                    Bs.
                                </span>
                                <input
                                    type="number"
                                    name="descuento"
                                    id="descuento"
                                    class="form-control text-end"
                                    value="{{ old('descuento', $cotizacionActual?->descuento ?? 0) }}"
                                    min="0"
                                    step="0.01">
                            </div>
                        </div>
                        <div class="separator my-4"></div>
                        <div class="d-flex justify-content-between">
                            <span class="fs-3 fw-bold">
                                TOTAL
                            </span>
                            <span class="fs-3 fw-bold">
                                Bs.
                                <span id="total">
                                    0.00
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-5">
            <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
                class="btn btn-light">
                Cancelar
            </a>

            <button type="submit"
                class="btn btn-primary">
                <i class="fa fa-save me-2"></i>
                Guardar cotización
            </button>
        </div>
    </form>
</div>

@endsection
@section('js')
<script>
let detalleIndex = 0;

const productos = @json($productos);
const detallesIniciales = @json($detallesIniciales);

function agregarDetalle(detalle = null) {
    const index = detalleIndex++;

    let opcionesProductos = `
        <option value="">Seleccione un producto</option>
    `;

    productos.forEach(producto => {
        const seleccionado =
            detalle?.producto_id == producto.id ? 'selected' : '';

        opcionesProductos += `
            <option value="${producto.id}" ${seleccionado}>
                ${producto.codigo ?? ''} - ${producto.nombre}
            </option>
        `;
    });

    const fila = `
        <tr data-index="${index}">
            <td>
                <input
                    type="hidden"
                    name="detalles[${index}][id]"
                    class="detalle-id"
                    value="${detalle?.id ?? ''}">

                <select
                    name="detalles[${index}][tipo]"
                    class="form-select tipo-detalle"
                    required>
                    <option value="PRODUCTO">Producto</option>
                    <option value="SERVICIO">Servicio</option>
                </select>
            </td>

            <td>
                <div class="producto-container">
                    <select
                        name="detalles[${index}][producto_id]"
                        class="form-select producto-select">
                        ${opcionesProductos}
                    </select>
                </div>

                <div class="servicio-container d-none">
                    <input
                        type="text"
                        name="detalles[${index}][descripcion]"
                        class="form-control descripcion-servicio"
                        maxlength="255"
                        placeholder="Descripción del servicio">
                </div>

                <input
                    type="hidden"
                    name="detalles[${index}][descripcion]"
                    class="descripcion-producto">
            </td>

            <td>
                <input
                    type="number"
                    name="detalles[${index}][cantidad]"
                    class="form-control cantidad text-end"
                    value="${detalle?.cantidad ?? 1}"
                    min="0.01"
                    step="0.01"
                    required>
            </td>

            <td>
                <input
                    type="number"
                    name="detalles[${index}][precio_unitario]"
                    class="form-control precio text-end"
                    value="${detalle?.precio_unitario ?? 0}"
                    min="0"
                    step="0.01"
                    required>
            </td>

            <td>
                <input
                    type="text"
                    class="form-control subtotal-detalle text-end"
                    value="0.00"
                    readonly>
            </td>

            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-icon btn-light-danger btnEliminarDetalle">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#detalleContainer').append(fila);

    const nuevaFila = $('#detalleContainer tr').last();

    configurarFila(nuevaFila);

    // Configurar primero el tipo para habilitar el campo correspondiente.
    nuevaFila.find('.tipo-detalle')
        .val(detalle?.tipo ?? 'PRODUCTO')
        .trigger('change');

    if ((detalle?.tipo ?? 'PRODUCTO') === 'PRODUCTO') {
        nuevaFila.find('.producto-select')
            .val(detalle?.producto_id ?? '')
            .trigger('change');

        // Conserva la descripción guardada incluso si cambió el nombre
        // actual del producto.
        nuevaFila.find('.descripcion-producto')
            .val(detalle?.descripcion ?? '');
    } else {
        nuevaFila.find('.descripcion-servicio')
            .val(detalle?.descripcion ?? '');
    }

    calcularFila(nuevaFila);
    calcularTotales();
}


function configurarFila(fila) {
    const tipo = fila.find('.tipo-detalle');
    const producto = fila.find('.producto-select');
    const cantidad = fila.find('.cantidad');
    const precio = fila.find('.precio');
    const descripcionProducto = fila.find('.descripcion-producto');
    const descripcionServicio = fila.find('.descripcion-servicio');
    tipo.on('change', function () {
        if ($(this).val() === 'PRODUCTO') {
            fila.find('.producto-container')
                .removeClass('d-none');
            fila.find('.servicio-container')
                .addClass('d-none');
            producto.prop('disabled', false);
            descripcionServicio
                .prop('disabled', true)
                .val('');
            descripcionProducto
                .prop('disabled', false);

        } else {
            fila.find('.producto-container')
                .addClass('d-none');

            fila.find('.servicio-container')
                .removeClass('d-none');
            producto.prop('disabled', true);
            descripcionServicio
                .prop('disabled', false);
            descripcionProducto
                .prop('disabled', true)
                .val('');
        }

    });


    producto.on('change', function () {
        const productoSeleccionado =
            productos.find(
                item =>
                    item.id == $(this).val()
            );
        if (productoSeleccionado) {
            descripcionProducto.val(
                productoSeleccionado.nombre
            );

        } else {
            descripcionProducto.val('');
        }
    });

    cantidad.on('input', function () {
        calcularFila(fila);
        calcularTotales();

    });


    precio.on('input', function () {
        calcularFila(fila);
        calcularTotales();
    });


    descripcionServicio.on(
        'input',
        function () {
            calcularTotales();

        }
    );

    calcularFila(fila);
}

function calcularFila(fila) {
    const cantidad =
        parseFloat(
            fila.find('.cantidad').val()
        ) || 0;

    const precio =
        parseFloat(
            fila.find('.precio').val()
        ) || 0;

    const subtotal =
        cantidad * precio;

    fila.find('.subtotal-detalle')
        .val(
            subtotal.toFixed(2)
        );
}


function calcularTotales() {
    let subtotal = 0;
    $('#detalleContainer tr').each(
        function () {
            const cantidad =
                parseFloat(
                    $(this)
                        .find('.cantidad')
                        .val()
                ) || 0;

            const precio =
                parseFloat(
                    $(this)
                        .find('.precio')
                        .val()
                ) || 0;

            subtotal += cantidad * precio;

        }
    );

    const descuento = parseFloat(
            $('#descuento').val()
        ) || 0;

    let total = subtotal - descuento;

    if (total < 0) {
        total = 0;
    }


    $('#subtotal').text(
        subtotal.toFixed(2)
    );


    $('#total').text(
        total.toFixed(2)
    );
}

$('#btnAgregarDetalle').on(
    'click',
    function () {
        agregarDetalle();
    }
);

$(document).on(
    'click',
    '.btnEliminarDetalle',
    function () {
        $(this)
            .closest('tr')
            .remove();

        calcularTotales();
    }
);

$('#descuento').on(
    'input',
    function () {
        calcularTotales();
    }
);

$(document).ready(function () {
    if (detallesIniciales.length > 0) {
        detallesIniciales.forEach(detalle => {
            agregarDetalle(detalle);
        });
    } else {
        agregarDetalle();
    }

    calcularTotales();
});


document.addEventListener('DOMContentLoaded', function () {
    const boton = document.querySelector(
        '[data-target="observaciones_cotizacion"]'
    );

    const textarea = document.getElementById(
        'observaciones_cotizacion'
    );

    const estado = document.getElementById(
        'estadoVoz_observaciones_cotizacion'
    );

    if (!boton || !textarea || !estado) {
        console.warn('[Dictado] No se encontraron los elementos.');
        return;
    }

    const SpeechRecognition =
        window.SpeechRecognition ||
        window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        boton.disabled = true;
        estado.textContent =
            'Tu navegador no admite dictado por voz.';
        return;
    }

    const reconocimiento = new SpeechRecognition();

    reconocimiento.lang = 'es-BO';
    reconocimiento.continuous = true;
    reconocimiento.interimResults = true;

    let escuchando = false;
    let textoBase = '';
    let textoConfirmado = '';

    boton.addEventListener('click', function () {
        if (escuchando) {
            reconocimiento.stop();
            return;
        }

        textoBase = textarea.value.trim();
        textoConfirmado = '';

        reconocimiento.start();
    });

    reconocimiento.onstart = function () {
        escuchando = true;

        boton.classList.remove('btn-light-primary');
        boton.classList.add('btn-light-danger');

        boton.innerHTML =
            '<i class="fa fa-stop me-1"></i> Detener dictado';

        estado.textContent = 'Escuchando... Habla ahora.';
    };

    reconocimiento.onresult = function (event) {
        let textoIntermedio = '';

        for (
            let i = event.resultIndex;
            i < event.results.length;
            i++
        ) {
            const resultado = event.results[i];
            const texto = resultado[0].transcript;

            if (resultado.isFinal) {
                textoConfirmado += texto + ' ';
            } else {
                textoIntermedio += texto;
            }
        }

        const textoNuevo = (
            textoBase +
            (textoBase && textoConfirmado ? ' ' : '') +
            textoConfirmado +
            textoIntermedio
        ).trim();

        textarea.value = textoNuevo;

        textarea.dispatchEvent(
            new Event('input', { bubbles: true })
        );
    };

    reconocimiento.onerror = function (event) {
        console.error('[Dictado] Error:', event.error);

        const mensajes = {
            'not-allowed': 'Debes permitir el acceso al micrófono.',
            'no-speech': 'No se detectó voz. Intenta nuevamente.',
            'audio-capture': 'No se encontró un micrófono disponible.',
            'network': 'Error de conexión durante el reconocimiento.'
        };

        estado.textContent =
            mensajes[event.error] ||
            'No se pudo reconocer la voz: ' + event.error;
    };

    reconocimiento.onend = function () {
        escuchando = false;

        boton.classList.remove('btn-light-danger');
        boton.classList.add('btn-light-primary');

        boton.innerHTML =
            '<i class="fa fa-microphone me-1"></i> Dictar observaciones';

        if (!estado.textContent.startsWith('Debes permitir') &&
            !estado.textContent.startsWith('No se detectó') &&
            !estado.textContent.startsWith('No se encontró') &&
            !estado.textContent.startsWith('Error de conexión') &&
            !estado.textContent.startsWith('No se pudo reconocer')) {
            estado.textContent = 'Dictado finalizado.';
        }
    };
});

</script>
@endsection