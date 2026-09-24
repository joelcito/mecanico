<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_cajas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Usuario</th>
                <th>Sucursal</th>
                <th>Apertura</th>
                <th>Monto Inicial</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Estado</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">
            @forelse ($cajas as $caja)
                <tr>
                    <td>
                        {{ $caja->usuario->nombres ?? 'Sin usuario' }}
                        {{ $caja->usuario->apellidos ?? '' }}
                    </td>
                    <td>
                        {{ $caja->sucursal->nombre ?? '-' }}
                    </td>
                    <td>
                        {{ $caja->fecha_apertura
                            ? $caja->fecha_apertura->format('d/m/Y H:i')
                            : '-' }}
                    </td>
                    <td>
                        Bs.
                        {{ number_format(
                            $caja->monto_apertura,
                            2,
                            '.',
                            ','
                        ) }}
                    </td>
                    <td>
                        Bs.
                        {{ number_format(
                            $caja->total_ingresos,
                            2,
                            '.',
                            ','
                        ) }}
                    </td>
                    <td>
                        Bs.
                        {{ number_format(
                            $caja->total_egresos,
                            2,
                            '.',
                            ','
                        ) }}
                    </td>
                    <td>
                        @if ($caja->estado == 'ABIERTA')
                            <span class="badge badge-light-success">
                                Abierta
                            </span>
                        @elseif ($caja->estado == 'CERRADA')
                            <span class="badge badge-light-secondary">
                                Cerrada
                            </span>
                        @elseif ($caja->estado == 'ANULADA')
                            <span class="badge badge-light-danger">
                                Anulada
                            </span>
                        @else
                            <span class="badge badge-light-warning">
                                {{ $caja->estado }}
                            </span>

                        @endif
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-primary btn-circle"
                            title="Ver caja"
                            onclick="verCaja('{{ $caja->id }}')">
                            <i class="fa fa-eye"></i>
                        </button>

                        @if ($caja->estado == 'ABIERTA')
                            <button class="btn btn-icon btn-sm btn-danger btn-circle"
                                title="Cerrar caja"
                                onclick="cerrarCaja('{{ $caja->id }}')" >
                                <i class="fa fa-lock"></i>
                            </button>

                        @endif
                    </td>
                </tr>
            @empty
                        <h4 class="text-danger">
                            No hay datos
                        </h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_cajas').DataTable({
            lengthMenu: [10, 25, 50, 100],
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
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

    });
</script>
