<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_herramientas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Stock mínimo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($herramientas as $herramienta)
                <tr>
                    <td>{{ $herramienta->codigo ?? '-' }}</td>
                    <td>{{ $herramienta->nombre }}</td>
                    <td>{{ $herramienta->categoria->nombre ?? '-' }}</td>
                    <td>{{ $herramienta->marca->nombre ?? '-' }}</td>
                    <td>{{ $herramienta->unidad_medida ?? '-' }}</td>
                    <td>{{ $herramienta->cantidad }}</td>
                    <td>{{ $herramienta->stock_minimo }}</td>
                    <td>
                        @if ($herramienta->estado === 'ACTIVO')
                            <span class="badge badge-light-success">Activo</span>
                        @else
                            <span class="badge badge-light-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-icon btn-sm btn-warning btn-circle" title="Editar herramienta" onclick='editarHerramienta(@json($herramienta))'>
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Eliminar herramienta" onclick='eliminarHerramienta(@json($herramienta->id), @json($herramienta->nombre))'>
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
               
                    <h4 colspan="9" class="text-center text-muted">No hay herramientas registradas.</h4>
               
            @endforelse
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#kt_table_herramientas').DataTable({
            lengthMenu: [10, 25, 50, 100],
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
            language: {
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
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