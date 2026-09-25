<div style="overflow-x: auto;">

    <table
        class="table align-middle table-row-dashed fs-6 gy-5"
        id="kt_table_cuentas_cobrar"
    >

        <thead>

            <tr>

                <th>Orden</th>

                <th>Cliente</th>

                <th>Vehículo</th>

                <th>Placa</th>

                <th>Total</th>

                <th>Pagado</th>

                <th>Saldo</th>

                <th>Estado</th>

                <th>Acciones</th>

            </tr>

        </thead>

        <tbody>

            @foreach($cuentas as $cuenta)

                <tr>

                    <td>
                        <strong>
                            {{ $cuenta['numero_orden'] }}
                        </strong>
                    </td>

                    <td>
                        {{ $cuenta['cliente'] }}
                    </td>

                    <td>
                        {{ $cuenta['vehiculo'] }}
                    </td>

                    <td>
                        {{ $cuenta['placa'] }}
                    </td>

                    <td>
                        Bs. {{ number_format($cuenta['total'], 2) }}
                    </td>

                    <td>
                        Bs. {{ number_format($cuenta['pagado'], 2) }}
                    </td>

                    <td>

                        <strong>
                            Bs. {{ number_format($cuenta['saldo'], 2) }}
                        </strong>

                    </td>

                    <td>

                        @if($cuenta['estado_pago'] === 'SIN_PAGO')

                            <span class="badge badge-light-danger">
                                SIN PAGO
                            </span>

                        @else

                            <span class="badge badge-light-warning">
                                PARCIAL
                            </span>

                        @endif

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            onclick="registrarPago({{ $cuenta['id'] }})"
                        >

                            <i class="ki-duotone ki-dollar fs-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>

                            Registrar pago

                        </button>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

<script>

$('#kt_table_cuentas_cobrar').DataTable({

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

        lengthMenu:
            'Mostrar _MENU_ registros por página',

        info:
            'Mostrando _START_ a _END_ de _TOTAL_ registros',

        emptyTable:
            'No hay cuentas por cobrar'

    },

    order: [],

    responsive: true

});

</script>