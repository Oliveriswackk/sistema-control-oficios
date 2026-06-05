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

<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Sistema Control de Oficios
        </h6>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($oficios as $oficio)

                        <tr>
                            <td>{{ $oficio->id }}</td>

                            <td>
                                {{ $oficio->numero_oficio }}
                            </td>

                            <td>
                                {{ $oficio->asunto }}
                            </td>

                            <td>
                                {{ $oficio->estado->nombre }}
                            </td>

                            <td>
                                <button class="btn btn-primary btn-ver-oficio" data-id="{{ $oficio->id }}">
                                    Ver
                                </button>

                                @if($oficio->estado_id != 5)
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalTurnar{{ $oficio->id }}">
                                        Turnar
                                    </button>
                                @else
                                    <span class="badge badge-secondary">
                                        Cerrado
                                    </span>
                                @endif
                                
                            </td>
                            
                        </tr>
                    
                        
                    {{-- MODAL TURNAR --}}
                    <div class="modal fade" id="modalTurnar{{ $oficio->id }}" tabindex="-1" role="dialog">

                        <div class="modal-dialog" role="document">

                            <div class="modal-content">

                                <form
                                    method="POST"
                                    action="{{ route('oficios.turnar', $oficio->id) }}"
                                >

                                    @csrf

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Turnar Oficio
                                        </h5>

                                        <button type="button"  class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <div class="form-group">

                                            <label>Coordinación</label>

                                            <select name="coordinacion_id" class="form-control" required>

                                                @foreach(\App\Models\Coordinacion::all() as $coord)

                                                    <option value="{{ $coord->id }}">
                                                        {{ $coord->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Persona a cargo</label>

                                            <select name="usuario_id" class="form-control" required>

                                                @foreach(\App\Models\User::all() as $usuario)

                                                    <option value="{{ $usuario->id }}">
                                                        {{ $usuario->name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Tipo participación</label>

                                            <select name="tipo_participacion_id" class="form-control" required>

                                                @foreach(\App\Models\TipoParticipacion::all() as $tipo)

                                                    <option value="{{ $tipo->id }}">
                                                        {{ $tipo->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Observaciones</label>

                                            <textarea  name="observaciones" class="form-control" rows="3"></textarea>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="submit" class="btn btn-warning">
                                            Turnar
                                        </button>

                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Cancelar
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </tbody>

            </table>

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

                    {{-- =========================
                        IDENTIFICACIÓN
                    ========================= --}}
                    <h6 class="text-primary font-weight-bold mb-2">
                        Identificación
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label>Número Oficio  <span class="text-danger">*</span></label>
                            <input type="text" name="numero_oficio" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Consecutivo  <span class="text-danger">*</span></label>
                            <input type="text" name="consecutivo" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Asunto  <span class="text-danger">*</span></label>
                            <input type="text" name="asunto" class="form-control form-control-sm" required>
                        </div>

                    </div>

                    {{-- =========================
                        FECHAS
                    ========================= --}}
                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label>Fecha oficio  <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_oficio"
                                class="form-control form-control-sm"
                                value="{{ now()->toDateString() }}"
                                required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Fecha recepción <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_recepcion"
                                class="form-control form-control-sm"
                                value="{{ now()->toDateString() }}"
                                required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Fecha límite</label>
                            <input type="date" name="fecha_limite" class="form-control form-control-sm">
                        </div>

                    </div>

                    {{-- =========================
                        CONTENIDO
                    ========================= --}}
                    <h6 class="text-primary font-weight-bold mb-2">
                        Contenido del Oficio
                    </h6>


                        <div class="col-md-12 mb-3">

                            <label>Descripción</label>

                            <textarea
                                name="descripcion"
                                class="form-control form-control-sm"
                                rows="3"
                            ></textarea>

                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Link documento</label>
                            <input type="text" name="link_documento" class="form-control form-control-sm">
                        </div>

                    {{-- =========================
                        REMITENTE
                    ========================= --}}
                    <h6 class="text-primary font-weight-bold mb-2">
                        Remitente
                    </h6>

                        <div class="row">

                            <div class="col-md-4 mb-2">
                                <label>Nombre</label>
                                <input type="text" name="remitente_nombre" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Cargo</label>
                                <input type="text" name="remitente_cargo" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Dependencia</label>
                                <input type="text" name="remitente_dependencia" class="form-control form-control-sm">
                            </div>

                        </div>

                    {{-- =========================
                       DESTINATARIO
                    ========================= --}}
                    <h6 class="text-primary font-weight-bold mb-2">
                        Destinatario
                    </h6>

                        <div class="row">


                            <div class="col-md-4 mb-2">
                                <label>Nombre</label>
                                <input type="text" name="destinatario_nombre" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Cargo</label>
                                <input type="text" name="destinatario_cargo" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Dependencia</label>
                                <input type="text" name="destinatario_dependencia" class="form-control form-control-sm">
                            </div>

                        </div>

                    {{-- =========================
                       Elabora
                    ========================= --}}
                    <h6 class="text-primary font-weight-bold mb-2">
                        Elaborador 
                    </h6>

                        <div class="row">


                            <div class="col-md-4 mb-2">
                                <label>Nombre</label>
                                <input type="text" name="quien_elabora_nombre" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Cargo</label>
                                <input type="text" name="quien_elabora_cargo" class="form-control form-control-sm">
                            </div>

                        </div>

                    {{-- =========================
                        FLAGS OPERATIVOS
                    ========================= --}}
                    <div class="row">

                        <div class="col-md-4 mb-2">

                            <label>Requiere respuesta</label>

                            <div class="d-flex">

                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="req_no" name="requiere_respuesta"
                                        value="0" class="custom-control-input" required>
                                    <label class="custom-control-label" for="req_no">No</label>
                                </div>

                                <div class="custom-control custom-radio">
                                    <input type="radio" id="req_si" name="requiere_respuesta"
                                        value="1" class="custom-control-input">
                                    <label class="custom-control-label" for="req_si">Sí</label>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 mb-2">

                            <label>Documento sensible</label>

                            <div class="d-flex">

                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" id="sens_no" name="es_sensible"
                                        value="0" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="sens_no">No</label>
                                </div>

                                <div class="custom-control custom-radio">
                                    <input type="radio" id="sens_si" name="es_sensible"
                                        value="1" class="custom-control-input">
                                    <label class="custom-control-label" for="sens_si">Sí</label>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 mb-2">

                            <label>
                                Tipo de oficio
                                <span class="text-danger">*</span>
                            </label>

                            <div>
                                <div class="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="tipo_enviado"
                                        name="tipo_oficio_id"
                                        value="1"
                                        class="custom-control-input"
                                        checked
                                    >
                                    <label
                                        class="custom-control-label"
                                        for="tipo_enviado"
                                    >
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
                                    >
                                    <label
                                        class="custom-control-label"
                                        for="tipo_recibido"
                                    >
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
                                    >
                                    <label
                                        class="custom-control-label"
                                        for="tipo_cpc"
                                    >
                                        Recibido CPC
                                    </label>
                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- hidden sistema --}}
                    <input type="hidden" name="usuario_registro_id" value="{{ auth()->id() }}">

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