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
    .modal-oficio .btn {
        font-size: .9rem;
        font-weight: 600;
        padding: .45rem 1rem;
    }

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
</style>
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Oficios
    </h1>

    <div class="d-flex justify-content-end align-items-center" >

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('recepcion'))
            <button class="btn btn-indigo" data-toggle="modal" data-target="#modalReservarFolios">
                Reservar Folios
            </button>
        @endif
    </div>
    

</div>


{{-- Filtros de búsqueda --}}

<form method="GET" action="{{ route('dashboard') }}">

    @include('oficios.partials.oficio-search')

</form>


{{-- Tabla de Oficios --}}
<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Sistema Control de Oficios
        </h6>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            @include('oficios.partials.oficios-table', [
                'tableId' => 'tabla-oficios'
            ])

        </div>

    </div>

</div>

<!-- Llamar Modales -->
@include('oficios.modals')


@endsection

@section('scripts')
<script>

$(document).ready(function () {

    // Ver Detalles del Oficio
    $(document).on('click', '.btn-ver-oficio', function () {

        Oficios.open(
            $(this).data('id'),
            false
        );

    });

     $('#tabla-oficios').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
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

    // Cambiar etiqueta fecha según tipo de oficio
    $('input[name="tipo_oficio_id"]').on('change', function () {

        const tipo = $('input[name="tipo_oficio_id"]:checked').val();

        if (tipo == 1) {

            $('#labelFechaCrear').html(
                'Fecha de envío <span class="text-danger">*</span>'
            );

        } else {

            $('#labelFechaCrear').html(
                'Fecha de recepción <span class="text-danger">*</span>'
            );

        }

    });

});

// =========================================================
// CREAR OFICIO
// =========================================================
$(document).on('submit', '#formCrearOficio', function (e) {
    e.preventDefault();

    const $form = $(this);

    const data = $form.serialize();
    $.ajax({
        url: `${window.LaravelBaseUrl}/oficios`,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done((res) => {

        Alerts.success('Oficio creado correctamente');

        $('#modalCrearOficio').modal('hide');

        const table = $('#tabla-oficios').DataTable();

        table.row
            .add($(res.row))
            .draw(false);

        $('#formCrearOficio')[0].reset();

    })
    .fail((xhr) => {

        console.log(xhr.responseJSON);

        const msg = xhr.responseJSON?.message || 'Error al crear oficio';

        Alerts.error(msg);
    });
});

// =========================================================
// CAMBIAR LABEL DE FECHA
// =========================================================
$(document).on('change', '#tipo_oficio_id', function () {

    const tipo = $(this).val();

    if (tipo == 1) {

        $('#labelFechaCrear').html(
            'Fecha de envío <span class="text-danger">*</span>'
        );

    } else {

        $('#labelFechaCrear').html(
            'Fecha de recepción <span class="text-danger">*</span>'
        );

    }

});


// =========================================================
// DATATABLES
// =========================================================
$(document).ready(function () {

   //

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
// ABRIR MODAL TURNAR
// =========================================================
function abrirTurnar(oficioId) {

    $('#modalGlobalTitle').text('Turnar oficio');

    $('#modalGlobalBody').html('<div class="text-center p-3">Cargando...</div>');

    $('#modalGlobalFooter').html(`
    <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary btn-cerrar-modal">
            Cerrar
        </button>

        <button type="button"
                class="btn btn-primary"
                id="btnGuardarTurnado">
            Guardar turnado
        </button>
    </div>
    `);

    $('#modalGlobal').modal('show');

    OficiosApi.getTurnarModal(oficioId)

        .done(function(html) {

            $('#modalGlobalBody').html(html);

        })

        .fail(function() {

            $('#modalGlobalBody').html(
                '<div class="text-danger p-3">Error cargando modal</div>'
            );

        });

}


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
// GUARDAR TURNADO
// =========================================================
$(document).on('click', '#btnGuardarTurnado', function () {

    const form = $('#modalGlobalBody').find('form');

    if (!form.length) {
        Alerts.error('No se encontró el formulario de turnado');
        return;
    }

    // validación simple del navegador
    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }

    form.submit();
});


// =========================================================
// MOSTRAR / OCULTAR COORDINACIONES
// =========================================================
$(document).on('change', '.coord-toggle', function () {

    const id = $(this).data('id');

    const block = $('#modalGlobalBody')
        .find(`.coord-block[data-coord="${id}"]`);

    if ($(this).is(':checked')) {

        block.show();

    } else {

        block.hide();

    }

});


// =========================================================
// SELECCIÓN DE USUARIOS
// =========================================================
$(document).on('change', '.user-check', function () {

    const row = $(this).closest('tr');

    const select = row.find('.participation-select');

    if ($(this).is(':checked')) {

        row.addClass('table-primary');

        select.prop('disabled', false);

    } else {

        row.removeClass('table-primary');

        select.prop('disabled', true);

    }

});

// =========================================================
// DINÁMICA TIPO DE OFICIO
// =========================================================

function actualizarFormularioTipoOficio() {

    const DEPENDENCIA_SESEA =
        'Secretaría Ejecutiva del Sistema Estatal Anticorrupción';

    const tipo =
        $('input[name="tipo_oficio_id"]:checked').val();

    // IMPORTANTE:
    // El ID REAL del modal es #contenedorCoordinacion
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


    // =====================================================
    // ENVIADO
    // =====================================================

    if (tipo == 1) {

        // MOSTRAR coordinación
        bloqueCoordinacion.show();

        // Coordinación obligatoria
        selectCoordinacion
            .prop('required', true);


        // Número generado por sistema
        inputNumero
            .prop('readonly', true);


        // SESEA es el remitente
        remitenteDependencia.val(
            DEPENDENCIA_SESEA
        );


        // Destinatario se captura manualmente
        destinatarioDependencia.val('');


        // Elaborador sí aplica
        $('#bloqueElaborador').show();


        // Cambiar etiqueta
        $('#labelFechaCrear').html(
            'Fecha de envío <span class="text-danger">*</span>'
        );


        // Generar número
        generarNumeroOficio();

    }


    // =====================================================
    // RECIBIDO / CPC
    // =====================================================

    else {

        // OCULTAR COMPLETAMENTE coordinación
        bloqueCoordinacion.hide();

        // No es obligatoria
        selectCoordinacion
            .prop('required', false)
            .val('');


        // Número capturado manualmente
        inputNumero
            .prop('readonly', false)
            .val('');


        // No utiliza consecutivo de SESEA
        consecutivo.val(0);


        // Ocultar opciones de numeración
        opcionesNumeracion.hide();


        // Remitente externo
        remitenteDependencia.val('');


        // SESEA es el destinatario
        destinatarioDependencia.val(
            DEPENDENCIA_SESEA
        );


        // Elaborador no aplica
        $('#bloqueElaborador').hide();


        // Cambiar etiqueta
        $('#labelFechaCrear').html(
            'Fecha de recepción <span class="text-danger">*</span>'
        );

    }
}


// =========================================================
// CAMBIAR TIPO DE OFICIO
// =========================================================

function setTipoOficio(tipo, vista) {

    // Seleccionar radio real
    $('input[name="tipo_oficio_id"][value="' + tipo + '"]')
        .prop('checked', true);


    // Cambiar tarjetas visuales
    $('.tipo-oficio-card')
        .removeClass('active');


    if (vista === 'enviado') {

        $('#btnTipoEnviado')
            .addClass('active');

    }

    if (vista === 'recibido') {

        $('#btnTipoRecibido')
            .addClass('active');

    }

    if (vista === 'recibido_cpc') {

        $('#btnTipoRecibidoCPC')
            .addClass('active');

    }


    // Aplicar inmediatamente la lógica
    actualizarFormularioTipoOficio();
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
// AL ABRIR EL MODAL
// =========================================================

$('#modalCrearOficio').on(
    'shown.bs.modal',
    function () {

        actualizarFormularioTipoOficio();

    }
);

function setTipoOficio(tipo, vista) {

    /*
     * Cambiar el radio real que se enviará al backend
     */
    $('input[name="tipo_oficio_id"][value="' + tipo + '"]')
        .prop('checked', true)
        .trigger('change');


    /*
     * Cambiar únicamente el estado visual
     * de las tarjetas.
     */
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


$(document).on('change', 'input[name="tipo_oficio_id"]', function () {

    actualizarFormularioTipoOficio();

});


$('#modalCrearOficio').on('shown.bs.modal', function () {

    actualizarFormularioTipoOficio();

});


// Cambio de radio buttons
$(document).on('change', 'input[name="tipo_oficio_id"]', function () {

    actualizarFormularioTipoOficio();

});


// Inicialización al abrir modal
$('#modalCrearOficio').on('shown.bs.modal', function () {
    actualizarFormularioTipoOficio();
});

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