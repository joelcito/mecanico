<!DOCTYPE html>
<html lang="es">
<head>
    <base href="../../../" />
    <title>MI PORTAL DEL CLIENTE | TALLER MECÁNICO</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Portal del cliente del sistema de gestión del taller mecánico." />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-image: url('{{ asset('assets/media/auth/bg6.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            min-height: 430px;
        }

        .login-logo {
            max-width: 380px;
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        @media (max-width: 991px) {
            .login-logo {
                max-width: 240px;
            }
        }
    </style>
</head>

<body id="kt_body"
      class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">

    <script>
        var defaultThemeMode = "light";
        var themeMode = defaultThemeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            }

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches
                    ? "dark"
                    : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-column-fluid flex-lg-row">
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <div class="d-flex flex-center flex-lg-start flex-column text-center">
                    <img
                        alt="Logo del taller"
                        src="{{ asset('assets/img/logo3.png') }}"
                        class="login-logo"
                    />
                    
                </div>
            </div>

            <div class="d-flex flex-column-fluid flex-lg-row-auto
                        justify-content-center justify-content-lg-end
                        p-8 p-lg-20"
                 style="align-items: center;">

                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 login-card p-8 p-lg-10 shadow">
                    <div class="d-flex flex-center flex-column flex-column-fluid">
                        <form class="form w-100"
                              method="POST"
                              action="{{ route('portal-cliente.login.procesar') }}">
                            @csrf
                            <div class="text-center mb-10">
                                <div class="mb-5">
                                    <i class="fa fa-user-circle text-primary"
                                       style="font-size: 55px;"></i>
                                </div>

                                <h1 class="text-gray-900 fw-bolder mb-3">
                                    Bienvenido
                                </h1>

                                <div class="text-gray-500 fw-semibold fs-6">
                                    Ingresa a tu cuenta para consultar tus servicios.
                                </div>
                            </div>

                
                            <div class="separator separator-content my-8">
                                <span class="w-200px text-gray-500 fw-semibold fs-7">
                                    Ingresa tus credenciales
                                </span>
                            </div>

                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                           
                            <div class="fv-row mb-6">
                                <label class="form-label fw-semibold">
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Ingresa tu correo"
                                    autocomplete="username"
                                    class="form-control form-control-lg bg-transparent @error('email') is-invalid @enderror"
                                    required
                                    autofocus
                                />
                            </div>

                       
                            <div class="fv-row mb-8">
                                <label class="form-label fw-semibold">
                                    Contraseña
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    placeholder="Ingresa tu contraseña"
                                    autocomplete="current-password"
                                    class="form-control form-control-lg bg-transparent"
                                    required
                                />
                            </div>

                       
                            <div class="d-grid mb-8">
                                <button type="submit"
                                        class="btn btn-primary btn-lg">

                                    <span class="indicator-label">
                                        <i class="fa fa-sign-in-alt me-2"></i>
                                        Iniciar sesión
                                    </span>
                                </button>
                            </div>

                            <div class="text-center">
                                <span class="text-gray-500 fs-7">
                                    Acceso exclusivo para clientes del taller.
                                </span>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var hostUrl = "assets/";
    </script>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

</body>
</html>