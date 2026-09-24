<div>
    <div class="modal-header">
        <div>
            <h2 class="fw-bold mb-1">
                Detalle del Pago #{{ $pago->id }}
            </h2>
            <span class="text-muted">
                Orden {{ $pago->ordenServicio?->numero_orden ?? '-' }}
            </span>
        </div>
        <div
            class="btn btn-icon btn-sm btn-active-light-primary"
            data-bs-dismiss="modal">
            <i class="ki-duotone ki-cross fs-1"></i>
        </div>
    </div>

    <div class="modal-body">
        <div class="row mb-8">
            <div class="col-md-6 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Orden de Servicio
                </div>
                <div class="fs-5 fw-bold">
                    {{ $pago->ordenServicio?->numero_orden ?? '-' }}
                </div>
            </div>

            <div class="col-md-6 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Fecha
                </div>
                <div class="fs-5">
                    {{ $pago->fecha?->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>

            <div class="col-md-6 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Vehículo
                </div>
                <div class="fs-5">
                    @if($pago->ordenServicio?->vehiculo)
                        {{ $pago->ordenServicio->vehiculo->placa ?? '-' }}
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="col-md-6 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Caja
                </div>
                <div class="fs-5">
                    @if($pago->caja)
                        Caja #{{ $pago->caja->id }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <div class="separator separator-dashed mb-8"></div>
        <h4 class="fw-bold mb-5">
            Información del Pago
        </h4>
        <div class="row mb-8">
            <div class="col-md-4 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Método de Pago
                </div>
                @switch($pago->tipo_pago)
                    @case('EFECTIVO')
                        <span class="badge badge-light-success fs-6">
                            Efectivo
                        </span>
                        @break
                    @case('QR')
                        <span class="badge badge-light-primary fs-6">
                            QR
                        </span>
                        @break
                    @case('TRANSFERENCIA')
                        <span class="badge badge-light-info fs-6">
                            Transferencia
                        </span>
                        @break
                    @default
                        <span class="badge badge-light fs-6">
                            {{ $pago->tipo_pago }}
                        </span>
                @endswitch
            </div>

            <div class="col-md-4 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Monto
                </div>
                <div class="fs-4 fw-bold">
                    Bs. {{ number_format($pago->monto, 2) }}
                </div>
            </div>

            <div class="col-md-4 mb-5">
                <div class="fw-bold text-muted mb-2">
                    Cambio
                </div>
                <div class="fs-4 fw-bold">
                    Bs. {{ number_format($pago->cambio ?? 0, 2) }}
                </div>
            </div>
        </div>

        <div class="separator separator-dashed mb-8"></div>
        <h4 class="fw-bold mb-5">
            Estado
        </h4>
        <div class="mb-8">
            @if($pago->estado === 'ACTIVO')
                <span class="badge badge-light-success fs-6">
                    ACTIVO
                </span>
            @else
                <span class="badge badge-light-danger fs-6">
                    ANULADO
                </span>
            @endif
        </div>

        @if($pago->movimientoCaja)
            <div class="separator separator-dashed mb-8"></div>
            <h4 class="fw-bold mb-5">
                Movimiento de Caja
            </h4>
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="fw-bold text-muted mb-2">
                        Movimiento
                    </div>
                    <div>
                        #{{ $pago->movimientoCaja->id }}
                    </div>
                </div>
                <div class="col-md-4">
                <div class="fw-bold text-muted mb-2">
                        Tipo
                    </div>

                    <div>
                        {{ $pago->movimientoCaja->tipo }}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="fw-bold text-muted mb-2">
                        Estado
                    </div>
                    @if($pago->movimientoCaja->estado === 'ACTIVO')

                        <span class="badge badge-light-success">
                            ACTIVO
                        </span>
                    @else
                        <span class="badge badge-light-danger">
                            ANULADO
                        </span>

                    @endif
                </div>
            </div>
        @endif

        @if($pago->descripcion)
            <div class="separator separator-dashed mb-8"></div>
            <h4 class="fw-bold mb-5">
                Descripción
            </h4>
            <div class="text-gray-700">
                {{ $pago->descripcion }}
            </div>
        @endif

        <div class="separator separator-dashed my-8"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="fw-bold text-muted mb-2">
                    Registrado por
                </div>
                <div>
                    @if($pago->usuarioCreador)
                        {{ $pago->usuarioCreador->nombres ?? '' }}
                        {{ $pago->usuarioCreador->apellidos ?? '' }}
                    @else
                        -
                    @endif
                </div>
            </div>


            <div class="col-md-6">
                <div class="fw-bold text-muted mb-2">
                    Sucursal
                </div>
                <div>
                    {{ $pago->sucursal?->nombre ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        @if($pago->estado === 'ACTIVO')
            <button
                type="button"
                class="btn btn-light-danger"
                onclick="anularPago({{ $pago->id }})">
                <i class="ki-duotone ki-trash fs-2"></i>
                Anular Pago
            </button>
        @endif
        <button
            type="button"
            class="btn btn-light"
            data-bs-dismiss="modal">
            Cerrar
        </button>
    </div>
</div>