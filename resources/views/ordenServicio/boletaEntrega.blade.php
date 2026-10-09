<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de entrega ENT-{{ str_pad((string) $orden->entrega->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { color: #202a32; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1, h2, h3, p { margin: 0; }
        .header { border-bottom: 2px solid #087f8c; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { color: #087f8c; font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .title { font-size: 22px; font-weight: bold; margin-top: 5px; }
        .meta { color: #52616b; line-height: 1.7; margin-top: 6px; }
        .section { margin-top: 17px; }
        .section-title { border-bottom: 1px solid #b8c5ca; color: #087f8c; font-size: 11px; font-weight: bold; margin-bottom: 8px; padding-bottom: 5px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; }
        .details td { padding: 4px 2px; vertical-align: top; width: 50%; }
        .label { color: #52616b; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .value { margin-top: 2px; }
        .items th, .items td { border-bottom: 1px solid #dce3e6; padding: 7px 5px; text-align: left; }
        .items th { background: #f0f5f6; color: #34454d; font-size: 9px; text-transform: uppercase; }
        .numeric { text-align: right !important; }
        .summary { margin-left: auto; width: 48%; }
        .summary td { padding: 4px 2px; }
        .summary .total { border-top: 1px solid #b8c5ca; font-weight: bold; padding-top: 7px; }
        .status { color: #087f8c; font-size: 12px; font-weight: bold; margin-top: 7px; }
        .work { line-height: 1.6; white-space: pre-line; }
        .signatures { margin-top: 54px; }
        .signatures td { padding: 0 20px; text-align: center; width: 50%; }
        .sign-line { border-top: 1px solid #34454d; padding-top: 7px; }
        .footer { border-top: 1px solid #dce3e6; color: #718087; font-size: 9px; margin-top: 25px; padding-top: 8px; text-align: center; }
    </style>
</head>
<body>
    @php
        $cliente = $orden->vehiculo?->cliente?->user;
        $vehiculo = $orden->vehiculo;
        $numeroBoleta = 'ENT-' . str_pad((string) $orden->entrega->id, 6, '0', STR_PAD_LEFT);
    @endphp

    <div class="header">
        <div class="brand">Taller mecánico</div>
        <div class="title">Boleta de entrega</div>
        <div class="meta">
            N.º {{ $numeroBoleta }}<br>
            Fecha de entrega: {{ $orden->entrega->fecha_entrega?->format('d/m/Y H:i') ?? '-' }}<br>
            Orden de servicio: {{ $orden->numero_orden }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Cliente y vehículo</div>
        <table class="details">
            <tr>
                <td><div class="label">Cliente</div><div class="value">{{ trim(($cliente?->nombres ?? '') . ' ' . ($cliente?->ap_paterno ?? '') . ' ' . ($cliente?->ap_materno ?? '')) ?: '-' }}</div></td>
                <td><div class="label">Teléfono</div><div class="value">{{ $cliente?->celular ?? '-' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Vehículo</div><div class="value">{{ trim(($vehiculo?->marca?->nombre ?? '') . ' ' . ($vehiculo?->modelo ?? '')) ?: '-' }}</div></td>
                <td><div class="label">Tipo</div><div class="value">{{ $vehiculo?->tipo_vehiculo ?? '-' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Placa</div><div class="value">{{ $vehiculo?->placa ?? '-' }}</div></td>
                <td><div class="label">Kilometraje</div><div class="value">{{ $orden->kilometraje !== null ? number_format((float) $orden->kilometraje, 0, ',', '.') . ' km' : '-' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Trabajos realizados</div>
        <div class="work">{{ $orden->reparacion?->trabajos_realizados ?: 'No se registraron trabajos.' }}</div>
    </div>

    <div class="section">
        <div class="section-title">Productos utilizados</div>
        <table class="items">
            <thead><tr><th>Producto</th><th class="numeric">Cantidad</th><th>Unidad</th></tr></thead>
            <tbody>
                @forelse($orden->reparacion?->detalles ?? collect() as $detalle)
                    <tr>
                        <td>{{ $detalle->producto?->nombre ?? 'Producto eliminado' }}</td>
                        <td class="numeric">{{ number_format((float) $detalle->cantidad, 2, ',', '.') }}</td>
                        <td>{{ $detalle->producto?->unidad_medida ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No se registraron productos utilizados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Resumen de pago</div>
        <table class="summary">
            <tr><td>Total</td><td class="numeric">Bs. {{ number_format($resumenPago['total'], 2, ',', '.') }}</td></tr>
            <tr><td>Pagado</td><td class="numeric">Bs. {{ number_format($resumenPago['pagado'], 2, ',', '.') }}</td></tr>
            <tr class="total"><td>Saldo</td><td class="numeric">Bs. {{ number_format($resumenPago['saldo'], 2, ',', '.') }}</td></tr>
        </table>
        <div class="status">ESTADO: {{ $resumenPago['saldo'] <= 0 ? 'PAGADO' : 'CON SALDO PENDIENTE' }}</div>
    </div>

    <table class="signatures">
        <tr>
            <td><div class="sign-line">Firma del cliente</div></td>
            <td><div class="sign-line">{{ $orden->entrega->usuario?->nombres ?? 'Responsable' }}<br>Responsable de entrega</div></td>
        </tr>
    </table>

   
</body>
</html>