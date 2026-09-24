<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5"
        id="kt_table_movimientos_caja" >

        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Fecha</th>
                <th>Caja</th>
                <th>Tipo</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Origen</th>
                <th>Descripción</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">
            @forelse ($movimientos as $movimiento)
                <tr>
                    <td>
                        {{ $movimiento->fecha
                            ? $movimiento->fecha->format('d/m/Y H:i')
                            : '-' }}
                    </td>
                    <td>
                        Caja #{{ $movimiento->caja_id }}
                        <br>
                        <small class="text-muted">

                            {{ $movimiento->caja->sucursal->nombre ?? '-' }}
                        </small>
                    </td>

                    <td>
                        @if ($movimiento->tipo == 'INGRESO')
                            <span class="badge badge-light-success">
                                Ingreso
                            </span>
                        @else
                            <span class="badge badge-light-danger">
                                Egreso
                            </span>
                        @endif
                    </td>
                    <td>
                        {{ $movimiento->metodo_pago ?? '-' }}
                    </td>
                    <td>
                        Bs.
                        {{ number_format(
                            $movimiento->monto,
                            2,
                            '.',
                            ','
                        ) }}

                    </td>
                    <td>
                        {{ $movimiento->origen_dinero ?? '-' }}
                    </td>
                    <td>
                        {{ $movimiento->descripcion ?? '-' }}
                    </td>
                    <td>
                        <button
                            class="btn btn-icon btn-sm btn-warning btn-circle"
                            title="Ver movimiento"
                            onclick="verMovimiento('{{ $movimiento->id }}')" >
                            <i class="fa fa-eye"></i>
                        </button>
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
        $('#kt_table_movimientos_caja').DataTable({
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
                lengthMenu:
                    'Mostrar _MENU_ registros por página',
                info:
                    'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable:
                    'No hay datos disponibles'

            },
            order: [],
            responsive: true

        });
    });


    function verMovimiento(id) {
        Swal.fire({
            icon: 'info',
            title: 'Movimiento',
            text: 'Movimiento #' + id
        });
    }

</script>
