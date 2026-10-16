<style>
    /* Colores del menú */
    :root {
        --menu-rojo: #b42332;
        --menu-guindo: #701c2a;
        --menu-guindo-hover: #862638;
        --menu-texto: #e8e8ec;
        --menu-fondo-activo: rgba(180, 35, 50, 0.18);
    }

    /* Menú principal */
    #kt_app_sidebar_menu > .menu-item > .menu-link {
        margin-bottom: 5px;
        border-radius: 8px;
    }

    /* Títulos de las secciones principales */
    #kt_app_sidebar_menu .menu-accordion > .menu-link .menu-title {
        color: var(--menu-rojo) !important;
        font-weight: 800;
        letter-spacing: .35px;
        transition: color .2s ease;
    }

    /* Iconos de las secciones principales */
    #kt_app_sidebar_menu .menu-accordion > .menu-link > .menu-icon {
        color: var(--menu-rojo);
    }

    /* Enlaces de las opciones */
    #kt_app_sidebar_menu .menu-link {
        transition: background-color .2s ease, color .2s ease;
        border-radius: 7px;
    }

    #kt_app_sidebar_menu .menu-link .menu-title {
        color: var(--menu-texto) !important;
        font-weight: 500;
    }

    #kt_app_sidebar_menu .menu-link .menu-icon,
    #kt_app_sidebar_menu .menu-link .menu-bullet {
        color: #c8a1a7;
    }

    /* Hover guindo */
    #kt_app_sidebar_menu .menu-link:hover {
        background-color: var(--menu-guindo) !important;
    }

    #kt_app_sidebar_menu .menu-link:hover .menu-title,
    #kt_app_sidebar_menu .menu-link:hover .menu-icon,
    #kt_app_sidebar_menu .menu-link:hover .menu-bullet,
    #kt_app_sidebar_menu .menu-link:hover .menu-arrow {
        color: #ffffff !important;
    }

    /* Elemento seleccionado */
    #kt_app_sidebar_menu .menu-link.active {
        background-color: var(--menu-fondo-activo) !important;
        border-left: 3px solid var(--menu-rojo);
    }

    #kt_app_sidebar_menu .menu-link.active .menu-title,
    #kt_app_sidebar_menu .menu-link.active .menu-icon {
        color: #ffffff !important;
        font-weight: 700;
    }

    /* Iconos de los submenús */
    #kt_app_sidebar_menu .menu-sub .menu-icon {
        width: 22px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 4px;
        font-size: 14px;
    }

    /* Sangría y separación de los submenús */
    #kt_app_sidebar_menu .menu-sub-accordion {
        padding-left: 8px;
    }

    #kt_app_sidebar_menu .menu-sub .menu-link {
        padding-top: 9px;
        padding-bottom: 9px;
        min-height: 38px;
    }

    /* Cabeceras de los submenús desplegables */
    #kt_app_sidebar_menu .menu-accordion > .menu-link .menu-arrow {
        color: #a7a7b3;
    }

    #kt_app_sidebar_menu .menu-accordion > .menu-link:hover .menu-arrow {
        color: #ffffff;
    }
</style>

<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_menu_scroll"
             class="scroll-y my-5 mx-3"
             data-kt-scroll="true"
             data-kt-scroll-activate="true"
             data-kt-scroll-height="auto"
             data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
             data-kt-scroll-wrappers="#kt_app_sidebar_menu"
             data-kt-scroll-offset="5px"
             data-kt-scroll-save-state="true">

            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
                 id="kt_app_sidebar_menu"
                 data-kt-menu="true"
                 data-kt-menu-expand="true">

                {{-- MENU --}}
                @if(Auth::check() && in_array((int) Auth::user()->rol_id, [1, 2, 3], true))
                <div data-kt-menu-trigger="click"
                     class="menu-item menu-accordion here show">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fa fa-university"></i>
                        </span>
                        <span class="menu-title">MENU</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">

                        {{-- OPERACIONES --}}
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion here show">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-cogs"></i>
                                </span>
                                <span class="menu-title">OPERACIONES</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('ordenServicio.*', 'ordenesServicio.*') ? 'active' : '' }}"
                                       href="{{ route('ordenServicio.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-clipboard-list"></i>
                                        </span>
                                        <span class="menu-title">Recepción</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('vehiculo.*') ? 'active' : '' }}"
                                       href="{{ route('vehiculo.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-car"></i>
                                        </span>
                                        <span class="menu-title">Vehículos</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('cliente.*') ? 'active' : '' }}"
                                       href="{{ route('cliente.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-users"></i>
                                        </span>
                                        <span class="menu-title">Clientes</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                        {{-- INVENTARIO Y TALLER --}}
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion here show">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-wrench"></i>
                                </span>
                                <span class="menu-title">INVENTARIO &amp; TALLER</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">

                                @if(in_array((int) Auth::user()->rol_id, [1, 2, 3], true))
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('asignacionesHerramientas.*') ? 'active' : '' }}"
                                           href="{{ route('asignacionesHerramientas.listado') }}">
                                            <span class="menu-icon">
                                                <i class="fa fa-toolbox"></i>
                                            </span>
                                            <span class="menu-title">Asignaciones de herramientas</span>
                                        </a>
                                    </div>
                                @endif

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('herramienta.*') ? 'active' : '' }}"
                                       href="{{ route('herramienta.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-tools"></i>
                                        </span>
                                        <span class="menu-title">Herramientas</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('producto.*') ? 'active' : '' }}"
                                       href="{{ route('producto.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-boxes"></i>
                                        </span>
                                        <span class="menu-title">Productos</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                        {{-- CAJA Y FINANZAS --}}
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion here show">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-wallet"></i>
                                </span>
                                <span class="menu-title">CAJA &amp; FINANZAS</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}"
                                       href="{{ route('cajas.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-cash-register"></i>
                                        </span>
                                        <span class="menu-title">Caja</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('movimientosCaja.*') ? 'active' : '' }}"
                                       href="{{ route('movimientosCaja.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-exchange-alt"></i>
                                        </span>
                                        <span class="menu-title">Movimientos</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}"
                                       href="{{ route('pagos.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-money-bill-wave"></i>
                                        </span>
                                        <span class="menu-title">Pagos</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('cuentasPorCobrar.*') ? 'active' : '' }}"
                                       href="{{ route('cuentasPorCobrar.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-file-invoice-dollar"></i>
                                        </span>
                                        <span class="menu-title">Cuentas por cobrar</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                        {{-- CONFIGURACIÓN --}}
                        <div data-kt-menu-trigger="click"
                             class="menu-item menu-accordion here show">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-sliders-h"></i>
                                </span>
                                <span class="menu-title">CONFIGURACIÓN</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">

                                {{-- REPORTES: ACORDEÓN CORRECTO --}}
                                <div data-kt-menu-trigger="click"
                                     class="menu-item menu-accordion {{ request()->routeIs('reportes.*') ? 'here show' : '' }}">

                                    <span class="menu-link">
                                        <span class="menu-icon">
                                            <i class="fa fa-chart-bar"></i>
                                        </span>
                                        <span class="menu-title">Reportes</span>
                                        <span class="menu-arrow"></span>
                                    </span>

                                    <div class="menu-sub menu-sub-accordion">

                                        @foreach([
                                            'ventas' => ['Ventas', 'fa-chart-line'],
                                            'movimientos' => ['Movimientos', 'fa-exchange-alt'],
                                            'productos' => ['Productos / Stock', 'fa-boxes'],
                                            'pagos' => ['Pagos', 'fa-money-bill-wave'],
                                        ] as $tipoReporte => $reporte)

                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->route('tipo') === $tipoReporte ? 'active' : '' }}"
                                                   href="{{ route('reportes.index', ['tipo' => $tipoReporte]) }}">

                                                    <span class="menu-icon">
                                                        <i class="fa {{ $reporte[1] }}"></i>
                                                    </span>

                                                    <span class="menu-title">
                                                        {{ $reporte[0] }}
                                                    </span>
                                                </a>
                                            </div>

                                        @endforeach

                                    </div>
                                </div>

                                {{-- USUARIOS --}}
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('user.*') ? 'active' : '' }}"
                                       href="{{ route('user.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-user-cog"></i>
                                        </span>
                                        <span class="menu-title">Usuarios</span>
                                    </a>
                                </div>

                                {{-- ROLES --}}
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('rol.*') ? 'active' : '' }}"
                                       href="{{ route('rol.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-user-shield"></i>
                                        </span>
                                        <span class="menu-title">Roles</span>
                                    </a>
                                </div>

                                {{-- SUCURSALES --}}
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('sucursal.*') ? 'active' : '' }}"
                                       href="{{ route('sucursal.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-building"></i>
                                        </span>
                                        <span class="menu-title">Sucursales</span>
                                    </a>
                                </div>

                                {{-- CATEGORÍAS --}}
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('categoria.*') ? 'active' : '' }}"
                                       href="{{ route('categoria.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-tags"></i>
                                        </span>
                                        <span class="menu-title">Categorías</span>
                                    </a>
                                </div>

                                {{-- MARCAS --}}
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('marca.*') ? 'active' : '' }}"
                                       href="{{ route('marca.listado') }}">
                                        <span class="menu-icon">
                                            <i class="fa fa-copyright"></i>
                                        </span>
                                        <span class="menu-title">Marcas</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
@endif
            </div>
        </div>
    </div>
</div>