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

    <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearOficio">
        Nuevo Oficio
    </button>
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
// DATATABLES
// =========================================================
$(document).ready(function () {

   //

});

// =========================================================
// GENERAR NÚMERO DE OFICIO
// =========================================================

function generarNumeroOficio() {

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

            document.getElementById('numero_oficio').value =
                data.numero_oficio;

            document.getElementById('consecutivo').value =
                data.consecutivo;

        })
        .catch(err => {

            console.error(
                'Error generando número de oficio:',
                err
            );

        });

}

document.addEventListener('change', function (e) {

    if (e.target.matches(
        '#modalCrearOficio select[name="coordinacion_origen_id"]'
    )) {

        generarNumeroOficio();

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

</script>

@endsection