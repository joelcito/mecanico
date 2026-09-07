@extends('layouts.app')
@section('css')

    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
        rel="stylesheet"
        type="text/css" />

    <style>
        .tamanio_boton {
            font-size: 6px;
        }
    </style>

@endsection
@section('metadatos')

    <meta name="csrf-token" content="{{ csrf_token() }}" />

@endsection
@section('content')

<div class="modal fade"
    id="modalProducto"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">
                    FORMULARIO DE PRODUCTO
                    <span
                        class="text-info"
                        id="nombre_busqueda"
                    ></span>
                </h3>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body scroll-y">

                <form
                    id="formularioProducto"
                    enctype="multipart/form-data"
                >
                    <input
                        type="hidden"
                        name="id"
                        id="id"
                        value="0"
                    >


                    {{-- CÓDIGO --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Código
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="codigo"
                                    name="codigo"
                                >

                            </div>

                        </div>


                        {{-- NOMBRE --}}

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Nombre
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="nombre"
                                    name="nombre"
                                >

                            </div>

                        </div>

                    </div>


                    {{-- TIPO / CATEGORÍA --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Tipo
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="tipo"
                                    name="tipo"
                                >

                                    <option value="">
                                        Seleccione...
                                    </option>

                                    <option value="PRODUCTO">
                                        Producto
                                    </option>

                                    <option value="HERRAMIENTA">
                                        Herramienta
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Categoría
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="categoria_id"
                                    name="categoria_id"
                                >

                                    <option value="">
                                        Seleccione una categoría...
                                    </option>

                                    @foreach($categorias as $categoria)

                                        <option
                                            value="{{ $categoria->id }}"
                                        >
                                            {{ $categoria->nombre }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>


                    {{-- MARCA / UNIDAD --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Marca
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="marca_id"
                                    name="marca_id"
                                >

                                    <option value="">
                                        Seleccione una marca...
                                    </option>

                                    @foreach($marcas as $marca)

                                        <option
                                            value="{{ $marca->id }}"
                                        >
                                            {{ $marca->nombre }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Unidad de medida
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="unidad_medida"
                                    name="unidad_medida"
                                >

                                    <option value="">
                                        Seleccione...
                                    </option>

                                    <option value="UNIDAD">
                                        Unidad
                                    </option>

                                    <option value="LITRO">
                                        Litro
                                    </option>

                                    <option value="GALON">
                                        Galón
                                    </option>

                                    <option value="CAJA">
                                        Caja
                                    </option>

                                    <option value="JUEGO">
                                        Juego
                                    </option>

                                    <option value="PAR">
                                        Par
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    {{-- CANTIDAD / STOCK MÍNIMO --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Cantidad
                                </label>

                                <input
                                    type="number"
                                    class="form-control form-control-sm"
                                    id="cantidad"
                                    name="cantidad"
                                    min="0"
                                    step="0.01"
                                    value="0"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="required fw-semibold fs-6 mb-2">
                                    Stock mínimo
                                </label>

                                <input
                                    type="number"
                                    class="form-control form-control-sm"
                                    id="stock_minimo"
                                    name="stock_minimo"
                                    min="0"
                                    step="0.01"
                                    value="0"
                                >

                            </div>

                        </div>

                    </div>


                    {{-- IMAGEN / ESTADO --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="fw-semibold fs-6 mb-2">
                                    Imagen
                                </label>

                                <input
                                    type="file"
                                    class="form-control form-control-sm"
                                    id="imagen"
                                    name="imagen"
                                    accept="image/*"
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="fv-row mb-7">

                                <label class="fw-semibold fs-6 mb-2">
                                    Estado
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    id="estado"
                                    name="estado"
                                >

                                    <option value="ACTIVO">
                                        Activo
                                    </option>

                                    <option value="INACTIVO">
                                        Inactivo
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    {{-- DESCRIPCIÓN --}}

                    <div class="row">

                        <div class="col-md-12">

                            <div class="fv-row mb-7">

                                <label class="fw-semibold fs-6 mb-2">
                                    Descripción
                                </label>

                                <textarea
                                    class="form-control form-control-sm"
                                    id="descripcion"
                                    name="descripcion"
                                    rows="3"
                                ></textarea>

                            </div>

                        </div>

                    </div>

                </form>

            </div>


            <div class="modal-footer">

                <div class="row w-100">

                    <div class="col-md-12">

                        <button
                            type="button"
                            class="btn btn-sm w-100 btn-success"
                            onclick="guardarProducto()"
                        >
                            Guardar
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="d-flex flex-column flex-column-fluid">

    <div
        id="kt_app_content"
        class="app-content flex-column-fluid"
    >

        <div
            id="kt_app_content_container"
            class="app-container container-xxlg"
        >

            <div class="card shadow-sm">

                <div
                    class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between"
                >

                    <h3 class="card-title fw-bold">
                        Listado de Productos
                    </h3>


                    <div class="card-toolbar">

                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="modalNuevoProducto()"
                        >

                            <i class="fa fa-plus"></i>

                            Nuevo Producto

                        </button>

                    </div>

                </div>


                <div
                    class="card-body py-4"
                    id="table_listado"
                >

                </div>
            </div>
        </div>
    </div>
</div>

@stop()

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>

    $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN':
                $('meta[name="csrf-token"]').attr('content')

        }

    });


  
    $(document).ready(function () {

        ajaxListado();

    });


 

    function ajaxListado() {

        let datos = {};
        $.ajax({
            url: "{{ route('producto.ajaxListado') }}",
            method: "POST",
            data: datos,
            success: function (resultado) {
                if (resultado.estado) {
                    $('#table_listado')
                        .html(resultado.data.listado);

                }

            },

            error: function (xhr) {

                console.log(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:
                        'No se pudo cargar el listado de productos.'

                });

            }

        });

    }


  

    function modalNuevoProducto() {

        limpiarErrores();
        $('#formularioProducto')[0].reset();
        $('#id').val(0);
        $('#cantidad').val(0);
        $('#stock_minimo').val(0);
        $('#estado').val('ACTIVO');
        $('#modalProducto').modal('show');

    }


    function guardarProducto() {

        limpiarErrores();

        let formData = new FormData(
            $('#formularioProducto')[0]
        );


        $.ajax({
            url: "{{ route('producto.guardar') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,


            success: function (resultado) {

                if (resultado.estado) {

                    Swal.fire({

                        title: "EL REGISTRO FUE EXITOSO.",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false

                    });

                    ajaxListado();
                    $('#modalProducto').modal('hide');

                } else {

                    Swal.fire({

                        icon: 'error',
                        title: 'Error',
                        text:
                            resultado.message ||
                            'No se pudo guardar el producto.'

                    });

                }

            },


            error: function (xhr) {
                limpiarErrores();
                if (xhr.status === 422) {

                    let errores =
                        xhr.responseJSON.errors;

                    for (let campo in errores) {

                        let mensaje =
                            errores[campo][0];
                        let input =
                            $(`[name="${campo}"]`);

                        input.addClass(
                            'is-invalid'
                        );

                        input.after(

                            `<div class="invalid-feedback">
                                ${mensaje}
                            </div>`
                        );
                    }

                } else {

                    console.log(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            'Ocurrió un error inesperado.'

                    });

                }

            }

        });

    }


    function editarProducto(producto) {
        limpiarErrores();

        $('#id').val(producto.id);
        $('#codigo').val(
            producto.codigo
        );

        $('#nombre').val(
            producto.nombre
        );

        $('#descripcion').val(
            producto.descripcion ?? ''
        );

        $('#tipo').val(
            producto.tipo
        );

        $('#categoria_id').val(
            producto.categoria_id
        );

        $('#marca_id').val(
            producto.marca_id
        );

        $('#unidad_medida').val(
            producto.unidad_medida
        );

        $('#cantidad').val(
            producto.cantidad
        );

        $('#stock_minimo').val(
            producto.stock_minimo
        );

        $('#estado').val(
            producto.estado ?? 'ACTIVO'
        );

        $('#imagen').val('');
        $('#modalProducto').modal('show');

    }

    function eliminarProducto(id, nombre) {
        Swal.fire({
            title:
                "¿Quieres eliminar " + nombre + "?",
            text:
                "¡No podrás recuperarlo!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText:
                "Sí, borrar",
            cancelButtonText:
                "No, cancelar",
            reverseButtons: true

        }).then((result) => {


            if (result.isConfirmed) {

                $.ajax({
                    url:
                        "{{ route('producto.eliminar') }}",

                    method: "POST",
                    data: {
                        id: id

                    },


                    success: function (resultado) {
                        if (resultado.estado) {
                            ajaxListado();
                            Swal.fire(
                                'Eliminado!',
                                'El producto ha sido eliminado correctamente.',
                                'success'

                            );

                        } else {

                            Swal.fire(
                                'Error',
                                resultado.message ||
                                'No se pudo eliminar el producto.',
                                'error'

                            );

                        }

                    },


                    error: function (xhr) {
                        console.log(xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text:
                                'Ocurrió un error inesperado.'

                        });

                    }

                });

            }


            else if (

                result.dismiss ===
                Swal.DismissReason.cancel

            ) {

                Swal.fire(
                    'Cancelado',
                    'La operación fue cancelada',
                    'info'

                );

            }

        });

    }


    function limpiarErrores() {

        $('.is-invalid')
            .removeClass('is-invalid');

        $('.invalid-feedback')
            .remove();

    }

</script>

@endsection