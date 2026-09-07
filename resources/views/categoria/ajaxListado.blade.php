<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_categorias">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">
            @forelse ($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->nombre }}</td>
                    <td>
                        {{ $categoria->descripcion ?? '-' }}
                    </td>
                    <td>
                        @if ($categoria->estado == 'ACTIVO')
                            <span class="badge badge-light-success">
                                Activo
                            </span>
                        @else
                            <span class="badge badge-light-danger">
                                Inactivo
                            </span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-warning btn-circle"
                            title="Editar categoría"
                            onclick='editarCategoria(@json($categoria))'>
                            <i class="fa fa-edit"></i>
                        </button>

                        <button
                            class="btn btn-icon btn-sm btn-danger btn-circle"
                            title="Eliminar categoría"
                            onclick='eliminarCategoria("{{ $categoria->id }}", "{{ $categoria->nombre }}")'>
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

        $('#kt_table_categorias').DataTable({
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