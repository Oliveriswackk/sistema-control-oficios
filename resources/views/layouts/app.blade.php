<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Control de Oficios</title>

    {{-- SB ADMIN --}}
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">

    {{-- DATATABLES --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    {{-- FUENTE --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    @yield('styles')

</head>

<body id="page-top">

<div id="wrapper">

    {{-- SIDEBAR --}}
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a
            class="sidebar-brand d-flex align-items-center justify-content-center"
            href="{{ route('dashboard') }}"
        >

            <div class="sidebar-brand-icon">
                <i class="fas fa-envelope"></i>
            </div>

            <div class="sidebar-brand-text mx-2">
                Oficios
            </div>

        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">

            <a class="nav-link" href="{{ route('home') }}">

                <i class="fas fa-fw fa-home"></i>

                <span>Inicio</span>

            </a>

        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Operación
        </div>

        <li class="nav-item">

            <a class="nav-link" href="{{ route('dashboard') }}">

                <i class="fas fa-inbox"></i>

                <span>Oficios</span>

            </a>

        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">

            <button
                class="rounded-circle border-0"
                id="sidebarToggle"
            ></button>

        </div>

    </ul>

    {{-- CONTENT WRAPPER --}}
    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            {{-- TOPBAR --}}
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button
                    id="sidebarToggleTop"
                    class="btn btn-link d-md-none rounded-circle mr-3"
                >
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">

                    @auth

                        <li class="nav-item dropdown no-arrow">

                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                            href="#"
                            id="userDropdown"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">

                                <!-- Avatar / Icono -->
                                <i class="fas fa-user-circle fa-lg text-gray-600"></i>

                                <!-- Info usuario -->
                                <div class="ml-2 d-flex flex-column text-left">

                                    <span class="text-gray-800 font-weight-bold small">
                                        {{ Auth::user()->name }}
                                    </span>

                                    <span class="text-gray-500" style="font-size: 11px; line-height: 1;">
                                        {{ ucfirst(Auth::user()->role->name ?? 'usuario') }}
                                    </span>

                                </div>

                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="dropdown-item"
                                    >
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>

                                        Cerrar sesión
                                    </button>

                                </form>

                            </div>

                        </li>

                    @endauth

                </ul>

            </nav>

            {{-- MAIN CONTENT --}}
            <div class="container-fluid">

                @yield('content')

            </div>

        </div>

        {{-- FOOTER --}}
        <footer class="sticky-footer bg-white">

            <div class="container my-auto">

                <div class="copyright text-center my-auto">

                    <span>
                        Sistema de Control de Oficios &copy; {{ date('Y') }} - Desarrollado por SESEA
                    </span>

                </div>

            </div>

        </footer>

    </div>

</div>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

{{-- DATATABLES --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

@yield('scripts')

</body>
</html>