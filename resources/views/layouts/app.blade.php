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

        $('#modalGlobalTitle').text('Detalle del Oficio');
        $('#modalGlobalBody').html('<div class="text-center">Cargando...</div>');

        ModalState.reset();

        const canEdit = Oficios.canEdit();

        // Mostrar/ocultar footer según permisos
        $('#modalGlobalFooter').toggle(canEdit);

        // Reset botón guardar
        $('#btnGuardarOficio')
            .prop('disabled', true);

        $('#modalGlobal').modal('show');

        Oficios.bindClose();

        $.get('/oficios/' + id + '/detalle', (oficio) => {

            ModalState.original = oficio;

            const html = this.renderDetalle(oficio, canEdit);

            $('#modalGlobalBody').html(html);

            if (canEdit) {
                this.enableChangeDetection();
                this.bindSave();
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
    | RENDER (mismios campos al crear)
    |--------------------------------------------------------------------------
    */
    renderDetalle(oficio, editable = false) {

        const ro = editable ? '' : 'readonly';

        const disabled = editable ? '' : 'disabled';

        const tipo = String(oficio.tipo_oficio_id ?? '');
        const req = Number(oficio.requiere_respuesta || 0);
        const sens = Number(oficio.es_sensible || 0);
        
        return `
            <div>

                {{-- =========================
                    IDENTIFICACIÓN
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Identificación</h6>

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Número Oficio</label>
                        <input type="text" name="numero_oficio"
                            class="form-control form-control-sm"
                            value="${oficio.numero_oficio ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Consecutivo</label>
                        <input type="text" name="consecutivo"
                            class="form-control form-control-sm"
                            value="${oficio.consecutivo ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Asunto</label>
                        <input type="text" name="asunto"
                            class="form-control form-control-sm"
                            value="${oficio.asunto ?? ''}" ${ro}>
                    </div>

                </div>

                {{-- =========================
                    FECHAS
                ========================= --}}
                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Fecha oficio</label>
                        <input type="date" name="fecha_oficio"
                            class="form-control form-control-sm"
                            value="${oficio.fecha_oficio ? oficio.fecha_oficio.substring(0, 10) : ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Fecha recepción</label>
                        <input type="date" name="fecha_recepcion"
                            class="form-control form-control-sm"
                            value="${oficio.fecha_recepcion ? oficio.fecha_recepcion.substring(0, 10) : ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Fecha límite</label>
                        <input type="date" name="fecha_limite"
                            class="form-control form-control-sm"
                            value="${oficio.fecha_limite ? oficio.fecha_limite.substring(0, 10) : ''}" ${ro}>
                    </div>

                </div>

                {{-- =========================
                    CONTENIDO
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Contenido del Oficio</h6>

                <div class="col-md-12 mb-3">

                    <label>Descripción</label>

                    <textarea name="descripcion"
                        class="form-control form-control-sm"
                        rows="3" ${ro}>${oficio.descripcion ?? ''}</textarea>

                </div>

                <div class="col-md-12 mb-3">
                    <label>Link documento</label>
                    <input type="text" name="link_documento"
                        class="form-control form-control-sm"
                        value="${oficio.link_documento ?? ''}" ${ro}>
                </div>

                {{-- =========================
                    REMITENTE
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Remitente</h6>

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Nombre</label>
                        <input type="text" name="remitente_nombre"
                            class="form-control form-control-sm"
                            value="${oficio.remitente_nombre ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Cargo</label>
                        <input type="text" name="remitente_cargo"
                            class="form-control form-control-sm"
                            value="${oficio.remitente_cargo ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Dependencia</label>
                        <input type="text" name="remitente_dependencia"
                            class="form-control form-control-sm"
                            value="${oficio.remitente_dependencia ?? ''}" ${ro}>
                    </div>

                </div>

                {{-- =========================
                    DESTINATARIO
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Destinatario</h6>

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Nombre</label>
                        <input type="text" name="destinatario_nombre"
                            class="form-control form-control-sm"
                            value="${oficio.destinatario_nombre ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Cargo</label>
                        <input type="text" name="destinatario_cargo"
                            class="form-control form-control-sm"
                            value="${oficio.destinatario_cargo ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Dependencia</label>
                        <input type="text" name="destinatario_dependencia"
                            class="form-control form-control-sm"
                            value="${oficio.destinatario_dependencia ?? ''}" ${ro}>
                    </div>

                </div>

                {{-- =========================
                    ELABORADOR
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Elaborador</h6>

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Nombre</label>
                        <input type="text" name="quien_elabora_nombre"
                            class="form-control form-control-sm"
                            value="${oficio.quien_elabora_nombre ?? ''}" ${ro}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Cargo</label>
                        <input type="text" name="quien_elabora_cargo"
                            class="form-control form-control-sm"
                            value="${oficio.quien_elabora_cargo ?? ''}" ${ro}>
                    </div>

                </div>


                {{-- =========================
                    FLAGS OPERATIVOS
                ========================= --}}
                <h6 class="text-primary font-weight-bold mb-2">Configuración</h6>

                <div class="row">

                    {{-- REQUIERE RESPUESTA --}}
                    <div class="col-md-4 mb-2">

                        <label>Requiere respuesta</label>

                        <div class="d-flex">

                            <div class="custom-control custom-radio mr-3">
                                <input
                                    type="radio"
                                    id="req_no"
                                    name="requiere_respuesta"
                                    value="0"
                                    class="custom-control-input"
                                    ${oficio.requiere_respuesta == 0 ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="req_no">No</label>
                            </div>

                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="req_si"
                                    name="requiere_respuesta"
                                    value="1"
                                    class="custom-control-input"
                                    ${oficio.requiere_respuesta == 1 ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="req_si">Sí</label>
                            </div>

                        </div>
                    </div>

                    {{-- SENSIBLE --}}
                    <div class="col-md-4 mb-2">

                        <label>Documento sensible</label>

                        <div class="d-flex">

                            <div class="custom-control custom-radio mr-3">
                                <input
                                    type="radio"
                                    id="sens_no"
                                    name="es_sensible"
                                    value="0"
                                    class="custom-control-input"
                                    ${oficio.es_sensible == 0 ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="sens_no">No</label>
                            </div>

                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="sens_si"
                                    name="es_sensible"
                                    value="1"
                                    class="custom-control-input"
                                    ${oficio.es_sensible == 1 ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="sens_si">Sí</label>
                            </div>

                        </div>
                    </div>

                    {{-- TIPO OFICIO --}}
                    <div class="col-md-4 mb-2">

                        <label>Tipo de oficio</label>

                        <div>

                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="tipo_enviado"
                                    name="tipo_oficio_id"
                                    value="1"
                                    class="custom-control-input"
                                    ${tipo === '1' ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="tipo_enviado">
                                    Enviado
                                </label>
                            </div>

                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="tipo_recibido"
                                    name="tipo_oficio_id"
                                    value="2"
                                    class="custom-control-input"
                                    ${tipo === '2' ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="tipo_recibido">
                                    Recibido
                                </label>
                            </div>

                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="tipo_cpc"
                                    name="tipo_oficio_id"
                                    value="3"
                                    class="custom-control-input"
                                    ${tipo === '3' ? 'checked' : ''}
                                    ${ro}
                                >
                                <label class="custom-control-label" for="tipo_cpc">
                                    Recibido CPC
                                </label>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        `;
    },

    /*
    |--------------------------------------------------------------------------
    | DETECTAR CAMBIOS
    |--------------------------------------------------------------------------
    */
    enableChangeDetection() {

        const $inputs = $('#modalGlobalBody').find('input, textarea');

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

                $('#modalGlobalBody')
                    .find('input, textarea')
                    .each(function () {
                        const name = $(this).attr('name');
                        if (name) {
                            data[name] = $(this).val();
                        }
                    });

                $.ajax({
                    url: `/oficios/${ModalState.original.id}`,
                    method: 'POST',
                    data: {
                        ...data,
                        _method: 'PUT',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: () => {

                        Alerts.success('Oficio actualizado');

                        ModalState.changed = false;

                        $('#btnGuardarOficio').prop('disabled', true);

                        $('#modalGlobal').modal('hide');
                    },

                    error: (xhr) => {
                        console.log(xhr.responseText);
                        Alerts.error('Error al guardar');
                    }
                });

            });

    }

};

window.ModalState = {

    original: null,
    changed: false,

    reset() {
        this.original = null;
        this.changed = false;
    }

};

</script>


{{-- =========================================================
|  MODAL GLOBAL (REUTILIZABLE)
========================================================= --}}
<div class="modal fade" id="modalGlobal" tabindex="-1" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-xl" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalGlobalTitle">Cargando...</h5>

                <button type="button" class="close btn-cerrar-modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body" id="modalGlobalBody">
                Cargando...
            </div>

            <div class="modal-footer" id="modalGlobalFooter">

                <button type="button" class="btn btn-secondary btn-cerrar-modal">
                    Cerrar
                </button>

                <button type="button" class="btn btn-success" id="btnGuardarOficio" disabled>
                    Guardar cambios
                </button>

            </div>

        </div>

    </div>

</div>

</body>
</html>