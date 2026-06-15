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

                <span>Bandeja de Trabajo</span>

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


{{-- =========================================================
|  LIBRERÍAS BASE (CORE UI)
========================================================= --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


{{-- SB ADMIN --}}
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

{{-- JS DE OFICIOS (AJAX) --}}
<script src="{{ asset('js/oficios.js') }}"></script>

{{-- =========================================================
|  PLUGINS EXTERNOS
========================================================= --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.USER_CAN_EDIT = @json(
        auth()->user()->hasRole('admin') ||
        auth()->user()->hasPermission('puede_registrar')
    );
</script>

{{-- =========================================================
|  SISTEMA DE ALERTAS (GLOBAL)
========================================================= --}}
<script>
    window.Alerts = {

        success(message) {
            return Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        },

        error(message) {
            return Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        },

        warning(message) {
            return Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        },

        confirm(message) {
            return Swal.fire({
                icon: 'warning',
                title: 'Confirmación',
                text: message,
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            });
        }
    };
</script>


{{-- =========================================================
|  FLASH MESSAGES (SESSION)
========================================================= --}}
@yield('scripts')

@if(session('success'))
<script>
    Alerts.success(@json(session('success')));
</script>
@endif

@if(session('error'))
<script>
    Alerts.error(@json(session('error')));
</script>
@endif

@if(session('warning'))
<script>
    Alerts.warning(@json(session('warning')));
</script>
@endif


{{-- =========================================================
|  CONFIRMACIONES GLOBALES (FORMULARIOS)
========================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Atender turnado
        document.querySelectorAll('.form-atender-turnado')
            .forEach(form => {

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const result = await Alerts.confirm(
                        '¿Desea marcar este turnado como atendido?'
                    );

                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        // Cerrar oficio
        document.querySelectorAll('.form-cerrar-oficio')
            .forEach(form => {

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const result = await Alerts.confirm(
                        '¿Desea cerrar este oficio?'
                    );

                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

    });
</script>


{{-- =========================================================
|  MODULO OFICIOS (MODAL GLOBAL SYSTEM)
========================================================= --}}
<script>

window.Oficios = {

    /*
    |--------------------------------------------------------------------------
    | ABRIR MODAL
    |--------------------------------------------------------------------------
    */
    open(id, editable = false) {

        $('#btnGuardarOficio').show();

        $('#modalGlobalTitle').text('Detalle del Oficio');

        $('#modalGlobalBody').html('<div class="text-center">Cargando...</div>');
        
        $('#modalGlobalFooter').html(`
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-cerrar-modal">
                Cerrar
            </button>

            <button type="button" class="btn btn-success" id="btnGuardarOficio" disabled>
                Guardar cambios
            </button>
        </div>
        `);

        ModalState.reset();

        const canEdit = Oficios.canEdit();

        $('#btnGuardarOficio')
            .toggle(canEdit)
            .prop('disabled', true);

        $('#modalGlobal').modal('show');

        $.get('/oficios/' + id + '/detalle', (html) => {

            $('#modalGlobalBody').html(html);

            ModalState.original = { id };

            Oficios.enableChangeDetection();

            if (canEdit) {
                Oficios.bindSave();
            }

        });

    },


    /*
    |--------------------------------------------------------------------------
    | PERMISOS
    |--------------------------------------------------------------------------
    */
    canEdit() {
        return window.USER_CAN_EDIT === true;
    },


    /*
    |--------------------------------------------------------------------------
    | DETECTAR CAMBIOS
    |--------------------------------------------------------------------------
    */
    enableChangeDetection() {

        const $inputs = $('#modalGlobalBody').find('input, textarea, select');

        $inputs.off('input.oficios').on('input.oficios', () => {

            ModalState.changed = true;

            $('#btnGuardarOficio').prop('disabled', false);

        });

    },

    /*
    |--------------------------------------------------------------------------
    | CERRAR
    |--------------------------------------------------------------------------
    */
    bindClose() {

    $(document).off('click', '.btn-cerrar-modal');

        $(document).on('click', '.btn-cerrar-modal', function () {

            if (ModalState.changed) {

                Alerts.confirm('Tienes cambios sin guardar. ¿Cerrar?')
                    .then(result => {

                        if (result.isConfirmed) {
                            $('#modalGlobal').modal('hide');
                        }

                    });

            } else {
                $('#modalGlobal').modal('hide');
            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */
    bindSave() {

        $('#btnGuardarOficio')
            .off('click')
            .on('click', () => {

                const data = {};

                const $container = $('#modalGlobalBody');

                // inputs normales
                $container.find('input[name], textarea[name], select[name]').each(function () {

                    const name = $(this).attr('name');

                    if (!name) return;

                    if ($(this).is(':radio')) {
                        if ($(this).is(':checked')) {
                            data[name] = $(this).val();
                        }
                    } else {
                        data[name] = $(this).val();
                    }
                });

                OficiosApi.update(
                    ModalState.original.id,
                    data
                )

                .done(() => {

                    Alerts.success('Oficio actualizado');

                    ModalState.changed = false;

                    $('#btnGuardarOficio')
                        .prop('disabled', true);

                    $('#modalGlobal')
                        .modal('hide');

                })

                .fail((xhr) => {

                    console.log(xhr.responseText);

                    Alerts.error('Error al guardar');

                });
            });
    }
};

$(document).on('change', '.turnado-check', function () {

    const row = $(this).closest('tr');
    const select = row.find('.tipo-participacion');

    if (this.checked) {

        row.addClass('table-primary');
        select.prop('disabled', false);

    } else {

        row.removeClass('table-primary');
        select.prop('disabled', true).val('');

    }

});

window.ModalState = {

    original: null,
    changed: false,

    reset() {
        this.original = null;
        this.changed = false;
    }

};

window.Oficios.bindClose();
</script>


{{-- =========================================================
|  MODAL GLOBAL (REUTILIZABLE)
========================================================= --}}
<div class="modal fade" id="modalGlobal" tabindex="-1" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-xl" role="document">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title" id="modalGlobalTitle">Cargando...</h5>

                <button type="button" class="close btn-cerrar-modal">
                    <span>&times;</span>
                </button>

            </div>

            {{-- Contenido --}}
            <div class="modal-body" id="modalGlobalBody">
                Cargando...
            </div>

            {{-- Footer (vacio) --}}
            <div id="modalGlobalFooter">
                
            </div>

        </div>

    </div>

</div>

</body>
</html>