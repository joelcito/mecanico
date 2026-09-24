<div style="overflow-x: auto;">

    <table
        class="table align-middle table-row-dashed fs-6 gy-5"
        id="kt_table_pagos">

        <thead>

            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Fecha</th>
                <th>Orden</th>
                <th>Vehículo</th>
                <th>Caja</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">
            @forelse($pagos as $pago)
            <tr>
                    <td>
                        {{ $pago->fecha?->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <span class="fw-bold">
                            #{{ $pago->ordenServicio?->numero_orden }}
                        </span>
                    </td>
                    <td>
                        @if($pago->ordenServicio?->vehiculo)
                            {{ $pago->ordenServicio->vehiculo->placa ?? '-' }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if($pago->caja)
                            Caja #{{ $pago->caja->id }}
                            @if($pago->caja->sucursal)
                                <br>
                                <small class="text-muted">

                                    {{ $pago->caja->sucursal->nombre }}

                                </small>
                            @endif
                        @else
                            -
                        @endif

                    </td>
                    <td>
                        @switch($pago->tipo_pago)
                            @case('EFECTIVO')
                                <span class="badge badge-light-success">
                                    Efectivo
                                </span>
                                @break
                            @case('QR')
                                <span class="badge badge-light-primary">
                                    QR
                                </span>

                                @break
                            @case('TRANSFERENCIA')
                                <span class="badge badge-light-info">
                                    Transferencia
                                </span>
                                @break
                            @default
                                <span class="badge badge-light">
                                    {{ $pago->tipo_pago }}
                                </span>
                        @endswitch

                    </td>

                    <td>
                        <span class="fw-bold">
                            Bs. {{ number_format($pago->monto, 2) }}
                        </span>
                    </td>


                    <td>
                        @if($pago->estado === 'ACTIVO')
                            <span class="badge badge-light-success">
                                ACTIVO
                            </span>
                        @else
                            <span class="badge badge-light-danger">
                                ANULADO
                            </span>
                        @endif
                    </td>

                    <td>
                        <button
                            type="button"
                            class="btn btn-sm btn-light-primary me-2"
                            onclick="verPago({{ $pago->id }})">
                            <i class="ki-duotone ki-eye fs-3"></i>
                            Ver
                        </button>

                        @if($pago->estado === 'ACTIVO')
                            <button
                                type="button"
                                class="btn btn-sm btn-light-danger"
                                onclick="anularPago({{ $pago->id }})">
                                <i class="ki-duotone ki-trash fs-3"></i>
                                Anular
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <h4>
                        No hay pagos registrados.
                </h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $('#kt_table_pagos').DataTable({
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        dom:
            '<"dt-head row"<"col-md-6"l><"col-md-6"f>>' +
            '<"clear">' +
            't' +
            '<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
        language: {
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior'

            },
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros por página',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            emptyTable: 'No hay datos disponibles'

        },
        order: [],
        responsive: true

    });

</script>