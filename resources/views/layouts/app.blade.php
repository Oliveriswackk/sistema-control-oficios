<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <title>Sistema de Control de Oficios</title>

    {{-- SB ADMIN --}}
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">

    {{-- DATATABLES --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    {{-- DATEPICKER --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    >

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
                                        {{ Auth::user()->rolPrincipal?->nombre ?? 'Usuario' }}
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
                        Sistema de Control de Oficios &copy; {{ date('Y') }} - Desarrollado por SESEA <span class="badge badge-secondary ml-1">v1.2.0</span>
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

{{-- DEFINICIÓN DE URL BASE DE LARAVEL PARA JS --}}
<script>
    window.LaravelBaseUrl = "{{ url('/') }}";
</script>

{{-- JS DE OFICIOS --}}
<script src="{{ asset('js/oficios.js') }}?v=1.0.2"></script>
<script src="{{ asset('js/sugerencias.js') }}?v=1.0.0"></script>

{{-- =========================================================
|  PLUGINS EXTERNOS
========================================================= --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.USER_CAN_EDIT = @json(
        auth()->user()->hasRole('admin') ||
        auth()->user()->hasPermission('puede_registrar_oficios')
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
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

    
// =========================================================
// TAGS
// =========================================================
// Mostrar input
$(document).on('click', '#btn-mostrar-tag', function () {

    $('#contenedor-nuevo-tag').show();

    $('#input-tag').focus();

});


// Crear tag al presionar ENTER
$(document).on('keypress', '#input-tag', function (e) {

    if (e.which !== 13) {
        return;
    }

    e.preventDefault();

    const input = $(this);
    const oficioId = input.data('oficio-id');

    const nombres = input.val()
        .split(',')
        .map(function (nombre) {
            return nombre.trim();
        })
        .filter(function (nombre) {
            return nombre !== '';
        });

    if (!nombres.length) {
        return;
    }

    nombres.forEach(function (nombre) {

        $.post(
            `{{ url('oficios') }}/${oficioId}/tags`,
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                nombre: nombre
            }
        )
        .done(function (response) {

            $('#btn-mostrar-tag').before(`
                <span
                    class="badge badge-info mr-1 tag-item"
                    data-tag-id="${response.tag.id}">

                    ${response.tag.nombre}

                    <span
                        class="ml-1 text-white btn-eliminar-tag"
                        data-tag-id="${response.tag.id}"
                        data-oficio-id="${oficioId}"
                        style="cursor:pointer;">

                        ×

                    </span>

                </span>
            `);

        });

    });

    input.val('');

});


// Eliminar tag
$(document).on('click', '.btn-eliminar-tag', function () {

    const tagId = $(this).data('tag-id');
    const oficioId = $(this).data('oficio-id');

    $.ajax({

        url: `{{ url('oficios') }}/${oficioId}/tags/${tagId}`,
        method: 'DELETE',

        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        }

    }).done(function () {

        $(`.tag-item[data-tag-id="${tagId}"]`).remove();

    });

});

window.Oficios = {

    /*
    |--------------------------------------------------------------------------
    | ABRIR MODAL
    |--------------------------------------------------------------------------
    */
    open(id, editable = false) {

        ModalState.reset();

        // Guardamos desde el inicio el ID del oficio.
            ModalState.original = {
            id: id
        };

        $('#btnCancelarOficio').hide();

        $('#detalleNumeroOficio').text('Cargando...');
        $('#detalleConsecutivo').text('');
        $('#detalleTipoOficio').html('');
        $('#detalleEstado').html('');
        $('#detalleTags').html('');
        $('#detalleDocumentos').html('');

        $('#detalleResponsable').html(`
            <div class="text-center text-muted py-3">
                <i class="fas fa-spinner fa-spin mr-1"></i>
                Cargando información...
            </div>
        `);

        $('#modalDetalleOficio').modal('show');

        $.get(
            `${window.LaravelBaseUrl}/oficios/${id}/detalle-json`,
            function (oficio) {

                // Guardamos el oficio completo para las acciones posteriores.
                ModalState.original = oficio;

                /*
                |--------------------------------------------------------------------------
                | CANCELAR
                |--------------------------------------------------------------------------
                */

                if (oficio.puede_cancelar === true) {
                    $('#btnCancelarOficio').show();
                } else {
                    $('#btnCancelarOficio').hide();
                }

                const bloqueado =
                    oficio.estado?.clave === 'cerrado' ||
                    oficio.estado?.clave === 'cancelado';

                $('#modalDetalleOficio')
                    .find('input, textarea, select')
                    .prop('readonly', bloqueado)
                    .prop('disabled', bloqueado);

                $('#btnGuardarOficio')
                    .prop('disabled', bloqueado || !ModalState.changed)
                    .toggle(!bloqueado);

                $('#btnCorregirIdentidad')
                    .toggle(!bloqueado);


                /*
                |--------------------------------------------------------------------------
                | IDENTIDAD
                |--------------------------------------------------------------------------
                */

                $('#detalleNumeroOficio').text(
                    oficio.numero_oficio || 'Sin número'
                );

                $('#detalleConsecutivo').text(
                    oficio.consecutivo
                        ? `Consecutivo ${oficio.consecutivo}`
                        : ''
                );

                $('#detalleTipoOficio').html(
                    oficio.tipo_oficio_nombre
                        ? `
                            <span class="badge badge-secondary px-2 py-1">
                                ${oficio.tipo_oficio_nombre}
                            </span>
                        `
                        : ''
                );

                if (oficio.estado) {

                    $('#detalleEstado').html(`
                        <span
                            class="badge px-2 py-1"
                            style="
                                background-color: ${oficio.estado.color || '#6c757d'};
                                color: #fff;
                            ">
                            ${oficio.estado.nombre}
                        </span>
                    `);

                } else {

                    $('#detalleEstado').html('');

                }


                /*
                |--------------------------------------------------------------------------
                | CONTENIDO
                |--------------------------------------------------------------------------
                */

                $('#detalleAsunto').val(
                    oficio.asunto || ''
                );

                $('#detalleDescripcion').val(
                    oficio.descripcion || ''
                );


                /*
                |--------------------------------------------------------------------------
                | REMITENTE
                |--------------------------------------------------------------------------
                */

                $('#detalleRemitenteNombre').val(
                    oficio.remitente_nombre || ''
                );

                $('#detalleRemitenteCargo').val(
                    oficio.remitente_cargo || ''
                );

                $('#detalleRemitenteDependencia').val(
                    oficio.remitente_dependencia || ''
                );


                /*
                |--------------------------------------------------------------------------
                | DESTINATARIO
                |--------------------------------------------------------------------------
                */

                $('#detalleDestinatarioNombre').val(
                    oficio.destinatario_nombre || ''
                );

                $('#detalleDestinatarioCargo').val(
                    oficio.destinatario_cargo || ''
                );

                $('#detalleDestinatarioDependencia').val(
                    oficio.destinatario_dependencia || ''
                );

                /*
                |--------------------------------------------------------------------------
                | ELABORADOR INTERNO
                |--------------------------------------------------------------------------
                */

                if (parseInt(oficio.tipo_oficio_id, 10) === 1) {

                    $('#detalleElaborador').show();

                    $('#detalleElaboradorNombre').val(
                        oficio.quien_elabora_nombre || ''
                    );

                    $('#detalleElaboradorCargo').val(
                        oficio.quien_elabora_cargo || ''
                    );

                } else {

                    $('#detalleElaborador').hide();

                    $('#detalleElaboradorNombre').val('');
                    $('#detalleElaboradorCargo').val('');

                }

                /*
                |--------------------------------------------------------------------------
                | ATENCIÓN
                |--------------------------------------------------------------------------
                */

                $('#detalleFechaOficio').val(
                    oficio.fecha_oficio || ''
                );

                $('#detalleFechaRecepcion').val(
                    oficio.fecha_recepcion || ''
                );

                $('#detalleFechaLimite').val(
                    oficio.fecha_limite || ''
                );


                $(
                    `input[name="requiere_respuesta"][value="${oficio.requiere_respuesta ? 1 : 0}"]`
                ).prop('checked', true);


                $(
                    `input[name="es_sensible"][value="${oficio.es_sensible ? 1 : 0}"]`
                ).prop('checked', true);


                /*
                |--------------------------------------------------------------------------
                | ENLACE DE TRANSPARENCIA
                |--------------------------------------------------------------------------
                */

                $('#detalleLinkDocumento').val(
                    oficio.link_documento || ''
                );


                /*
                |--------------------------------------------------------------------------
                | RESPONSABLE
                |--------------------------------------------------------------------------
                |
                | Este bloque se conserva tal como está planteado.
                |
                */

                if (oficio.responsable) {

                    $('#detalleResponsable').html(`
                        <div class="card border-left-primary shadow-sm mb-3">
                            <div class="card-body py-3">

                                <div class="d-flex align-items-center">

                                    <div class="mr-3">

                                        <div
                                            class="rounded-circle border bg-light d-flex align-items-center justify-content-center"
                                            style="width:60px;height:60px;">

                                            <i class="fas fa-user text-secondary"></i>

                                        </div>

                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="font-weight-bold text-dark">
                                            ${oficio.responsable.coordinacion}
                                        </div>

                                        <div class="text-muted">
                                            ${oficio.responsable.usuario}
                                        </div>

                                    </div>

                                    <div>

                                        <span class="badge badge-primary px-3 py-2">
                                            ${oficio.estado.nombre}
                                        </span>

                                    </div>

                                </div>

                            </div>
                        </div>
                    `);

                } else {

                    $('#detalleResponsable').html('');

                }


                /*
                |--------------------------------------------------------------------------
                | TAGS
                |--------------------------------------------------------------------------
                */

                let tagsHtml = '';

                if (Array.isArray(oficio.tags)) {

                    oficio.tags.forEach(function (tag) {

                        tagsHtml += `
                            <span
                                class="badge badge-info mr-1 tag-item"
                                data-tag-id="${tag.id}">

                                ${tag.nombre}

                                <span
                                    class="ml-1 text-white btn-eliminar-tag"
                                    data-tag-id="${tag.id}"
                                    data-oficio-id="${oficio.id}"
                                    style="cursor:pointer;">

                                    ×

                                </span>

                            </span>
                        `;

                    });

                }

                tagsHtml += `
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        id="btn-mostrar-tag">

                        +

                    </button>

                    <div
                        id="contenedor-nuevo-tag"
                        class="mt-2"
                        style="display:none;">

                        <input
                            type="text"
                            id="input-tag"
                            data-oficio-id="${oficio.id}"
                            class="form-control form-control-sm"
                            placeholder="Agregar tags separados por coma">

                    </div>
                `;

                $('#detalleTags').html(tagsHtml);


                /*
                |--------------------------------------------------------------------------
                | DOCUMENTOS
                |--------------------------------------------------------------------------
                */

                let documentosHtml = '';

                if (Array.isArray(oficio.archivos)) {

                    oficio.archivos.forEach(function (archivo) {

                        const versionActual = archivo.versiones.find(
                            function (version) {
                                return version.es_actual;
                            }
                        );

                        if (!versionActual) {
                            return;
                        }

                        documentosHtml += `
                            <div class="border rounded p-2 mb-2">

                                <strong>
                                    ${archivo.nombre_original}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    V${versionActual.version} · versión actual
                                </small>

                                <br>

                                <div class="mt-2">

                                    <a
                                        href="${versionActual.url}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="fas fa-file-pdf mr-1"></i>
                                        Ver PDF

                                    </a>

                                    ${Oficios.canEdit() ? `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary ml-1"
                                            onclick="
                                                document
                                                    .getElementById('reemplazo-pdf-${archivo.id}')
                                                    .classList
                                                    .toggle('d-none')
                                            ">

                                            <i class="fas fa-sync-alt mr-1"></i>
                                            Reemplazar PDF

                                        </button>
                                    ` : ''}

                                </div>

                                ${Oficios.canEdit() ? `
                                    <div
                                        id="reemplazo-pdf-${archivo.id}"
                                        class="d-none mt-3">

                                        <form
                                            method="POST"
                                            action="${window.LaravelBaseUrl}/oficios/${oficio.id}/archivos"
                                            enctype="multipart/form-data">

                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="${$('meta[name="csrf-token"]').attr('content')}">

                                            <div class="form-group mb-2">

                                                <label class="mb-1">
                                                    Nuevo PDF
                                                </label>

                                                <input
                                                    type="file"
                                                    name="archivo"
                                                    accept="application/pdf"
                                                    class="form-control form-control-sm"
                                                    required>

                                            </div>

                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-sm">

                                                <i class="fas fa-upload mr-1"></i>
                                                Guardar nueva versión

                                            </button>

                                        </form>

                                    </div>
                                ` : ''}

                            </div>
                        `;

                    });

                }

                if (!documentosHtml) {

                    documentosHtml = `
                        <div class="border rounded p-3">

                            <div class="text-muted small mb-3">
                                Este oficio no tiene documentos cargados.
                            </div>

                            ${Oficios.canEdit() ? `
                                <form
                                    method="POST"
                                    action="${window.LaravelBaseUrl}/oficios/${oficio.id}/archivos"
                                    enctype="multipart/form-data">

                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="${$('meta[name="csrf-token"]').attr('content')}">

                                    <div class="form-group mb-2">

                                        <label class="mb-1">
                                            Subir PDF principal
                                        </label>

                                        <input
                                            type="file"
                                            name="archivo"
                                            accept="application/pdf"
                                            class="form-control form-control-sm"
                                            required>

                                    </div>

                                    <button
                                        type="submit"
                                        class="btn btn-primary btn-sm">

                                        <i class="fas fa-upload mr-1"></i>
                                        Subir PDF

                                    </button>

                                </form>
                            ` : ''}

                        </div>
                    `;

                }

                $('#detalleDocumentos').html(
                    documentosHtml
                );


                /*
                |--------------------------------------------------------------------------
                | ESTADO INICIAL DEL MODAL
                |--------------------------------------------------------------------------
                */

                ModalState.changed = false;

            }
        )
        .fail(function (xhr) {

            console.error(xhr.responseText);

            $('#detalleNumeroOficio').text(
                'No se pudo cargar el oficio'
            );

            $('#detalleResponsable').html(`
                <div class="text-center text-danger py-3">
                    No se pudo cargar la información del oficio.
                </div>
            `);

            Alerts.error(
                'No se pudo cargar la información del oficio.'
            );

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

    canCancel(oficio) {

        return oficio.puede_cancelar === true;

    },


    /*
    |--------------------------------------------------------------------------
    | DETECTAR CAMBIOS
    |--------------------------------------------------------------------------
    */
    enableChangeDetection() {

        $(document)
            .off(
                'input.oficios change.oficios',
                '#modalDetalleOficio input, #modalDetalleOficio textarea, #modalDetalleOficio select'
            )
            .on(
                'input.oficios change.oficios',
                '#modalDetalleOficio input, #modalDetalleOficio textarea, #modalDetalleOficio select',
                function () {

                    if (!ModalState.original) {
                        return;
                    }

                    const changed = Oficios.hasEditableChanges();

                    ModalState.changed = changed;

                    $('#btnGuardarOficio').prop('disabled', !changed);
                }
            );
    },

    getEditableData() {

        const requiereRespuesta = $(
            '#modalDetalleOficio input[name="requiere_respuesta"]:checked'
        ).val();

        const esSensible = $(
            '#modalDetalleOficio input[name="es_sensible"]:checked'
        ).val();

        return {
            asunto: $('#detalleAsunto').val() || '',
            descripcion: $('#detalleDescripcion').val() || '',
            fecha_oficio: $('#detalleFechaOficio').val() || '',
            fecha_recepcion: $('#detalleFechaRecepcion').val() || '',
            fecha_limite: $('#detalleFechaLimite').val() || '',

            requiere_respuesta:
                requiereRespuesta !== undefined
                    ? parseInt(requiereRespuesta, 10)
                    : 0,

            es_sensible:
                esSensible !== undefined
                    ? parseInt(esSensible, 10)
                    : 0,

            remitente_nombre:
                $('#detalleRemitenteNombre').val() || '',

            remitente_cargo:
                $('#detalleRemitenteCargo').val() || '',

            remitente_dependencia:
                $('#detalleRemitenteDependencia').val() || '',

            destinatario_nombre:
                $('#detalleDestinatarioNombre').val() || '',

            destinatario_cargo:
                $('#detalleDestinatarioCargo').val() || '',

            destinatario_dependencia:
                $('#detalleDestinatarioDependencia').val() || '',

            quien_elabora_nombre:
                $('#detalleElaboradorNombre').val() || '',

            quien_elabora_cargo:
                $('#detalleElaboradorCargo').val() || '',

            link_documento:
                $('#detalleLinkDocumento').val() || ''
        };
    },

    hasEditableChanges() {

        if (!ModalState.original) {
            return false;
        }

        const current = Oficios.getEditableData();

        const original = {
            asunto: ModalState.original.asunto || '',
            descripcion: ModalState.original.descripcion || '',
            fecha_oficio: ModalState.original.fecha_oficio || '',
            fecha_recepcion: ModalState.original.fecha_recepcion || '',
            fecha_limite: ModalState.original.fecha_limite || '',

            requiere_respuesta:
                ModalState.original.requiere_respuesta ? 1 : 0,

            es_sensible:
                ModalState.original.es_sensible ? 1 : 0,

            remitente_nombre:
                ModalState.original.remitente_nombre || '',

            remitente_cargo:
                ModalState.original.remitente_cargo || '',

            remitente_dependencia:
                ModalState.original.remitente_dependencia || '',

            destinatario_nombre:
                ModalState.original.destinatario_nombre || '',

            destinatario_cargo:
                ModalState.original.destinatario_cargo || '',

            destinatario_dependencia:
                ModalState.original.destinatario_dependencia || '',

            quien_elabora_nombre:
                ModalState.original.quien_elabora_nombre || '',

            quien_elabora_cargo:
                ModalState.original.quien_elabora_cargo || '',

            link_documento:
                ModalState.original.link_documento || ''
        };

        return JSON.stringify(current) !== JSON.stringify(original);
    },

    /*
    |--------------------------------------------------------------------------
    | CERRAR
    |--------------------------------------------------------------------------
    */
    bindClose() {

        $(document)
            .off('click.oficios', '#btnCerrarDetalle')
            .on('click.oficios', '#btnCerrarDetalle', function () {

                $('#modalDetalleOficio').modal('hide');

            });

    },

    /*
    |--------------------------------------------------------------------------
    | CANCELAR
    |--------------------------------------------------------------------------
    */
    bindCancel() {

        $(document)
            .off('click.oficios', '#btnCancelarOficio')
            .on('click.oficios', '#btnCancelarOficio', function () {

                const oficioId = ModalState.original?.id;

                if (!oficioId) {

                    Alerts.error(
                        'No se pudo identificar el oficio.'
                    );

                    return;
                }

                Alerts.confirm(
                    '¿Está seguro de cancelar este oficio?'
                )
                .then(function (result) {

                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `${window.LaravelBaseUrl}/oficios/${oficioId}/cancelar`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })

                    .done(function () {

                        Alerts.success(
                            'Oficio cancelado correctamente'
                        )
                        .then(function () {

                            $('#modalDetalleOficio')
                                .modal('hide');

                            location.reload();

                        });

                    })

                    .fail(function (xhr) {

                        console.error(xhr.responseText);

                        if (xhr.status === 403) {

                            Alerts.error(
                                'No tiene permiso para cancelar este oficio.'
                            );

                            return;
                        }

                        Alerts.error(
                            'No se pudo cancelar el oficio.'
                        );

                    });

                });

            });

    },

    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */
    bindSave() {

        $(document)
            .off('click.oficios', '#btnGuardarOficio')
            .on('click.oficios', '#btnGuardarOficio', function () {

                if (!ModalState.original) {
                    Alerts.error('No se pudo identificar el oficio.');
                    return;
                }

                if (!Oficios.hasEditableChanges()) {
                    ModalState.changed = false;
                    $('#btnGuardarOficio').prop('disabled', true);
                    return;
                }

                const $button = $(this);
                const oficioId = ModalState.original.id;
                const data = Oficios.getEditableData();

                $button
                    .prop('disabled', true)
                    .html(`
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        Guardando...
                    `);

                OficiosApi.update(oficioId, data)
                    .done(function (response) {

                        ModalState.original = {
                            ...ModalState.original,
                            ...data
                        };

                        ModalState.changed = false;

                        $button
                            .prop('disabled', true)
                            .html(`
                                <i class="fas fa-save mr-1"></i>
                                Guardar cambios
                            `);

                        Alerts.success(
                            response.message || 'Oficio actualizado correctamente'
                        );
                    })
                    .fail(function (xhr) {

                        console.error(xhr.responseText);

                        $button
                            .prop('disabled', false)
                            .html(`
                                <i class="fas fa-save mr-1"></i>
                                Guardar cambios
                            `);

                        if (xhr.status === 403) {
                            Alerts.error(
                                'No tiene permiso para modificar este oficio.'
                            );
                            return;
                        }

                        if (xhr.status === 422) {
                            Alerts.error(
                                xhr.responseJSON?.message ||
                                'Los datos proporcionados no son válidos.'
                            );
                            return;
                        }

                        Alerts.error(
                            'No se pudieron guardar los cambios.'
                        );
                    });
            });
    },

    restoreOriginalValues() {

        if (!ModalState.original) {
            return;
        }

        const oficio = ModalState.original;

        $('#detalleAsunto').val(oficio.asunto || '');
        $('#detalleDescripcion').val(oficio.descripcion || '');

        $('#detalleRemitenteNombre').val(
            oficio.remitente_nombre || ''
        );

        $('#detalleRemitenteCargo').val(
            oficio.remitente_cargo || ''
        );

        $('#detalleRemitenteDependencia').val(
            oficio.remitente_dependencia || ''
        );

        $('#detalleDestinatarioNombre').val(
            oficio.destinatario_nombre || ''
        );

        $('#detalleDestinatarioCargo').val(
            oficio.destinatario_cargo || ''
        );

        $('#detalleDestinatarioDependencia').val(
            oficio.destinatario_dependencia || ''
        );

        $('#detalleElaboradorNombre').val(
            oficio.quien_elabora_nombre || ''
        );

        $('#detalleElaboradorCargo').val(
            oficio.quien_elabora_cargo || ''
        );

        $('#detalleFechaOficio').val(
            oficio.fecha_oficio || ''
        );

        $('#detalleFechaRecepcion').val(
            oficio.fecha_recepcion || ''
        );

        $('#detalleFechaLimite').val(
            oficio.fecha_limite || ''
        );

        $(
            `#modalDetalleOficio input[name="requiere_respuesta"][value="${oficio.requiere_respuesta ? 1 : 0}"]`
        ).prop('checked', true);

        $(
            `#modalDetalleOficio input[name="es_sensible"][value="${oficio.es_sensible ? 1 : 0}"]`
        ).prop('checked', true);

        $('#detalleLinkDocumento').val(
            oficio.link_documento || ''
        );
    },

    bindCloseGuard() {

        $(document)
            .off('hide.bs.modal.oficios', '#modalDetalleOficio')
            .on(
                'hide.bs.modal.oficios',
                '#modalDetalleOficio',
                function (event) {

                    if (ModalState.allowClose) {
                        ModalState.allowClose = false;
                        return;
                    }

                    if (!ModalState.changed) {
                        return;
                    }

                    event.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Cambios sin guardar',
                        text: 'Este oficio tiene cambios sin guardar.',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar cambios',
                        denyButtonText: 'Deshacer cambios',
                        cancelButtonText: 'Seguir editando',
                        reverseButtons: true
                    }).then(function (result) {

                        if (result.isConfirmed) {
                            $('#btnGuardarOficio').trigger('click');
                            return;
                        }

                        if (result.isDenied) {

                            Oficios.restoreOriginalValues();

                            ModalState.changed = false;

                            $('#btnGuardarOficio').prop(
                                'disabled',
                                true
                            );

                            ModalState.allowClose = true;

                            $('#modalDetalleOficio').modal('hide');
                        }
                    });
                }
            );
    },
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
    allowClose: false,

    reset() {
        this.original = null;
        this.changed = false;
        this.allowClose = false;
    }
};

window.Oficios.bindClose();
window.Oficios.bindCancel();
window.Oficios.enableChangeDetection();
window.Oficios.bindSave();
window.Oficios.bindCloseGuard();

</script>

</body>
</html>