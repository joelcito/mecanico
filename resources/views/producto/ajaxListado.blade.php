<div style="overflow-x: auto;">
    <table
        class="table align-middle table-row-dashed fs-6 gy-5"
        id="kt_table_productos">
        <thead>

            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">

                <th>Código</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Stock mínimo</th>
                <th>Estado</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($productos as $producto)
                <tr>
                    <td>
                        {{ $producto->codigo ?? '-' }}
                    </td>
                    <td>
                        {{ $producto->nombre }}
                    </td>

                    <td>
                        @if ($producto->tipo == 'HERRAMIENTA')
                            <span class="badge badge-light-warning">
                                Herramienta
                            </span>
                        @else
                            <span class="badge badge-light-primary">
                                Producto
                            </span>
                        @endif

                    </td>
                    <td>
                        {{ $producto->categoria->nombre ?? '-' }}
                    </td>
                    <td>
                        {{ $producto->marca->nombre ?? '-' }}
                    </td>
                    <td>
                        {{ $producto->unidad_medida ?? '-' }}
                    </td>
                    <td>
                        {{ $producto->cantidad }}
                    </td>
                    <td>
                        {{ $producto->stock_minimo }}
                    </td>
                    <td>

                        @if ($producto->estado == 'ACTIVO')
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

                        <button
                            class="btn btn-icon btn-sm btn-warning btn-circle"
                            title="Editar producto"
                            onclick='editarProducto(@json($producto))'>

                            <i class="fa fa-edit"></i>

                        </button>
                        <button
                            class="btn btn-icon btn-sm btn-danger btn-circle"
                            title="Eliminar producto"
                            onclick='eliminarProducto("{{ $producto->id }}", "{{ $producto->nombre }}")'>

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
    $('#kt_table_productos').DataTable({
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