{{-- Vista Oficios --}}

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

<form method="GET">

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

            <form method="POST" action="{{ route('oficios.store') }}">
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
                <div class="modal-footer py-2">

                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-sm btn-primary">
                        Guardar oficio
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
    $('.btn-ver-oficio').on('click', function () {
        Oficios.open($(this).data('id'), false);
    });

});

</script>
@endsection