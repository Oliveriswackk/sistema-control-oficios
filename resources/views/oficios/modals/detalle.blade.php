<div>

    {{-- =========================
        IDENTIFICACIÓN
    ========================= --}}
    <h6 class="text-primary font-weight-bold mb-2">Identificación</h6>

    <div class="row">

        <div class="col-md-4 mb-2">
            <label>Número Oficio</label>
            <input type="text"
                   name="numero_oficio"
                   class="form-control form-control-sm"
                   value="{{ $oficio->numero_oficio }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Consecutivo</label>
            <input type="text"
                   name="consecutivo"
                   class="form-control form-control-sm"
                   value="{{ $oficio->consecutivo }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Asunto</label>
            <input type="text"
                   name="asunto"
                   class="form-control form-control-sm"
                   value="{{ $oficio->asunto }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

    </div>

    {{-- =========================
        FECHAS
    ========================= --}}
    <div class="row">

        <div class="col-md-4 mb-2">
            <label>Fecha oficio</label>
            <input type="date"
                   name="fecha_oficio"
                   class="form-control form-control-sm"
                   value="{{ optional($oficio->fecha_oficio)->format('Y-m-d') ?? '' }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Fecha recepción</label>
            <input type="date"
                   name="fecha_recepcion"
                   class="form-control form-control-sm"
                   value="{{ optional($oficio->fecha_recepcion)->format('Y-m-d') ?? '' }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Fecha límite</label>
            <input type="date"
                   name="fecha_limite"
                   class="form-control form-control-sm"
                   value="{{ optional($oficio->fecha_limite)->format('Y-m-d') ?? '' }}"
                   {{ $editable ? '' : 'readonly' }}>
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
                  rows="3"
                  {{ $editable ? '' : 'readonly' }}>{{ $oficio->descripcion }}</textarea>

    </div>

    <div class="col-md-12 mb-3">
        <label>Link documento</label>
        <input type="text"
               name="link_documento"
               class="form-control form-control-sm"
               value="{{ $oficio->link_documento }}"
               {{ $editable ? '' : 'readonly' }}>
    </div>

    {{-- =========================
        REMITENTE
    ========================= --}}
    <h6 class="text-primary font-weight-bold mb-2">Remitente</h6>

    <div class="row">

        <div class="col-md-4 mb-2">
            <label>Nombre</label>
            <input type="text"
                   name="remitente_nombre"
                   class="form-control form-control-sm"
                   value="{{ $oficio->remitente_nombre }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Cargo</label>
            <input type="text"
                   name="remitente_cargo"
                   class="form-control form-control-sm"
                   value="{{ $oficio->remitente_cargo }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Dependencia</label>
            <input type="text"
                   name="remitente_dependencia"
                   class="form-control form-control-sm"
                   value="{{ $oficio->remitente_dependencia }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

    </div>

    {{-- =========================
        DESTINATARIO
    ========================= --}}
    <h6 class="text-primary font-weight-bold mb-2">Destinatario</h6>

    <div class="row">

        <div class="col-md-4 mb-2">
            <label>Nombre</label>
            <input type="text"
                   name="destinatario_nombre"
                   class="form-control form-control-sm"
                   value="{{ $oficio->destinatario_nombre }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Cargo</label>
            <input type="text"
                   name="destinatario_cargo"
                   class="form-control form-control-sm"
                   value="{{ $oficio->destinatario_cargo }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Dependencia</label>
            <input type="text"
                   name="destinatario_dependencia"
                   class="form-control form-control-sm"
                   value="{{ $oficio->destinatario_dependencia }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

    </div>

    {{-- =========================
        ELABORADOR
    ========================= --}}
    <h6 class="text-primary font-weight-bold mb-2">Elaborador</h6>

    <div class="row">

        <div class="col-md-4 mb-2">
            <label>Nombre</label>
            <input type="text"
                   name="quien_elabora_nombre"
                   class="form-control form-control-sm"
                   value="{{ $oficio->quien_elabora_nombre }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

        <div class="col-md-4 mb-2">
            <label>Cargo</label>
            <input type="text"
                   name="quien_elabora_cargo"
                   class="form-control form-control-sm"
                   value="{{ $oficio->quien_elabora_cargo }}"
                   {{ $editable ? '' : 'readonly' }}>
        </div>

    </div>

    {{-- =========================
        CONFIGURACIÓN
    ========================= --}}
    <h6 class="text-primary font-weight-bold mb-2">Configuración</h6>

    <div class="row">

        {{-- REQUIERE RESPUESTA --}}
        <div class="col-md-4 mb-2">

            <label>Requiere respuesta</label>

            <div class="d-flex">

                <div class="custom-control custom-radio mr-3">
                    <input type="radio"
                        id="req_0"
                        name="requiere_respuesta"
                        value="0"
                        class="custom-control-input"
                        {{ $oficio->requiere_respuesta == 0 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="req_0">No</label>
                </div>

                <div class="custom-control custom-radio">
                    <input type="radio"
                        id="req_1"
                        name="requiere_respuesta"
                        value="1"
                        class="custom-control-input"
                        {{ $oficio->requiere_respuesta == 1 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="req_1">Sí</label>
                </div>

            </div>
        </div>

        {{-- SENSIBLE --}}
        <div class="col-md-4 mb-2">

            <label>Documento sensible</label>

            <div class="d-flex">

                <div class="custom-control custom-radio mr-3">
                    <input type="radio"
                        id="sens_0"
                        name="es_sensible"
                        value="0"
                        class="custom-control-input"
                        {{ $oficio->es_sensible == 0 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="sens_0">No</label>
                </div>

                <div class="custom-control custom-radio">
                    <input type="radio"
                        id="sens_1"
                        name="es_sensible"
                        value="1"
                        class="custom-control-input"
                        {{ $oficio->es_sensible == 1 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="sens_1">Sí</label>
                </div>

            </div>
        </div>

        {{-- TIPO OFICIO --}}
        <div class="col-md-4 mb-2">

            <label>Tipo de oficio</label>

            <div>

                <div class="custom-control custom-radio">
                    <input type="radio"
                        id="tipo_1"
                        name="tipo_oficio_id"
                        value="1"
                        class="custom-control-input"
                        {{ $oficio->tipo_oficio_id == 1 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="tipo_1">Enviado</label>
                </div>

                <div class="custom-control custom-radio">
                    <input type="radio"
                        id="tipo_2"
                        name="tipo_oficio_id"
                        value="2"
                        class="custom-control-input"
                        {{ $oficio->tipo_oficio_id == 2 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="tipo_2">Recibido</label>
                </div>

                <div class="custom-control custom-radio">
                    <input type="radio"
                        id="tipo_3"
                        name="tipo_oficio_id"
                        value="3"
                        class="custom-control-input"
                        {{ $oficio->tipo_oficio_id == 3 ? 'checked' : '' }}
                        {{ $editable ? '' : 'disabled' }}>
                    <label class="custom-control-label" for="tipo_3">Recibido CPC</label>
                </div>

            </div>

        </div>

    </div>

</div>