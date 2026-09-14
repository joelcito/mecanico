@extends('layouts.app')

@section('css')

<link
    href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}"
    rel="stylesheet"
    type="text/css"
/>

@endsection


@section('metadatos')

<meta
    name="csrf-token"
    content="{{ csrf_token() }}"
/>

@endsection


@section('content')

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
                        Listado de Órdenes de Servicio
                    </h3>

                    <div class="card-toolbar">

                        <a
                            href="{{ route('ordenServicio.nuevo') }}"
                            class="btn btn-primary btn-sm"
                        >

                            <i class="fa fa-plus"></i>

                            Nueva Recepción

                        </a>

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

@endsection


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

            url: "{{ route('ordenServicio.ajaxListado') }}",

            method: "POST",

            data: datos,

            success: function (resultado) {

                if (resultado.estado) {

                    $('#table_listado')
                        .html(resultado.data.listado);

                } else {

                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text:
                            resultado.mensaje ||
                            'No se pudo cargar el listado.'

                    });

                }

            },

            error: function (xhr) {

                console.log(xhr);

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text:
                        'No se pudo cargar el listado de órdenes de servicio.'

                });

            }

        });

    }


    function verOrden(id) {

        console.log('Ver orden:', id);

    }


    function editarOrden(id) {

        console.log('Editar orden:', id);

    }

</script>

@endsection