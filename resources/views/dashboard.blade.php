<!DOCTYPE html>

@extends('layouts.app')
<style>

    .modal-oficio {
        max-width: 1400px;
    }

    .modal-oficio .modal-body {
        max-height: 80vh;
        overflow-y: auto;
        padding: 1.25rem 1.5rem;
    }

    /* Secciones */
    .modal-oficio h6 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: .75rem;
        margin-top: .5rem;
    }

    /* Labels */
    .modal-oficio label {
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: .35rem;
    }

    /* Inputs */
    .modal-oficio .form-control,
    .modal-oficio .custom-select {
        font-size: .9rem;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
    }

    /* Textareas */
    .modal-oficio textarea.form-control {
        min-height: 70px;
        resize: vertical;
    }

    /* Espaciado entre filas */
    .modal-oficio .row {
        margin-bottom: .5rem;
    }

    /* Footer */
    .modal-oficio .modal-footer {
        padding: .75rem 1.5rem;
    }

    /* Botones */
    .form-block {
        padding: .75rem 1rem;
        background: #f8f9fc;
        border-radius: .35rem;
        border: 1px solid #eaecf4;
    }

    .actor-title {
        color: #4e73df;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .tipo-oficio-selector {
        display: flex;
        gap: .4rem;
    }

    .tipo-oficio-card {
        flex: 1;
        height: 34px;
        border: 1px solid #d1d3e2;
        border-radius: .3rem;
        background: #fff;
        color: #5a5c69;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }

    .tipo-oficio-card:hover {
        border-color: #4e73df;
        color: #4e73df;
    }

    .tipo-oficio-card.active {
        background: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }

    /* Autocomplete / Sugerencias */

    .oficio-autocomplete {
        position: absolute;
        top: 100%;
        right: 0;
        left: 0;
        z-index: 1055;
        display: none;
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #d1d3e2;
        border-radius: .25rem;
        background: #fff;
        box-shadow: 0 .15rem .5rem rgba(58, 59, 69, .15);
    }

    .sugerencias-personas {
        position: absolute;
        top: 100%;
        right: 0;
        left: 0;
        z-index: 1055;
        display: none;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid #d1d3e2;
        border-radius: .25rem;
        background: #fff;
        box-shadow: 0 .15rem .5rem rgba(58, 59, 69, .15);
    }

    .sugerencia-persona {
        display: block;
        width: 100%;
        padding: .5rem .75rem;
        border: 0;
        border-bottom: 1px solid #eaecf4;
        background: #fff;
        text-align: left;
        cursor: pointer;
    }

    .sugerencia-persona:last-child {
        border-bottom: 0;
    }

    .sugerencia-persona:hover,
    .sugerencia-persona.active {
        background: #f8f9fc;
    }

    .sugerencia-persona-nombre {
        font-size: .8rem;
        font-weight: 700;
        color: #3a3b45;
    }

    .sugerencia-persona-detalle {
        margin-top: .1rem;
        font-size: .7rem;
        color: #858796;
    }

    .sugerencia-oficio {
        display: block;
        width: 100%;
        padding: .5rem .75rem;
        border: 0;
        border-bottom: 1px solid #eaecf4;
        background: #fff;
        text-align: left;
        cursor: pointer;
    }

    .sugerencia-oficio:last-child {
        border-bottom: 0;
    }

    .sugerencia-oficio:hover,
    .sugerencia-oficio.active {
        background: #f8f9fc;
    }

    .sugerencia-oficio-numero {
        font-size: .8rem;
        font-weight: 700;
        color: #3a3b45;
    }

    .sugerencia-oficio-detalle {
        margin-top: .1rem;
        font-size: .7rem;
        color: #858796;
    }

    /* Pestañas tabla */
    .oficios-vistas {
        border-bottom: 1px solid #eaecf4;
    }

    .oficios-vistas .nav-link {
        position: relative;
        border: 0;
        color: #858796;
        font-size: .9rem;
        font-weight: 600;
        padding: .8rem 1rem .75rem;
    }

    .oficios-vistas .nav-link:hover {
        color: #4e73df;
    }

    .oficios-vistas .nav-link.active {
        color: #4e73df;
    }

    .oficios-vistas .nav-link.active::after {
        content: '';
        position: absolute;
        right: 1rem;
        bottom: -1px;
        left: 1rem;
        height: 2px;
        background: #4e73df;
        border-radius: 2px;
    }

    .dataTables_filter {
        margin-bottom: 0;
    }

    .dataTables_filter label {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 0;
        font-size: 0;
    }

    .dataTables_filter input {
        margin-left: 0 !important;
        width: 280px;
        height: 36px;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
        font-size: .875rem;
        padding: .375rem .75rem;
    }

    .dataTables_length {
        margin-bottom: 0;
    }

    .dataTables_length label {
        margin-bottom: 0;
        font-size: .8rem;
        color: #858796;
    }

    .dataTables_length select {
        margin: 0 .35rem;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
        font-size: .8rem;
    }

    .dataTables_paginate {
        margin-top: 0 !important;
    }

    .filtro-fecha {
        position: relative;
    }

    .filtro-fecha-label {
        display: block;
        margin-bottom: .2rem;
        font-size: .7rem;
        font-weight: 600;
        color: #858796;
    }

    .filtro-fecha-input {
        width: 100%;
    }
</style>

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-0 font-weight-bold text-gray-800">
                Oficios
            </h1>
            <p class="text-muted small mb-0">
                Gestión y seguimiento de oficios
            </p>
        </div>

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('recepcion'))
            <button
                type="button"
                class="btn btn-sage btn-sm rounded shadow-sm px-3 font-weight-bold"
                data-toggle="modal"
                data-target="#modalReservarFolios"
            >
                <i class="fas fa-layer-group mr-1"></i>
                Reservar folios
            </button>
        @endif
    </div>

    @include('oficios.partials.oficio-search')

    @include('oficios.partials.oficios-table', [
        'tableId' => 'tabla-oficios'
    ])

</div>

@include('oficios.modals')

@endsection

@section('scripts')
<script>

    $(document).ready(function () {

    $(document).on('click', '.btn-ver-oficio', function () {

        Oficios.open(
            $(this).data('id'),
            false
        );

    });


    const tablaOficios = $('#tabla-oficios').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        dom:
            '<"tabla-oficios-contenido"t>'
            + '<"px-3 pb-3 pt-2"<"row align-items-center"<"col-md-6"l><"col-md-6 text-right"p>>>',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
        },
        columnDefs: [
            {
                targets: [3],
                visible: false,
                searchable: true
            }
        ]
    });


    const formFiltrosOficios =
        $('#formFiltrosOficios');


    // =========================================================
    // CARGAR OFICIOS
    // =========================================================

    window.cargarOficios = function () {

        const url =
            formFiltrosOficios.attr('action') +
            '?' +
            formFiltrosOficios.serialize();

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function () {

                $('#tabla-oficios tbody')
                    .css('opacity', '0.5');

            },
            success: function (html) {

                const filas =
                    $('<tbody>')
                        .html(html)
                        .children('tr');

                tablaOficios
                    .clear()
                    .rows
                    .add(filas.toArray())
                    .draw(false);

                window.history.pushState(
                    {},
                    '',
                    url
                );

            },
            error: function (xhr) {

                console.error(
                    'Error cargando oficios:',
                    xhr
                );

            },
            complete: function () {

                $('#tabla-oficios tbody')
                    .css('opacity', '1');

            }
        });

    };


    $(document).on(
        'change',
        '#formFiltrosOficios select[name="coordinacion_origen_id"], #formFiltrosOficios select[name="estado_id"]',
        function () {

            cargarOficios();

        }
    );


    let timeoutBusqueda;

    $(document).on(
        'input',
        '#inputBuscadorGlobal',
        function () {

            clearTimeout(timeoutBusqueda);

            timeoutBusqueda = setTimeout(function () {

                cargarOficios();

            }, 500);

        }
    );


    const convertirAISO = function (fecha) {

        return [
            fecha.getFullYear(),
            String(fecha.getMonth() + 1).padStart(2, '0'),
            String(fecha.getDate()).padStart(2, '0')
        ].join('-');

    };


    flatpickr('#fechaDesdeVisible', {

        locale: 'es',
        dateFormat: 'd/m/Y',
        allowInput: true,
        disableMobile: true,

        onChange: function (selectedDates) {

            if (!selectedDates.length) {

                $('#fechaDesde').val('');
                cargarOficios();

                return;

            }

            $('#fechaDesde').val(
                convertirAISO(selectedDates[0])
            );

            cargarOficios();

        }

    });


    flatpickr('#fechaHastaVisible', {

        locale: 'es',
        dateFormat: 'd/m/Y',
        allowInput: true,
        disableMobile: true,

        onChange: function (selectedDates) {

            if (!selectedDates.length) {

                $('#fechaHasta').val('');
                cargarOficios();

                return;

            }

            $('#fechaHasta').val(
                convertirAISO(selectedDates[0])
            );

            cargarOficios();

        }

    });


    // Modals

    flatpickr(
        '#formCrearOficio input[name="fecha_oficio"], #formCrearOficio input[name="fecha_recepcion"], #formCrearOficio input[name="fecha_limite"]',
        {
            locale: 'es',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            disableMobile: true
        }
    );

});


    // =========================================================
    // CREAR OFICIO
    // =========================================================

    $(document).on('submit', '#formCrearOficio', function (e) {

        e.preventDefault();

        const $form = $(this);
        const $boton = $form.find('button[type="submit"]');

        // EVITAR DOBLE ENVÍO 
        if ($boton.prop('disabled')) {
            return;
        }

        $boton
            .prop('disabled', true)
            .data('texto-original', $boton.html())
            .html(`
                <i class="fas fa-spinner fa-spin mr-1"></i>
                Guardando...
            `);

        // ENVIAR FORMULARIO
        $.ajax({
            url: `${window.LaravelBaseUrl}/oficios`,
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        .done(function (res) {

            $form[0].reset();

            // RESTAURAR ESTADO DINÁMICO

            $('#bloqueOficioRelacionado').hide();

            $('#oficio_relacionado')
                .prop('required', false)
                .val('');

            $('#respuesta_a_oficio_id').val('');

            $('#oficiosRelacionadosResultados')
                .empty()
                .hide();

            $('#opcionesNumeracion').hide();

            $('#folio_reservado_id').val('');
            $('#folio_reservado_select').empty();

            // CERRAR MODAL

            $('#modalCrearOficio').modal('hide');

            // ACTUALIZAR TABLA

            window.cargarOficios();

            // MENSAJE

            Alerts.success(
                res.message || 'Oficio creado correctamente'
            );
        })

        .fail(function (xhr) {

            console.error(
                'Error al crear oficio:',
                xhr
            );

            const msg =
                xhr.responseJSON?.message ||
                'Error al crear oficio.';

            Alerts.error(msg);

        })

        .always(function () {

            // =====================================================
            // RESTAURAR BOTÓN
            // =====================================================

            $boton
                .prop('disabled', false)
                .html(
                    $boton.data('texto-original')
                );

        });

    });


    // =========================================================
    // GENERAR NÚMERO DE OFICIO
    // =========================================================

    function generarNumeroOficio() {

        const tipo = $('input[name="tipo_oficio_id"]:checked').val();

        // Solo aplica a ENVIADO
        if (tipo != 1) return;


        const coordSelect = document.querySelector(
            '#modalCrearOficio select[name="coordinacion_origen_id"]'
        );

        const coord = coordSelect?.value;


        if (!coord) return;


        fetch(`{{ url('proximo-consecutivo') }}?coordinacion_id=${coord}`)
            .then(res => {

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }

                return res.json();

            })
            .then(data => {


                const bloqueOpciones =
                    document.getElementById('opcionesNumeracion');


                const selectReservados =
                    document.getElementById('folio_reservado_select');


                const radioReservado =
                    document.getElementById('usar_reservado');


                const radioConsecutivo =
                    document.getElementById('usar_consecutivo');


                const numeroOficio =
                    document.getElementById('numero_oficio');


                const consecutivo =
                    document.getElementById('consecutivo');


                const folioReservadoId =
                    document.getElementById('folio_reservado_id');


                const textoReservados =
                    document.getElementById('textoReservados');



                /*
                |--------------------------------------------------------------------------
                | Limpiar estado anterior
                |--------------------------------------------------------------------------
                */


                selectReservados.innerHTML = '';

                folioReservadoId.value = '';



                /*
                |--------------------------------------------------------------------------
                | Si existen reservados
                |--------------------------------------------------------------------------
                */

                if (data.reservados.length > 0) {


                    bloqueOpciones.style.display = 'block';


                    radioReservado.checked = true;


                    selectReservados.disabled = false;



                    data.reservados.forEach((folio, index) => {

                        const option = document.createElement('option');

                        option.value = folio.id;

                        option.textContent = folio.numero_oficio;

                        option.dataset.numeroClean = folio.numero; 

                        selectReservados.appendChild(option);

                        // Primer folio seleccionado automáticamente
                        if (index === 0) {
                            selectReservados.value = folio.id;
                        }

                    });



                    const primero =
                        data.reservados[0];



                    numeroOficio.value =
                        primero.numero_oficio;


                    consecutivo.value =
                        primero.numero;


                    folioReservadoId.value =
                        primero.id;



                    const primeroNumero =
                        data.reservados[0].numero;


                    const ultimoNumero =
                        data.reservados[
                            data.reservados.length - 1
                        ].numero;



                    textoReservados.innerHTML =

                        `${data.cantidad_reservados} folios reservados disponibles ` +
                        `(${String(primeroNumero).padStart(3,'0')} - ${String(ultimoNumero).padStart(3,'0')}).`;



                }

                /*
                |--------------------------------------------------------------------------
                | Si NO existen reservados
                |--------------------------------------------------------------------------
                */

                else {


                    bloqueOpciones.style.display = 'none';


                    numeroOficio.value =
                        data.consecutivo.numero_oficio;


                    consecutivo.value =
                        data.consecutivo.consecutivo;



                }



                /*
                |--------------------------------------------------------------------------
                | Guardamos consecutivo automático
                |--------------------------------------------------------------------------
                */


                radioConsecutivo.dataset.numero =
                    data.consecutivo.numero_oficio;


                radioConsecutivo.dataset.consecutivo =
                    data.consecutivo.consecutivo;



            })
            .catch(err => {

                console.error(
                    'Error generando número de oficio:',
                    err
                );

            });

    }

    $(document).on('change', '#selectCoordinacion', function () {

        const coordinacionId = $(this).val();

        const remitenteNombre = $('input[name="remitente_nombre"]');
        const remitenteCargo = $('input[name="remitente_cargo"]');
        const remitenteDependencia = $('#remitente_dependencia');

        if (!coordinacionId) {
            remitenteNombre.val('');
            remitenteCargo.val('');
            remitenteDependencia.val('');
            return;
        }

        fetch(`{{ url('coordinaciones') }}/${coordinacionId}/coordinador`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }

                return res.json();
            })
            .then(data => {

                if (!data.coordinador) {
                    remitenteNombre.val('');
                    remitenteCargo.val('');
                    remitenteDependencia.val(
                        'Secretaría Ejecutiva del Sistema Estatal Anticorrupción'
                    );
                    return;
                }

                remitenteNombre.val(data.coordinador.nombre);
                remitenteCargo.val(data.coordinador.cargo || '');
                remitenteDependencia.val(
                    'Secretaría Ejecutiva del Sistema Estatal Anticorrupción'
                );
            })
            .catch(err => {
                console.error('Error obteniendo coordinador:', err);
            });
    });


    // ---- Relacion con oficio ----
    document.addEventListener('change', function (e) {

        if (!e.target.matches('input[name="tipo_relacion"]')) {
            return;
        }

        const bloque =
            document.getElementById('bloqueOficioRelacionado');

        const oficioRelacionado =
            document.getElementById('oficio_relacionado');

        const respuestaId =
            document.getElementById('respuesta_a_oficio_id');

        const resultados =
            document.getElementById('oficiosRelacionadosResultados');

        if (e.target.value === 'relacionado') {

            bloque.style.display = 'block';

            oficioRelacionado.required = true;

        } else {

            bloque.style.display = 'none';

            oficioRelacionado.required = false;

            oficioRelacionado.value = '';
            respuestaId.value = '';
            resultados.innerHTML = '';
            resultados.style.display = 'none';

        }

    });

    // ---- Coordinacion -----
    document.addEventListener('change', function (e) {

        if (e.target.matches(
            '#modalCrearOficio select[name="coordinacion_origen_id"]'
        )) {

            generarNumeroOficio();

        }

    });


    document.addEventListener('change', function(e){


        if (
            e.target.matches(
                'input[name="modo_numeracion"]'
            )
        ) {


            const numeroOficio =
                document.getElementById('numero_oficio');


            const consecutivo =
                document.getElementById('consecutivo');


            const folioReservadoId =
                document.getElementById('folio_reservado_id');



            if(e.target.value === 'reservado'){


                const select =
                    document.getElementById(
                        'folio_reservado_select'
                    );


                const option =
                    select.options[
                        select.selectedIndex
                    ];


                numeroOficio.value =
                    option.text;

                consecutivo.value =
                    option.dataset.numeroClean;

                folioReservadoId.value =
                    option.value;

            }


            if(e.target.value === 'consecutivo'){


                numeroOficio.value =
                    document.getElementById(
                        'usar_consecutivo'
                    ).dataset.numero;


                consecutivo.value =
                    document.getElementById(
                        'usar_consecutivo'
                    ).dataset.consecutivo;


                folioReservadoId.value = '';

            }


        }


    });


    // =========================================================
    // ABRIR MODAL TURNAR / RETURNAR
    // =========================================================
    function abrirTurnar(oficioId, numeroOficio, action, returnar = false) {

        $('#turnarNumeroOficio').text(numeroOficio);

        $('#formTurnar').attr('action', action);

        $('#formTurnar')[0].reset();

        $('#modalTurnar .coord-item').removeClass('selected');

        $('#modalTurnar .participacion-select')
            .prop('disabled', true)
            .val('');

        $('#turnarEsReturnado').val(returnar ? '1' : '0');

        if (returnar) {
            $('#turnarTitulo').text('Returnar oficio');
            $('#turnarBotonTexto').text('Returnar oficio');
        } else {
            $('#turnarTitulo').text('Turnar oficio');
            $('#turnarBotonTexto').text('Turnar oficio');
        }

        $('#modalTurnar').modal('show');
    }

    $(document).on('click', '#modalTurnar .coord-item', function(e) {

        if ($(e.target).is('select') || $(e.target).is('option')) {
            return;
        }

        const fila = $(this);
        const select = fila.find('select');

        fila.toggleClass('selected');

        if (fila.hasClass('selected')) {
            select.prop('disabled', false);
        } else {
            select.prop('disabled', true);
            select.val('');
        }

    });

    // =========================================================
    // MOSTRAR INPUT TAG
    // =========================================================
    $(document).on(
        'click',
        '#btn-mostrar-tag',
        function () {

            $('#contenedor-nuevo-tag').show();

            $('#input-tag').focus();

        }
    );


    // =========================================================
    // DINÁMICA TIPO DE OFICIO
    // =========================================================

    function actualizarFormularioTipoOficio() {

        const DEPENDENCIA_SESEA =
            'Secretaría Ejecutiva del Sistema Estatal Anticorrupción';

        const tipo =
            $('input[name="tipo_oficio_id"]:checked').val();

        const bloqueCoordinacion =
            $('#contenedorCoordinacion');

        const selectCoordinacion =
            $('#selectCoordinacion');

        const inputNumero =
            $('#numero_oficio');

        const consecutivo =
            $('#consecutivo');

        const opcionesNumeracion =
            $('#opcionesNumeracion');

        const remitenteDependencia =
            $('#remitente_dependencia');

        const destinatarioDependencia =
            $('#destinatario_dependencia');

        const linkDrive =
            $('#link_drive');

        const asteriscoLinkDrive =
            $('#asteriscoLinkDrive');

        const ayudaLinkDrive =
            $('#ayudaLinkDrive');

        if (tipo == 1) {

            bloqueCoordinacion.show();
            selectCoordinacion.prop('required', true);

            inputNumero.prop('readonly', true);

            remitenteDependencia.val(DEPENDENCIA_SESEA);
            destinatarioDependencia.val('');

            $('#bloqueElaborador').show();

            $('#labelFechaCrear').html(
                'Fecha de envío <span class="text-danger">*</span>'
            );

            linkDrive.prop('required', false);
            asteriscoLinkDrive.hide();

            ayudaLinkDrive.text(
                'Opcional para oficios enviados.'
            );

            generarNumeroOficio();

        } else {

            bloqueCoordinacion.hide();

            selectCoordinacion
                .prop('required', false)
                .val('');

            inputNumero
                .prop('readonly', false)
                .val('');

            consecutivo.val(0);
            opcionesNumeracion.hide();

            remitenteDependencia.val('');
            destinatarioDependencia.val(DEPENDENCIA_SESEA);

            $('#bloqueElaborador').hide();

            $('#labelFechaCrear').html(
                'Fecha de recepción <span class="text-danger">*</span>'
            );

            linkDrive.prop('required', true);

            asteriscoLinkDrive.show();

            ayudaLinkDrive.text(
                'Obligatorio: enlace al documento recibido en Drive.'
            );
        }
    }


    // =========================================================
    // CAMBIAR TIPO DE OFICIO
    // =========================================================

    function setTipoOficio(tipo, vista) {

        $('input[name="tipo_oficio_id"][value="' + tipo + '"]')
            .prop('checked', true)
            .trigger('change');

        $('.tipo-oficio-card').removeClass('active');

        if (vista === 'enviado') {
            $('#btnTipoEnviado').addClass('active');
        }

        if (vista === 'recibido') {
            $('#btnTipoRecibido').addClass('active');
        }

        if (vista === 'recibido_cpc') {
            $('#btnTipoRecibidoCPC').addClass('active');
        }
    }


    // =========================================================
    // CAMBIO DE RADIO DE TIPO
    // =========================================================

    $(document).on(
        'change',
        'input[name="tipo_oficio_id"]',
        function () {
            actualizarFormularioTipoOficio();
        }
    );

    
    // =========================================================
    // RESERVAR NO. OFICIOS
    // =========================================================
    document.getElementById('formReservarFolios').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        const response = await fetch("{{ route('oficios.reservar-folios') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: data
        });

        const result = await response.json();

        if (!response.ok) {
            alert('Error al reservar folios');
            return;
        }

        document.getElementById('resultadoReserva').innerHTML =
            `<div class="alert alert-success mb-0 py-2">
                <i class="fas fa-check-circle mr-1"></i>
                <strong>Reserva realizada correctamente.</strong>
                <br>
                Tus folios reservados son del
                <strong>${result.folio_inicial} al ${result.folio_final}</strong>.
            </div>`;

        // opcional: reset
        form.reset();
    });


    // =========================================================
    // CAMBIO MANUAL DE FOLIO RESERVADO
    // =========================================================
    document.addEventListener('change', function (e) {
        if (e.target.id === 'folio_reservado_select') {
            const select = e.target;
            const option = select.options[select.selectedIndex];
            if (!option) return;

            document.getElementById('numero_oficio').value = option.text;
            document.getElementById('consecutivo').value = option.dataset.numeroClean;
            document.getElementById('folio_reservado_id').value = option.value;
        }
    });

</script>

@endsection