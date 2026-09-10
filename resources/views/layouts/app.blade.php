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
window.Oficios.bindCancel();

</script>

</body>
</html>