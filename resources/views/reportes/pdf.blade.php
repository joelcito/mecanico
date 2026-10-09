<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        @page { margin: 26px 28px; }
        body { color: #26343a; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h1 { color: #087f8c; font-size: 18px; margin: 0 0 8px; }
        .meta { border-bottom: 2px solid #087f8c; line-height: 1.8; margin-bottom: 16px; padding-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #eaf3f4; color: #26343a; font-size: 8px; text-align: left; text-transform: uppercase; }
        th, td { border: 1px solid #d4dfe2; padding: 6px 5px; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafb; }
        .empty { color: #63757d; text-align: center; }
        .footer { color: #718087; font-size: 8px; margin-top: 12px; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <div class="meta">
        <strong>Período:</strong> {{ $periodo }}<br>
        <strong>Sucursal:</strong> {{ $sucursal }}
        @if($tipo === 'productos')<br><strong>Nota:</strong> el stock actual es global; los movimientos sí respetan período y sucursal.@endif
    </div>
    <table>
        <thead><tr>@foreach($columnas as $columna)<th>{{ $columna }}</th>@endforeach</tr></thead>
        <tbody>
            @forelse($registros as $registro)
                <tr>@foreach($columnas as $campo => $columna)<td>{{ $registro[$campo] ?? '-' }}</td>@endforeach</tr>
            @empty
                <tr><td class="empty" colspan="{{ count($columnas) }}">No se encontraron datos para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Generado {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>