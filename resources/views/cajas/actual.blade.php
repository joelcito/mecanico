<div class="modal-header">
    <h3 class="fw-bold">
        Caja #{{ $caja->id }}
    </h3>
    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
    ></button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6 mb-7">
         <label class="fw-bold fs-6 mb-2">
                Usuario
            </label>
            <div class="text-gray-600">
                {{ $caja->usuario->nombres ?? '-' }}
                {{ $caja->usuario->apellidos ?? '' }}
            </div>
        </div>

        <div class="col-md-6 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Sucursal
            </label>
            <div class="text-gray-600">
                {{ $caja->sucursal->nombre ?? '-' }}
            </div>
        </div>

        <div class="col-md-6 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Estado
            </label>
            <div>
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
            </div>
        </div>


        <div class="col-md-6 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Fecha de apertura
            </label>
            <div class="text-gray-600">

                {{ $caja->fecha_apertura
                    ? $caja->fecha_apertura->format('d/m/Y H:i')
                    : '-' }}

            </div>
        </div>


        <div class="col-md-3 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Monto apertura
            </label>
            <div class="fw-bold">
                Bs.
                {{ number_format(
                    $caja->monto_apertura,
                    2,
                    '.',
                    ','
                ) }}

            </div>
        </div>


        <div class="col-md-3 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Ingresos
            </label>

            <div class="fw-bold text-success">

                Bs.
                {{ number_format(
                    $caja->total_ingresos,
                    2,
                    '.',
                    ','
                ) }}

            </div>

        </div>


        <div class="col-md-3 mb-7">
            <label class="fw-bold fs-6 mb-2">
                Egresos
            </label>

            <div class="fw-bold text-danger">

                Bs.
                {{ number_format(
                    $caja->total_egresos,
                    2,
                    '.',
                    ','
                ) }}

            </div>

        </div>


        <div class="col-md-3 mb-7">

            <label class="fw-bold fs-6 mb-2">
                Saldo
            </label>

            <div class="fw-bold text-primary">

                Bs.
                {{ number_format(
                    $caja->monto_apertura
                    + $caja->total_ingresos
                    - $caja->total_egresos,
                    2,
                    '.',
                    ','
                ) }}

            </div>

        </div>

    </div>


    <hr class="my-5">


    {{-- MOVIMIENTOS --}}

    <div class="d-flex align-items-center justify-content-between mb-5">

        <h4 class="fw-bold mb-0">
            Movimientos de Caja
        </h4>

    </div>

    <div style="overflow-x: auto;">
        <table class="table align-middle table-row-dashed fs-7 gy-4" >
            <thead>
                <tr class="text-start text-muted fw-bold fs-8 text-uppercase">
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Origen</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ($caja->movimientos->sortByDesc('id') as $movimiento)
                    <tr>
                        <td>
                            {{ $movimiento->fecha
                                ? $movimiento->fecha->format('d/m/Y H:i')
                                : '-' }}
                        </td>
                        <td>
                            @if ($movimiento->tipo === 'INGRESO')
                                <span class="badge badge-light-success">
                                    Ingreso
                                </span>
                            @elseif ($movimiento->tipo === 'EGRESO')
                                <span class="badge badge-light-danger">
                                    Egreso
                                </span>
                            @else

                                <span class="badge badge-light-warning">
                                    {{ $movimiento->tipo }}
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
                            @if ($movimiento->estado === 'ACTIVO')
                                <span class="badge badge-light-success">
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-light-danger">
                                    Anulado
                                </span>
                            @endif
                        </td>
                        <td>
                            @if (
                                $movimiento->estado === 'ACTIVO'
                                && $caja->estado === 'ABIERTA'
                            )
                                <button
                                    type="button"
                                    class="btn btn-icon btn-sm btn-danger btn-circle"
                                    title="Anular movimiento"
                                    onclick="anularMovimiento('{{ $movimiento->id }}')">
                                    <i class="fa fa-ban"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted" >
                            No hay movimientos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<div class="modal-footer">
    <button
        type="button"
        class="btn btn-light"
        data-bs-dismiss="modal"
    >
        Cerrar
    </button>
</div>


<script>
    function anularMovimiento(id) {
        Swal.fire({
            title: '¿Anular movimiento?',
            text: 'El movimiento quedará registrado como anulado y se revertirá del total de la caja.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (!resultado.isConfirmed) {
                return;
            }
            $.ajax({
            url:
                    "{{ url('movimientosCaja') }}/"
                    + id
                    + "/anular",

                method: "POST",
                success: function (resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Movimiento anulado',
                            text: resultado.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalVerCaja').modal('hide');
                            ajaxListado();
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resultado.message
                        });
                    }
                },


                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            xhr.responseJSON?.message
                            ||
                            'No se pudo anular el movimiento.'
                    });
                }
            });
        });
    }

</script>