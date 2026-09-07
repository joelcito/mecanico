<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_clientes">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>C.I.</th>
                <th>Celular</th>
                <th>NIT</th>
                <th>Dirección</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">

            @forelse ($clientes as $cliente)

                <tr>
                    <td>{{ $cliente->user?->nombres }}</td>
                    <td>{{ $cliente->user?->ap_paterno }}</td>
                    <td>{{ $cliente->user?->ap_materno }}</td>
                    <td>{{ $cliente->user?->cedula }}</td>
                    <td>{{ $cliente->user?->celular }}</td>
                    <td>{{ $cliente->nit }}</td>
                    <td>{{ $cliente->direccion }}</td>
                    <td>
                        <button
                            class="btn btn-icon btn-sm btn-warning btn-circle"
                            title="Editar cliente"
                            onclick="editarCliente({{ json_encode($cliente) }})">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button
                            class="btn btn-icon btn-sm btn-danger btn-circle"
                            title="Eliminar cliente"
                            onclick="eliminarCliente('{{ $cliente->id }}', '{{ $cliente->user?->nombres }}')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>

             @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#kt_table_clientes').DataTable({
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