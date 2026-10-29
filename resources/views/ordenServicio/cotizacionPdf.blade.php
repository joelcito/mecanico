<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización de servicio</title>

    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #263238;
            line-height: 1.45;
        }

        .header {
            width: 100%;
            border-bottom: 3px solid #17365d;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header td {
            vertical-align: middle;
        }

        .brand {
            font-size: 19px;
            font-weight: bold;
            color: #17365d;
        }

        .subtitle {
            color: #607080;
            font-size: 9px;
        }

        .document-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #17365d;
        }

        .muted {
            color: #657580;
        }

        .section-title {
            background: #17365d;
            color: white;
            padding: 7px 9px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 8px;
        }

        .info {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 5px 7px;
            border: 1px solid #dce3e8;
            width: 50%;
        }

        .label {
            color: #607080;
            font-size: 8px;
            text-transform: uppercase;
        }

        .value {
            font-weight: bold;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .items th {
            background: #eaf0f5;
            color: #17365d;
            padding: 8px 5px;
            border: 1px solid #ccd6df;
            text-align: left;
        }

        .items td {
            padding: 7px 5px;
            border: 1px solid #dce3e8;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .diagnostic {
            border: 1px solid #dce3e8;
            padding: 9px;
            margin-bottom: 9px;
        }

        .finding-title {
            font-weight: bold;
            color: #17365d;
            font-size: 10px;
        }

        .photos {
            margin-top: 7px;
        }

        .photo {
            display: inline-block;
            width: 125px;
            margin: 4px 7px 4px 0;
            vertical-align: top;
        }

        .photo img {
            width: 120px;
            height: 85px;
            border: 1px solid #ccd6df;
        }

        .photo-caption {
            font-size: 8px;
            color: #657580;
        }

        .totals {
            width: 48%;
            margin-left: auto;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 8px;
            border-bottom: 1px solid #dce3e8;
        }

        .grand-total td {
            background: #17365d;
            color: white;
            font-weight: bold;
            font-size: 12px;
            border: none;
        }

        .observations {
            border: 1px solid #dce3e8;
            padding: 9px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #ccd6df;
            color: #657580;
            font-size: 8px;
            text-align: center;
        }

        .status {
            font-weight: bold;
            color: #17365d;
        }

        .page-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

@php
    $vehiculo = $orden->vehiculo;
    $cliente = $vehiculo?->cliente;
    $diagnostico = $orden->diagnosticoActual;
    $userCliente = $cliente?->user;
    $nombreCliente = $userCliente
    ? trim(implode(' ', array_filter([
        $userCliente->nombres,
        $userCliente->ap_paterno,
        $userCliente->ap_materno,
    ])))
    : 'No registrado';

if ($nombreCliente === '') {
    $nombreCliente = 'No registrado';
}

    $placa = data_get($vehiculo, 'placa')
        ?? data_get($vehiculo, 'numero_placa')
        ?? 'No registrada';

    $marca = data_get($vehiculo, 'marca.nombre')
        ?? 'No registrada';

    $modelo = data_get($vehiculo, 'modelo')
        ?? data_get($vehiculo, 'modelo_vehiculo')
        ?? 'No registrado';

    $moneda = 'Bs';

   
$rutaPublica = function ($ruta) {
    $ruta = ltrim(str_replace('\\', '/', (string) $ruta), '/');

    if (str_starts_with($ruta, 'storage/')) {
        $ruta = substr($ruta, strlen('storage/'));
    }

    return public_path('storage/' . $ruta);
};

@endphp

<table class="header">
    <tr>
        <td>
            <div class="brand">TALLER MECÁNICO</div>
            <div class="subtitle">Servicio técnico y mantenimiento vehicular</div>
        </td>
        <td class="document-title">
            COTIZACIÓN<br>
            <span class="subtitle">
                Orden N.º {{ $orden->id }}
            </span>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <td>
            <div class="label">Fecha de cotización</div>
            <div class="value">
                {{ $cotizacion->fecha?->format('d/m/Y H:i') ?? 'No registrada' }}
            </div>
        </td>
        <td>
            <div class="label">Estado</div>
            <div class="value status">
                {{ $cotizacion->estado }}
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="label">Cliente</div>
            <div class="value">{{ $nombreCliente }}</div>
        </td>
        <td>
            <div class="label">Placa del vehículo</div>
            <div class="value">{{ $placa }}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="label">Marca</div>
            <div class="value">{{ $marca }}</div>
        </td>
        <td>
            <div class="label">Modelo</div>
            <div class="value">{{ $modelo }}</div>
        </td>
    </tr>
</table>

@php
    $tecnico = $orden->diagnosticoActual?->tecnico;
@endphp

<div class="diagnostic"> 
    <strong>Técnico responsable:</strong>
    {{ $tecnico
        ? trim($tecnico->nombres . ' ' . $tecnico->ap_paterno . ' ' . $tecnico->ap_materno)
        : 'No asignado' }}
</div>

<div class="section-title">1. Diagnóstico del vehículo</div>

@if($diagnostico)
    <div class="diagnostic">
        <strong>Descripción general</strong><br>
        {{ $diagnostico->descripcion ?: 'Sin descripción registrada.' }}

        @if($diagnostico->observaciones)
            <br><br>
            <strong>Observaciones</strong><br>
            {{ $diagnostico->observaciones }}
        @endif
    </div>

    @forelse($diagnostico->detalles as $detalle)
        <div class="diagnostic page-break">
            <div class="finding-title">
                {{ $loop->iteration }}. {{ $detalle->descripcion }}
            </div>

            <div>
                <strong>Tipo:</strong>
                {{ $detalle->tipo ?: 'No especificado' }}
                &nbsp; | &nbsp;
                <strong>Prioridad:</strong>
                {{ $detalle->prioridad ?: 'No especificada' }}
            </div>

            @if($detalle->observacion)
                <div style="margin-top: 5px;">
                    <strong>Observación:</strong>
                    {{ $detalle->observacion }}
                </div>
            @endif

          @php
    $consultaFotos = \App\Models\OrdenFoto::where(
        'orden_servicio_id',
        $orden->id
    )
    ->where('tipo', 'DIAGNOSTICO')
    ->where('estado', 'ACTIVO');

    if ($loop->first) {
        $consultaFotos->where(function ($query) use ($detalle) {
            $query->where(
                'orden_diagnostico_detalle_id',
                $detalle->id
            )->orWhereNull('orden_diagnostico_detalle_id');
        });
    } else {
        $consultaFotos->where(
            'orden_diagnostico_detalle_id',
            $detalle->id
        );
    }

    $fotosDiagnostico = $consultaFotos->get();
@endphp

            {{-- @if($fotosDiagnostico->isNotEmpty())
                <div class="photos">
                    <strong>Fotografías del hallazgo</strong>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                        @foreach($fotosDiagnostico->filter(fn ($foto) => !empty($foto->ruta))->chunk(2) as $fila)
                            <tr>
                                @foreach($fila as $foto)
                                    @php
                                        $rutaFoto = $rutaPublica($foto->ruta);
                                    @endphp

                                    <td style="width: 50%; padding: 5px; vertical-align: top; text-align: center;">
                                        @if(is_file($rutaFoto))
                                            <img
                                                src="{{ $rutaFoto }}"
                                                style="width: 220px; height: 130px; border: 1px solid #ccd6df;"
                                                alt="Fotografía del diagnóstico">

                                            @if($foto->descripcion)
                                                <div class="photo-caption">
                                                    {{ $foto->descripcion }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="photo-caption">
                                                No se encontró el archivo de la fotografía.
                                            </div>
                                        @endif
                                    </td>
                                @endforeach

                                @if($fila->count() === 1)
                                    <td style="width: 50%;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif --}}
        @if($fotosDiagnostico->isNotEmpty())
            <div class="photos">
                <strong>Fotografías del hallazgo</strong>

                <table style="width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed;">
                    @foreach($fotosDiagnostico->filter(fn ($foto) => !empty($foto->ruta))->chunk(2) as $fila)
                        <tr>
                            @foreach($fila as $foto)
                                @php
                                    $rutaFoto = $rutaPublica($foto->ruta);
                                @endphp

                                <td style="width: 50%; padding: 6px; vertical-align: top; text-align: center;">
                                    @if(is_file($rutaFoto))
                                        <img
                                            src="{{ $rutaFoto }}"
                                            style="width: 220px; height: 130px; object-fit: cover; border: 1px solid #ccd6df;"
                                            alt="Fotografía del diagnóstico">

                                        @if($foto->descripcion)
                                            <div class="photo-caption" style="text-align: center; margin-top: 4px;">
                                                {{ $foto->descripcion }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="photo-caption">
                                            No se encontró el archivo de la fotografía.
                                        </div>
                                    @endif
                                </td>
                            @endforeach

                            @if($fila->count() === 1)
                                <td style="width: 50%;"></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif


           
        </div>
    @empty
        <p class="muted">No hay hallazgos detallados registrados.</p>
    @endforelse
@else
    <p class="muted">No existe un diagnóstico registrado.</p>
@endif

<div class="section-title">2. Detalle de la cotización</div>

<table class="items">
    <thead>
        <tr>
            <th style="width: 7%;">N.º</th>
            <th style="width: 43%;">Descripción</th>
            <th style="width: 12%;" class="right">Cantidad</th>
            <th style="width: 18%;" class="right">Precio unitario</th>
            <th style="width: 20%;" class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cotizacion->detalles as $detalle)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>
                    <strong>{{ $detalle->descripcion }}</strong><br>
                    <span class="muted">{{ $detalle->tipo }}</span>
                </td>
                <td class="right">{{ $detalle->cantidad }}</td>
                <td class="right">
                    {{ $moneda }} {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}
                </td>
                <td class="right">
                    {{ $moneda }} {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="center">
                    No hay detalles registrados en esta cotización.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Subtotal</td>
        <td class="right">
            {{ $moneda }} {{ number_format((float) $cotizacion->subtotal, 2, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td>Descuento</td>
        <td class="right">
            {{ $moneda }} {{ number_format((float) $cotizacion->descuento, 2, ',', '.') }}
        </td>
    </tr>
    <tr class="grand-total">
        <td>TOTAL</td>
        <td class="right">
            {{ $moneda }} {{ number_format((float) $cotizacion->total, 2, ',', '.') }}
        </td>
    </tr>
</table>

@if($cotizacion->observaciones)
    <div class="observations">
        <strong>Observaciones de la cotización</strong><br>
        {{ $cotizacion->observaciones }}
    </div>
@endif

@if($cotizacion->observacion_respuesta)
    <div class="observations">
        <strong>Observación de la respuesta del cliente</strong><br>
        {{ $cotizacion->observacion_respuesta }}
    </div>
@endif

<div class="footer">
    Documento generado el {{ now()->format('d/m/Y H:i') }}.
    Los trabajos y materiales estarán sujetos a la aprobación de la cotización.
</div>

</body>
</html>