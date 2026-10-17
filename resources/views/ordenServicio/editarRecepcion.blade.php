@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Editar recepción - {{ $orden->numero_orden }}</h4>
        </div>

        <div class="card-body">
            <form id="formEditarRecepcion">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Vehículo</label>
                    <select name="vehiculo_id" class="form-select" required>
                        @foreach($vehiculos as $vehiculo)
                            <option
                                value="{{ $vehiculo->id }}"
                                @selected($orden->vehiculo_id == $vehiculo->id)>
                                {{ $vehiculo->placa }}
                                - {{ $vehiculo->marca->nombre ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de recepción</label>
                    <input
                        type="date"
                        name="fecha_recepcion"
                        class="form-control"
                        value="{{ \Carbon\Carbon::parse($orden->fecha_recepcion)->format('Y-m-d') }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kilometraje</label>
                    <input
                        type="number"
                        name="kilometraje"
                        class="form-control"
                        min="0"
                        value="{{ $orden->kilometraje }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Motivo de ingreso</label>
                    <input
                        type="text"
                        name="motivo_ingreso"
                        class="form-control"
                        value="{{ $orden->motivo_ingreso }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nivel de combustible (%)</label>
                    <input
                        type="number"
                        name="nivel_combustible"
                        class="form-control"
                        min="0"
                        max="100"
                        step="0.01"
                        value="{{ $orden->nivel_combustible }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea
                        name="observaciones"
                        class="form-control"
                        rows="3">{{ $orden->observaciones }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar cambios
                </button>

                <a
                    href="{{ route('ordenServicio.detalle', $orden->id) }}"
                    class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formEditarRecepcion')
    .addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = this;
        const boton = form.querySelector('[type="submit"]');
        boton.disabled = true;

        try {
            const response = await fetch(
                @json(route('ordenServicio.recepcion.actualizar', $orden->id)),
                {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector(
                            '[name="_token"]'
                        ).value
                    },
                    body: new FormData(form)
                }
            );

            const resultado = await response.json();

            if (!response.ok) {
                const errores = resultado.errors
                    ? Object.values(resultado.errors).flat().join('\n')
                    : resultado.message || 'No se pudieron guardar los cambios.';

                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: errores
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: resultado.mensaje ||
                      resultado.message ||
                      'Recepción actualizada correctamente.'
            });

            window.location.href =
                @json(route('ordenServicio.detalle', $orden->id));

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al guardar los cambios.'
            });
        } finally {
            boton.disabled = false;
        }
    });
</script>
@endsection