<div style="overflow-x: auto;">
    <table
        class="table align-middle table-row-dashed fs-6 gy-5"
        id="kt_table_ordenes_servicio"
    >
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Orden</th>
                <th>Fecha Recepción</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Placa</th>
                <th>Kilometraje</th>
                <th>Estado</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">

            @forelse ($ordenes as $orden)

                @php
                    $vehiculo = $orden->vehiculo;
                    $cliente = $vehiculo?->cliente;
                    $usuario = $cliente?->user;
                @endphp

                <tr>

                    {{-- ORDEN --}}
                    <td>
                        <span class="fw-bold text-primary">
                            {{ $orden->numero_orden }}
                        </span>
                    </td>

                    {{-- FECHA --}}
                    <td>
                        {{ $orden->fecha_recepcion
                            ? $orden->fecha_recepcion->format('d/m/Y H:i')
                            : '-' }}
                    </td>

                    {{-- CLIENTE --}}
                    <td>

                        @if ($usuario)

                            {{ $usuario->nombres }}
                            {{ $usuario->ap_paterno }}
                            {{ $usuario->ap_materno }}

                        @else

                            -

                        @endif

                    </td>

                    {{-- VEHÍCULO --}}
                    <td>

                        @if ($vehiculo)

                            {{ $vehiculo->marca?->nombre }}
                            {{ $vehiculo->modelo }}

                        @else

                            -

                        @endif

                    </td>

                    {{-- PLACA --}}
                    <td>
                        {{ $vehiculo?->placa ?? '-' }}
                    </td>

                    {{-- KILOMETRAJE --}}
                    <td>

                        @if ($orden->kilometraje !== null)

                            {{ number_format(
                                $orden->kilometraje,
                                0,
                                ',',
                                '.'
                            ) }}

                        @else

                            -

                        @endif

                    </td>

                    {{-- ESTADO --}}
                    <td>

                        @switch($orden->estado)

                            @case('RECIBIDO')

                                <span class="badge badge-light-primary">
                                    Recibido
                                </span>

                                @break

                            @case('EN_DIAGNOSTICO')

                                <span class="badge badge-light-warning">
                                    En diagnóstico
                                </span>

                                @break

                            @case('EN_COTIZACION')

                                <span class="badge badge-light-info">
                                    En cotización
                                </span>

                                @break

                            @case('AUTORIZADO')

                                <span class="badge badge-light-success">
                                    Autorizado
                                </span>

                                @break

                            @case('EN_REPARACION')

                                <span class="badge badge-light-warning">
                                    En reparación
                                </span>

                                @break

                            @case('LISTO')

                                <span class="badge badge-light-success">
                                    Listo
                                </span>

                                @break

                            @case('ENTREGADO')

                                <span class="badge badge-light-dark">
                                    Entregado
                                </span>

                                @break

                            @case('ANULADO')

                                <span class="badge badge-light-danger">
                                    Anulado
                                </span>

                                @break

                            @default

                                <span class="badge badge-light-secondary">
                                    {{ $orden->estado }}
                                </span>

                        @endswitch

                    </td>

                    {{-- ACCIONES --}}
                    <td>

                        <button
                            type="button"
                            class="btn btn-icon btn-sm btn-info btn-circle"
                            title="Ver orden"
                            onclick="verOrden('{{ $orden->id }}')"
                        >
                            <i class="fa fa-eye"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-icon btn-sm btn-warning btn-circle"
                            title="Editar orden"
                            onclick="editarOrden('{{ $orden->id }}')"
                        >
                            <i class="fa fa-edit"></i>
                        </button>

                    </td>

                </tr>

            @empty

                <h4> No hay órdenes de servicio registradas.
                </h4>

            @endforelse

        </tbody>

    </table>

</div>


<script>

    $(document).ready(function () {

        $('#kt_table_ordenes_servicio').DataTable({

            lengthMenu: [10, 25, 50, 100],

            dom:
                '<"dt-head row"' +
                    '<"col-md-6"l>' +
                    '<"col-md-6"f>' +
                '>' +
                '<"clear">' +
                't' +
                '<"dt-footer row"' +
                    '<"col-md-5"i>' +
                    '<"col-md-7"p>' +
                '>',

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


    function verOrden(id) {

    window.location.href =
        "{{ url('/ordenServicio') }}/" + id;

}


    function editarOrden(id) {

        console.log('Editar orden:', id);

    }

</script>