@extends('layouts.app')
@section('content')
<div class="container-fluid">

    <div class="card mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        Inspección del vehículo
                    </h2>
                    <div class="text-muted">
                        Orden:
                        <strong>
                            {{ $orden->numero_orden }}
                        </strong>
                    </div>
                </div>
                <div>
                    <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
                       class="btn btn-light">

                        <i class="fa fa-arrow-left me-2"></i>

                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-car me-2"></i>
                Información del vehículo
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">
                        Cliente
                    </label>
                    <div>
                        @php
                            $usuario = $orden->vehiculo->cliente->user ?? null;
                        @endphp

                        {{ $usuario
                            ? trim(
                                $usuario->nombres . ' ' .
                                $usuario->ap_paterno . ' ' .
                                $usuario->ap_materno
                            )
                            : '-' }}
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Cédula
                    </label>
                    <div>
                        {{ $usuario->cedula ?? '-' }}
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Placa
                    </label>
                    <div>
                        <span class="badge badge-light-primary fs-6">
                            {{ $orden->vehiculo->placa }}
                        </span>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="form-label fw-bold">
                        Marca
                    </label>
                    <div>
                        {{ $orden->vehiculo->marca->nombre ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <label class="form-label fw-bold">
                        Modelo
                    </label>
                    <div>
                        {{ $orden->vehiculo->modelo }}
                    </div>
                </div>

                <div class="col-md-3 mb-4">

                <label class="form-label fw-bold">
                    Tipo
                </label>

                <div>
                    {{ $orden->vehiculo->tipo_vehiculo }}
                </div>

            </div>

            <div class="col-md-3 mb-4">

                <label class="form-label fw-bold">
                    Propulsión
                </label>

                <div>

                    @if($orden->vehiculo->tipo_propulsion === 'ELECTRICO')

                        <span class="badge badge-light-success">
                            Eléctrico
                        </span>

                    @elseif($orden->vehiculo->tipo_propulsion === 'HIBRIDO')

                        <span class="badge badge-light-warning">
                            Híbrido
                        </span>

                    @else

                        <span class="badge badge-light-primary">
                            Combustión
                        </span>

                    @endif

                </div>

            </div>


            </div>
        </div>
    </div>


    <form method="POST"
          action="{{ route('ordenServicio.inspeccion.guardar', $orden->id) }}"
          enctype="multipart/form-data">
        @csrf
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <div>
                        <h3 class="mb-1">
                            Checklist del vehículo
                        </h3>
                        <span class="text-muted">
                            Marque el estado de cada elemento.
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed">
                        <thead>
                            <tr>
                                <th width="35%">
                                    Elemento
                                </th>
                                <th width="20%">
                                    Resultado
                                </th>
                                <th>
                                    Observación
                            </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($items as $item)

                            <tr>

                                <td>

                                    <span class="fw-bold">
                                        {{ $item->nombre }}
                                    </span>

                                </td>

                                <td>

                                    <select
                                        name="items[{{ $item->id }}][resultado]"
                                        class="form-select form-select-sm"
                                        required>

                                        <option value="">
                                            Seleccione
                                        </option>

                                        <option value="SI">
                                            SI
                                        </option>

                                        <option value="NO">
                                            NO
                                        </option>

                                        <option value="NA">
                                            N/A
                                        </option>

                                    </select>

                                </td>

                                <td>

                                    <input
                                        type="text"
                                        name="items[{{ $item->id }}][observacion]"
                                        class="form-control form-control-sm"
                                        placeholder="Observación">

                                </td>

                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    Observaciones generales
                </h3>
            </div>
            <div class="card-body">
                <textarea
                    name="observaciones"
                    class="form-control"
                    rows="4"
                    placeholder="Ingrese observaciones generales de la inspección..."></textarea>
            </div>
        </div>

        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-camera me-2"></i>
                    Fotografías
            </h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        Fotografías del vehículo
                    </label>
                    <input
                        type="file"
                        name="fotos[]"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                        multiple>
                    <div class="form-text">
                        Puede seleccionar varias fotografías.
                        Máximo 5 MB por imagen.
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-10">
            <a href="{{ route('ordenServicio.detalle', $orden->id) }}"
               class="btn btn-light">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-2"></i>
                Finalizar inspección
            </button>
        </div>
    </form>
</div>
@endsection