{{-- Vista Oficios --}}
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

</style>
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Oficios
    </h1>

    <div class="d-flex justify-content-end align-items-center" >

        @can('create', App\Models\Oficio::class)
            <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalCrearOficio">
                Nuevo Oficio
            </button>
        @endcan

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

{{-- MODAL CREAR OFICIO --}}
<div class="modal fade" id="modalCrearOficio" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-xl modal-oficio" role="document">

        <div class="modal-content">

            <form id="formCrearOficio">
                @csrf

                {{-- HEADER --}}
                <div class="modal-header py-2">

                    <div>
                        <h5 class="modal-title mb-0">
                            Crear Oficio
                        </h5>

                        <small class="text-muted">
                            Campos con <span class="text-danger">*</span> son obligatorios
                        </small>
                    </div>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                {{-- BODY --}}
                <div class="modal-body py-2">

                    @include('oficios.partials.oficio-form')

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Guardar Oficio
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- MODAL RESERVAR NÚMERO DE OFICIO --}}
<div class="modal fade" id="modalReservarFolios" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">

            <form id="formReservarFolios">

                @csrf

                <div class="modal-header py-2">

                    <h5 class="modal-title">
                        Reservar Folios
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    {{-- COORDINACIÓN --}}
                    <div class="form-group">
                        <label>Coordinación</label>

                        <select class="form-control" name="coordinacion_id" required>
                            @foreach(App\Models\Coordinacion::all() as $coord)
                                <option value="{{ $coord->id }}">
                                    {{ $coord->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FECHA --}}
                    <div class="form-group">
                        <label>Fecha base</label>

                        <input type="date"
                               class="form-control"
                               name="fecha"
                               value="{{ now()->toDateString() }}"
                               required>
                    </div>

                    {{-- CANTIDAD --}}
                    <div class="form-group">
                        <label>Cantidad de folios</label>

                        <input type="number"
                               class="form-control"
                               name="cantidad"
                               min="1"
                               max="200"
                               value="10"
                               required>
                    </div>

                    <div id="resultadoReserva" class="small text-muted"></div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Reservar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
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
        url: '/oficios',
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


    fetch(`/oficios/proximo-consecutivo?coordinacion_id=${coord}`)
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


                    const option =
                        document.createElement('option');


                    option.value = folio.id;


                    option.textContent =
                        folio.numero_oficio;


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
                option.value;


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

    const DEPENDENCIA_SESEA = 'Secretaría Ejecutiva del Sistema Estatal Anticorrupción';
    
    const tipo = $('input[name="tipo_oficio_id"]:checked').val();

    const bloqueCoordinacion = $('#bloqueCoordinacion');

    const selectCoordinacion = $('select[name="coordinacion_origen_id"]');

    const inputNumero = $('#numero_oficio');

    const textoAyuda = $('#textoNumeroAutomatico');

    const consecutivo = $('#consecutivo');
    
    const remitenteDependencia = $('#remitente_dependencia');

    const destinatarioDependencia = $('#destinatario_dependencia');


    if (tipo == 1) {

        // ==========================
        // ENVIADO
        // ==========================

        bloqueCoordinacion.show();

        selectCoordinacion.prop('required', true);

        inputNumero.prop('readonly', true);

        textoAyuda.text('Generado automáticamente por el sistema.');

        remitenteDependencia.val(DEPENDENCIA_SESEA);

        destinatarioDependencia.val('');

        generarNumeroOficio();

    } else {

        // ==========================
        // RECIBIDO / RECIBIDO CPC
        // ==========================

        bloqueCoordinacion.hide();

        selectCoordinacion.prop('required', false);

        inputNumero.prop('readonly', false);

        textoAyuda.text('Captura manual del número de oficio.');

        remitenteDependencia.val('');

        destinatarioDependencia.val(DEPENDENCIA_SESEA);

        consecutivo.val(0);

    }

}

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
        `Se reservaron ${result.cantidad} folios.<br>Grupo: ${result.grupo_uuid}`;

    // opcional: reset
    form.reset();
});
</script>

@endsection