<!DOCTYPE html>
<html lang="es">
<!--begin::Head-->

<head>
    <title>SISTEMA MECANICO</title>
    <meta charset="utf-8" />
    @yield('metadatos')
    
    <meta name="description"
    content="Sistema de gestión para taller mecánico. Administración de clientes, vehículos, servicios, órdenes de trabajo, repuestos, inventario, ventas y reportes.">

    <meta name="keywords"
    content="taller mecánico, sistema mecánico, gestión de taller, vehículos, clientes, órdenes de trabajo, servicios mecánicos, repuestos, inventario, ventas, mantenimiento vehicular, Bolivia">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_BO" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Sistema de Gestión de Taller Mecánico" />
    <meta property="og:url" content="https://cmedicos.com/" />
    <meta property="og:site_name" content="Sistema de Salud en Línea" />
    <link rel="canonical" href="https://cmedicos.com/" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />


<style>
   
   /* =========================================================
   TEMA SISTEMA MECÁNICO
   LIGHT + DARK
   ========================================================= */

:root {

    /* =====================================================
       COLORES PRINCIPALES
       ===================================================== */

    --mecanico-orange: #F26B21;
    --mecanico-orange-hover: #D95713;
    --mecanico-orange-light: #FFF1E8;

    --mecanico-blue: #0288D1;
    --mecanico-blue-hover: #0277BD;
    --mecanico-blue-light: #E1F5FE;

    --mecanico-blue-dark: #1565C0;

    --mecanico-yellow: #F9A825;
    --mecanico-yellow-hover: #F57F17;
    --mecanico-yellow-light: #FFF8E1;

    --mecanico-green: #2E7D32;
    --mecanico-red: #D32F2F;

    --mecanico-white: #FFFFFF;

    --mecanico-sidebar: #102A43;
    --mecanico-sidebar-hover: #163A59;
    --mecanico-sidebar-active: #F26B21;

    --mecanico-text: #183B56;
    --mecanico-text-secondary: #486581;

    --mecanico-border: #D9E2EC;

    --mecanico-background: #F5F9FC;
}


/* =========================================================
   LIGHT MODE
   ========================================================= */

[data-bs-theme="light"] {

    --bs-body-bg: #F5F9FC !important;
    --bs-body-color: #183B56 !important;

    --bs-primary: #F26B21 !important;
    --bs-primary-rgb: 242, 107, 33 !important;

    --bs-info: #0288D1 !important;
    --bs-info-rgb: 2, 136, 209 !important;

    --bs-warning: #F9A825 !important;
    --bs-warning-rgb: 249, 168, 37 !important;

    --bs-success: #2E7D32 !important;
    --bs-danger: #D32F2F !important;

    --bs-border-color: #D9E2EC !important;

    --bs-app-bg-color: #F5F9FC !important;
    --bs-app-sidebar-bg-color: #102A43 !important;
}


/* =========================================================
   DARK MODE
   ========================================================= */

[data-bs-theme="dark"] {

    --bs-body-bg: #12191C !important;
    --bs-body-color: #ECEFF1 !important;

    --bs-primary: #FF9800 !important;
    --bs-primary-rgb: 255, 152, 0 !important;

    --bs-info: #29B6F6 !important;
    --bs-info-rgb: 41, 182, 246 !important;

    --bs-warning: #FFC107 !important;
    --bs-warning-rgb: 255, 193, 7 !important;

    --bs-success: #66BB6A !important;
    --bs-danger: #EF5350 !important;

    --bs-border-color: #37474F !important;

    --bs-app-bg-color: #12191C !important;
    --bs-app-sidebar-bg-color: #0D1316 !important;
}


/* =========================================================
   BODY
   ========================================================= */

body {
    font-family: Inter, sans-serif !important;
    transition: background-color .2s ease, color .2s ease;
}


[data-bs-theme="light"] body {
    background-color: #F5F9FC !important;
    color: #183B56 !important;
}


[data-bs-theme="dark"] body {
    background-color: #12191C !important;
    color: #ECEFF1 !important;
}


/* =========================================================
   CONTENIDO
   ========================================================= */

[data-bs-theme="light"] #kt_app_content,
[data-bs-theme="light"] .app-content {
    background-color: #F5F9FC !important;
}


[data-bs-theme="dark"] #kt_app_content,
[data-bs-theme="dark"] .app-content {
    background-color: #12191C !important;
}


/* =========================================================
   SIDEBAR
   ========================================================= */

#kt_app_sidebar {
    background-color: #102A43 !important;
}


/* DARK SIDEBAR */

[data-bs-theme="dark"] #kt_app_sidebar {
    background-color: #0D1316 !important;
}


/* =========================================================
   SIDEBAR LOGO
   ========================================================= */

#kt_app_sidebar_logo {
    background-color: transparent !important;
}


/* =========================================================
   MENU SIDEBAR
   ========================================================= */

#kt_app_sidebar .menu-link {
    color: #D9EAF7 !important;
    transition: all .2s ease;
}


#kt_app_sidebar .menu-link .menu-icon,
#kt_app_sidebar .menu-link i {
    color: #8EC5E8 !important;
}


/* HOVER */

#kt_app_sidebar .menu-link:hover {
    background-color: #163A59 !important;
    color: #FFFFFF !important;
}


#kt_app_sidebar .menu-link:hover .menu-icon,
#kt_app_sidebar .menu-link:hover i {
    color: #F9A825 !important;
}


/* =========================================================
   MENU ACTIVO
   ========================================================= */

#kt_app_sidebar .menu-item .menu-link.active {

    background-color: #F26B21 !important;

    color: #FFFFFF !important;

    box-shadow:
        inset 4px 0 0 #F9A825,
        0 4px 12px rgba(242, 107, 33, .25) !important;

}


#kt_app_sidebar .menu-item .menu-link.active .menu-icon,
#kt_app_sidebar .menu-item .menu-link.active i {
    color: #FFFFFF !important;
}


/* =========================================================
   SUBMENUS
   ========================================================= */

#kt_app_sidebar .menu-sub .menu-link {
    color: #B9D4E8 !important;
}


#kt_app_sidebar .menu-sub .menu-link:hover {
    background-color: #163A59 !important;
    color: #FFFFFF !important;
}


#kt_app_sidebar .menu-sub .menu-link.active {
    color: #FFFFFF !important;
    background-color: rgba(242, 107, 33, .25) !important;
}


/* =========================================================
   TITULOS DEL MENU
   ========================================================= */

#kt_app_sidebar .menu-title {
    color: #FFFFFF !important;
}


#kt_app_sidebar .menu-section {
    color: #7FB1D0 !important;
}


/* =========================================================
   HEADER
   ========================================================= */

[data-bs-theme="light"] .app-header {
    background-color: #FFFFFF !important;
    border-bottom: 1px solid #D9E2EC !important;
}


[data-bs-theme="dark"] .app-header {
    background-color: #182226 !important;
    border-bottom: 1px solid #37474F !important;
}


/* =========================================================
   CARDS
   ========================================================= */

[data-bs-theme="light"] .card {

    background-color: #FFFFFF !important;

    border: 1px solid #D9E2EC !important;

    box-shadow:
        0 2px 8px rgba(24, 59, 86, .05) !important;

}


[data-bs-theme="dark"] .card {

    background-color: #1C2529 !important;

    border-color: #37474F !important;

}


/* =========================================================
   TITULOS
   ========================================================= */

[data-bs-theme="light"] h1,
[data-bs-theme="light"] h2,
[data-bs-theme="light"] h3,
[data-bs-theme="light"] h4,
[data-bs-theme="light"] h5,
[data-bs-theme="light"] h6 {

    color: #183B56 !important;

}


[data-bs-theme="light"] .card-title,
[data-bs-theme="light"] .card-title h1,
[data-bs-theme="light"] .card-title h2,
[data-bs-theme="light"] .card-title h3,
[data-bs-theme="light"] .card-title h4,
[data-bs-theme="light"] .card-title h5,
[data-bs-theme="light"] .card-title h6 {

    color: #183B56 !important;

    font-weight: 700 !important;

}


[data-bs-theme="dark"] h1,
[data-bs-theme="dark"] h2,
[data-bs-theme="dark"] h3,
[data-bs-theme="dark"] h4,
[data-bs-theme="dark"] h5,
[data-bs-theme="dark"] h6 {

    color: #ECEFF1 !important;

}


/* =========================================================
   TEXTOS SECUNDARIOS
   ========================================================= */

[data-bs-theme="light"] .text-muted {
    color: #607D8B !important;
}


[data-bs-theme="dark"] .text-muted {
    color: #90A4AE !important;
}


/* =========================================================
   LABELS
   ========================================================= */

[data-bs-theme="light"] .form-label,
[data-bs-theme="light"] label {

    color: #183B56 !important;

    font-weight: 600 !important;

}


[data-bs-theme="dark"] .form-label,
[data-bs-theme="dark"] label {

    color: #CFD8DC !important;

}


/* =========================================================
   INPUTS
   ========================================================= */

[data-bs-theme="light"] .form-control,
[data-bs-theme="light"] .form-select {

    background-color: #FFFFFF !important;

    border: 1px solid #B8C7D3 !important;

    color: #183B56 !important;

}


[data-bs-theme="light"] .form-control:focus,
[data-bs-theme="light"] .form-select:focus {

    border-color: #0288D1 !important;

    box-shadow:
        0 0 0 3px rgba(2, 136, 209, .12) !important;

}


[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select {

    background-color: #263238 !important;

    border-color: #455A64 !important;

    color: #ECEFF1 !important;

}


[data-bs-theme="dark"] .form-control:focus,
[data-bs-theme="dark"] .form-select:focus {

    border-color: #FF9800 !important;

    box-shadow:
        0 0 0 3px rgba(255, 152, 0, .15) !important;

}


/* =========================================================
   BOTÓN PRIMARY
   ========================================================= */

.btn-primary {

    background-color: #F26B21 !important;

    border-color: #F26B21 !important;

    color: #FFFFFF !important;

}


.btn-primary:hover,
.btn-primary:focus {

    background-color: #D95713 !important;

    border-color: #D95713 !important;

}


/* DARK PRIMARY */

[data-bs-theme="dark"] .btn-primary {

    background-color: #FF9800 !important;

    border-color: #FF9800 !important;

    color: #121212 !important;

}


[data-bs-theme="dark"] .btn-primary:hover {

    background-color: #FFB74D !important;

    border-color: #FFB74D !important;

}


/* =========================================================
   BOTÓN INFO / CELESTE
   ========================================================= */

.btn-info {

    background-color: #0288D1 !important;

    border-color: #0288D1 !important;

    color: #FFFFFF !important;

}


.btn-info:hover {

    background-color: #0277BD !important;

    border-color: #0277BD !important;

}


/* =========================================================
   BOTÓN WARNING / AMARILLO
   ========================================================= */

.btn-warning {

    background-color: #F9A825 !important;

    border-color: #F9A825 !important;

    color: #183B56 !important;

}


.btn-warning:hover {

    background-color: #F57F17 !important;

    border-color: #F57F17 !important;

}


/* =========================================================
   BOTÓN SUCCESS
   ========================================================= */

.btn-success {

    background-color: #2E7D32 !important;

    border-color: #2E7D32 !important;

    color: #FFFFFF !important;

}


/* =========================================================
   BOTÓN DANGER
   ========================================================= */

.btn-danger {

    background-color: #D32F2F !important;

    border-color: #D32F2F !important;

    color: #FFFFFF !important;

}


/* =========================================================
   LINKS
   ========================================================= */

[data-bs-theme="light"] a {

    color: #1565C0;

}


[data-bs-theme="light"] a:hover {

    color: #F26B21;

}


[data-bs-theme="dark"] a {

    color: #29B6F6;

}


[data-bs-theme="dark"] a:hover {

    color: #FF9800;

}


/* =========================================================
   TABLAS
   ========================================================= */

[data-bs-theme="light"] table {

    color: #183B56 !important;

}


[data-bs-theme="light"] table thead th {

    background-color: #E8F1F7 !important;

    color: #183B56 !important;

    font-weight: 700 !important;

    border-bottom: 2px solid #B8C7D3 !important;

}


[data-bs-theme="light"] table tbody td {

    color: #36566D !important;

}


[data-bs-theme="light"] table tbody tr:hover {

    background-color: #F1F8FC !important;

}


/* DARK TABLE */

[data-bs-theme="dark"] table {

    color: #ECEFF1 !important;

}


[data-bs-theme="dark"] table thead th {

    background-color: #263238 !important;

    color: #ECEFF1 !important;

}


[data-bs-theme="dark"] table tbody td {

    color: #CFD8DC !important;

}


[data-bs-theme="dark"] table tbody tr:hover {

    background-color: #263238 !important;

}


/* =========================================================
   DATATABLES
   ========================================================= */

[data-bs-theme="light"] .dataTables_wrapper {

    color: #183B56 !important;

}


[data-bs-theme="light"] .dataTables_wrapper .dataTables_info {

    color: #607D8B !important;

}


[data-bs-theme="light"] .dataTables_wrapper .dataTables_length label,
[data-bs-theme="light"] .dataTables_wrapper .dataTables_filter label {

    color: #183B56 !important;

}


/* =========================================================
   BADGES
   ========================================================= */

.badge-primary {

    background-color: #F26B21 !important;

    color: #FFFFFF !important;

}


.badge-info {

    background-color: #0288D1 !important;

    color: #FFFFFF !important;

}


.badge-warning {

    background-color: #F9A825 !important;

    color: #183B56 !important;

}


.badge-success {

    background-color: #2E7D32 !important;

    color: #FFFFFF !important;

}


.badge-danger {

    background-color: #D32F2F !important;

    color: #FFFFFF !important;

}


/* =========================================================
   MODALES LIGHT
   ========================================================= */

[data-bs-theme="light"] .modal-content {

    background-color: #FFFFFF !important;

    color: #183B56 !important;

    border: 1px solid #D9E2EC !important;

    box-shadow:
        0 15px 45px rgba(16, 42, 67, .18) !important;

}


[data-bs-theme="light"] .modal-header {

    background-color: #FFFFFF !important;

    border-bottom: 1px solid #D9E2EC !important;

}


[data-bs-theme="light"] .modal-title,
[data-bs-theme="light"] .modal-header h1,
[data-bs-theme="light"] .modal-header h2,
[data-bs-theme="light"] .modal-header h3,
[data-bs-theme="light"] .modal-header h4,
[data-bs-theme="light"] .modal-header h5,
[data-bs-theme="light"] .modal-header h6 {

    color: #183B56 !important;

    font-weight: 700 !important;

}


[data-bs-theme="light"] .modal-body {

    color: #36566D !important;

}


[data-bs-theme="light"] .modal-footer {

    border-top: 1px solid #D9E2EC !important;

}


/* =========================================================
   MODALES DARK
   ========================================================= */

[data-bs-theme="dark"] .modal-content {

    background-color: #1C2529 !important;

    color: #ECEFF1 !important;

    border: 1px solid #37474F !important;

}


[data-bs-theme="dark"] .modal-header {

    background-color: #1C2529 !important;

    border-bottom: 1px solid #37474F !important;

}


[data-bs-theme="dark"] .modal-title,
[data-bs-theme="dark"] .modal-header h1,
[data-bs-theme="dark"] .modal-header h2,
[data-bs-theme="dark"] .modal-header h3,
[data-bs-theme="dark"] .modal-header h4,
[data-bs-theme="dark"] .modal-header h5,
[data-bs-theme="dark"] .modal-header h6 {

    color: #FFFFFF !important;

    font-weight: 700 !important;

}


[data-bs-theme="dark"] .modal-body {

    color: #CFD8DC !important;

}


[data-bs-theme="dark"] .modal-footer {

    border-top: 1px solid #37474F !important;

}


/* =========================================================
   BOTÓN CERRAR MODAL
   ========================================================= */

[data-bs-theme="light"] .modal-header .btn-close {

    filter: none !important;

    opacity: .7;

}


[data-bs-theme="dark"] .modal-header .btn-close {

    filter: invert(1) !important;

    opacity: .8;

}


/* =========================================================
   ALERTS
   ========================================================= */

.alert-primary {

    background-color: #FFF1E8 !important;

    border-color: #F26B21 !important;

    color: #B8420E !important;

}


.alert-info {

    background-color: #E1F5FE !important;

    border-color: #0288D1 !important;

    color: #01579B !important;

}


.alert-warning {

    background-color: #FFF8E1 !important;

    border-color: #F9A825 !important;

    color: #795548 !important;

}


.alert-success {

    background-color: #E8F5E9 !important;

    border-color: #2E7D32 !important;

    color: #1B5E20 !important;

}


.alert-danger {

    background-color: #FFEBEE !important;

    border-color: #D32F2F !important;

    color: #B71C1C !important;

}


/* =========================================================
   CHECKBOX / RADIO
   ========================================================= */

.form-check-input:checked {

    background-color: #F26B21 !important;

    border-color: #F26B21 !important;

}


.form-check-input:focus {

    border-color: #0288D1 !important;

    box-shadow:
        0 0 0 3px rgba(2, 136, 209, .15) !important;

}


/* =========================================================
   PAGINACIÓN
   ========================================================= */

[data-bs-theme="light"] .page-link {

    color: #1565C0 !important;

    background-color: #FFFFFF !important;

    border-color: #D9E2EC !important;

}


[data-bs-theme="light"] .page-item.active .page-link {

    background-color: #F26B21 !important;

    border-color: #F26B21 !important;

    color: #FFFFFF !important;

}


[data-bs-theme="light"] .page-link:hover {

    background-color: #E1F5FE !important;

    color: #0277BD !important;

}


/* DARK */

[data-bs-theme="dark"] .page-link {

    color: #29B6F6 !important;

    background-color: #1C2529 !important;

    border-color: #37474F !important;

}


[data-bs-theme="dark"] .page-item.active .page-link {

    background-color: #FF9800 !important;

    border-color: #FF9800 !important;

    color: #121212 !important;

}


/* =========================================================
   BOTONES DE ACCIONES DE TABLA
   ========================================================= */

.btn-light-primary {

    background-color: #FFF1E8 !important;

    color: #D95713 !important;

    border-color: #FFD2BC !important;

}


.btn-light-primary:hover {

    background-color: #F26B21 !important;

    color: #FFFFFF !important;

}


.btn-light-info {

    background-color: #E1F5FE !important;

    color: #0277BD !important;

    border-color: #B3E5FC !important;

}


.btn-light-warning {

    background-color: #FFF8E1 !important;

    color: #F57F17 !important;

    border-color: #FFE082 !important;

}


.btn-light-success {

    background-color: #E8F5E9 !important;

    color: #2E7D32 !important;

}


.btn-light-danger {

    background-color: #FFEBEE !important;

    color: #D32F2F !important;

}


/* =========================================================
   ICONOS CIRCULARES
   ========================================================= */

.clase-icono {

    background-color: #FFFFFF !important;

    color: #F26B21 !important;

    padding: 10px;

    border-radius: 50%;

    transition:
        background-color .2s ease,
        color .2s ease,
        transform .2s ease;

}


.menu-link:hover .clase-icono {

    background-color: #F9A825 !important;

    color: #102A43 !important;

}


.menu-item.menu-accordion.hover.show
.menu-link .clase-icono {

    background-color: #F26B21 !important;

    color: #FFFFFF !important;

}


/* =========================================================
   SECTION TITLES
   ========================================================= */

.section-title {

    color: #183B56 !important;

    font-weight: 700;

}


[data-bs-theme="dark"] .section-title {

    color: #ECEFF1 !important;

}


/* =========================================================
   SCROLLBAR LIGHT
   ========================================================= */

[data-bs-theme="light"] ::-webkit-scrollbar {

    width: 8px;

    height: 8px;

}


[data-bs-theme="light"] ::-webkit-scrollbar-track {

    background: #E8F1F7;

}


[data-bs-theme="light"] ::-webkit-scrollbar-thumb {

    background: #90AFC4;

    border-radius: 10px;

}


[data-bs-theme="light"] ::-webkit-scrollbar-thumb:hover {

    background: #F26B21;

}


/* =========================================================
   SCROLLBAR DARK
   ========================================================= */

[data-bs-theme="dark"] ::-webkit-scrollbar {

    width: 8px;

    height: 8px;

}


[data-bs-theme="dark"] ::-webkit-scrollbar-track {

    background: #12191C;

}


[data-bs-theme="dark"] ::-webkit-scrollbar-thumb {

    background: #455A64;

    border-radius: 10px;

}


[data-bs-theme="dark"] ::-webkit-scrollbar-thumb:hover {

    background: #FF9800;

}


/* =========================================================
   UTILIDADES
   ========================================================= */

.text-primary {

    color: #F26B21 !important;

}


.text-info {

    color: #0288D1 !important;

}


.text-warning {

    color: #F9A825 !important;

}


[data-bs-theme="dark"] .text-primary {

    color: #FF9800 !important;

}


[data-bs-theme="dark"] .text-info {

    color: #29B6F6 !important;

}


[data-bs-theme="dark"] .text-warning {

    color: #FFC107 !important;

}


/* =========================================================
   BACKGROUNDS
   ========================================================= */

.bg-primary {

    background-color: #F26B21 !important;

}


.bg-info {

    background-color: #0288D1 !important;

}


.bg-warning {

    background-color: #F9A825 !important;

}


.bg-light-primary {

    background-color: #FFF1E8 !important;

}


.bg-light-info {

    background-color: #E1F5FE !important;

}


.bg-light-warning {

    background-color: #FFF8E1 !important;

}


/* =========================================================
   DARK MODE OVERRIDES
   ========================================================= */

[data-bs-theme="dark"] .bg-light-primary {

    background-color: #4A2B0A !important;

}


[data-bs-theme="dark"] .bg-light-info {

    background-color: #06384F !important;

}


[data-bs-theme="dark"] .bg-light-warning {

    background-color: #4A3905 !important;

}


/* =========================================================
   SOMBRAS
   ========================================================= */

[data-bs-theme="light"] .shadow-sm {

    box-shadow:
        0 2px 8px rgba(16, 42, 67, .06) !important;

}


[data-bs-theme="light"] .shadow {

    box-shadow:
        0 5px 20px rgba(16, 42, 67, .10) !important;

}


/* =========================================================
   DARK SHADOWS
   ========================================================= */

[data-bs-theme="dark"] .shadow-sm {

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .25) !important;

}
/* =========================================================
   DASHBOARD - CARDS LIGHT
   ========================================================= */

/* Texto de los cards */
[data-bs-theme="light"] .card .text-white {
    color: #183B56 !important;
}

[data-bs-theme="light"] .card .text-gray-500,
[data-bs-theme="light"] .card .text-gray-600,
[data-bs-theme="light"] .card .text-gray-700,
[data-bs-theme="light"] .card .text-gray-800,
[data-bs-theme="light"] .card .text-gray-900 {
    color: #183B56 !important;
}

/* Títulos */
[data-bs-theme="light"] .card h1,
[data-bs-theme="light"] .card h2,
[data-bs-theme="light"] .card h3,
[data-bs-theme="light"] .card h4,
[data-bs-theme="light"] .card h5,
[data-bs-theme="light"] .card h6 {
    color: #183B56 !important;
}

/* Números grandes */
[data-bs-theme="light"] .card .fs-2,
[data-bs-theme="light"] .card .fs-3,
[data-bs-theme="light"] .card .fs-4,
[data-bs-theme="light"] .card .fs-5 {
    color: #183B56 !important;
}


/* =========================================================
   ICONOS DEL DASHBOARD
   ========================================================= */

/* Iconos SVG */
[data-bs-theme="light"] .card .svg-icon {
    color: #F26B21 !important;
}

[data-bs-theme="light"] .card .svg-icon svg {
    fill: #F26B21 !important;
}

[data-bs-theme="light"] .card .svg-icon svg path {
    fill: #F26B21 !important;
}


/* Iconos con color azul */
[data-bs-theme="light"] .card .text-info .svg-icon,
[data-bs-theme="light"] .card .svg-icon.text-info {
    color: #0288D1 !important;
}

[data-bs-theme="light"] .card .text-info .svg-icon svg path,
[data-bs-theme="light"] .card .svg-icon.text-info svg path {
    fill: #0288D1 !important;
}


/* =========================================================
   SYMBOLS / ICONOS CIRCULARES
   ========================================================= */

[data-bs-theme="light"] .card .symbol {
    background-color: #FFF1E8 !important;
}

[data-bs-theme="light"] .card .symbol .svg-icon {
    color: #F26B21 !important;
}

[data-bs-theme="light"] .card .symbol .svg-icon svg path {
    fill: #F26B21 !important;
}


/* Azul */
[data-bs-theme="light"] .card .symbol.symbol-info {
    background-color: #E1F5FE !important;
}

[data-bs-theme="light"] .card .symbol.symbol-info .svg-icon {
    color: #0288D1 !important;
}

[data-bs-theme="light"] .card .symbol.symbol-info .svg-icon svg path {
    fill: #0288D1 !important;
}


/* Amarillo */
[data-bs-theme="light"] .card .symbol.symbol-warning {
    background-color: #FFF8E1 !important;
}

[data-bs-theme="light"] .card .symbol.symbol-warning .svg-icon {
    color: #F9A825 !important;
}

[data-bs-theme="light"] .card .symbol.symbol-warning .svg-icon svg path {
    fill: #F9A825 !important;
}


/* Verde */
[data-bs-theme="light"] .card .symbol.symbol-success {
    background-color: #E8F5E9 !important;
}

[data-bs-theme="light"] .card .symbol.symbol-success .svg-icon {
    color: #2E7D32 !important;
}

[data-bs-theme="light"] .card .symbol.symbol-success .svg-icon svg path {
    fill: #2E7D32 !important;
}


/* Rojo */
[data-bs-theme="light"] .card .symbol.symbol-danger {
    background-color: #FFEBEE !important;
}

[data-bs-theme="light"] .card .symbol.symbol-danger .svg-icon {
    color: #D32F2F !important;
}

[data-bs-theme="light"] .card .symbol.symbol-danger .svg-icon svg path {
    fill: #D32F2F !important;
}


/* =========================================================
   LINKS DENTRO DE LOS CARDS
   ========================================================= */

[data-bs-theme="light"] .card a {
    color: #1565C0 !important;
}

[data-bs-theme="light"] .card a:hover {
    color: #F26B21 !important;
}


/* =========================================================
   ICONOS FONT / KI
   ========================================================= */

[data-bs-theme="light"] .card .ki,
[data-bs-theme="light"] .card .ki-duotone {
    color: #F26B21 !important;
}


/* Cuando el icono tiene clase text-primary */
[data-bs-theme="light"] .card .text-primary {
    color: #F26B21 !important;
}


/* =========================================================
   DARK - NO CAMBIAR LA APARIENCIA ACTUAL
   ========================================================= */

[data-bs-theme="dark"] .card .text-white {
    color: #FFFFFF !important;
}

[data-bs-theme="dark"] .card .text-gray-500 {
    color: #90A4AE !important;
}

[data-bs-theme="dark"] .card .text-gray-600,
[data-bs-theme="dark"] .card .text-gray-700 {
    color: #CFD8DC !important;
}

[data-bs-theme="dark"] .card .svg-icon {
    color: #FF9800 !important;
}

[data-bs-theme="dark"] .card .svg-icon svg path {
    fill: #FF9800 !important;
}

.logo-img {
    width: 45%;
    max-width: 250px;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

</style>

    
    <!--end::Global Stylesheets Bundle-->
    @section('css')
    @show
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->
@yield('scripts')

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('partials.header')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true"
                    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
                    data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
                    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                    <!--begin::Logo-->
                    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
                        <!--begin::Logo image-->
                        <a href="{{ url('home') }}">
                            <div class="row mt-4">
    <div class="col-md-12 text-center">
        <img 
            alt="Logo" 
            src="{{ asset('assets/img/logo.jpg') }}" 
            class="logo-img"
        >
    </div>
</div>
                        </a>
                        <!--end::Logo image-->
                        <!--begin::Sidebar toggle-->
                        <!--begin::Minimized sidebar setup:
                                if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") {
                                    1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
                                    2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
                                    3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
                                    4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
                                }
                            -->
                        <div id="kt_app_sidebar_toggle"
                            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
                            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                            data-kt-toggle-name="app-sidebar-minimize">
                            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Sidebar toggle-->
                    </div>
                    <!--end::Logo-->
                    <!--begin::sidebar menu-->
                    @include('partials.menu')
                    <!--end::sidebar menu-->
                    <!--begin::Footer-->
                    <!--end::Footer-->
                </div>
                <!--end::Sidebar-->
                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                @yield('content')
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                    <!--begin::Footer-->
                    @include('partials.footer')
                    <!--end::Footer-->
                </div>
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/ajaxGlobal.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
    @section('js')
    @show
    @section('formularioBusquedaJs')
    @show
    <!--end::Custom Javascript-->
    <!--end::Javascript-->

</body>
<!--end::Body-->

</html>
