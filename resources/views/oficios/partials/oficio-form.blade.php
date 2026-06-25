{{-- MODAL Crear Oficio --}}

{{-- =========================
    CREACIÓN
========================= --}}
<div class="row">

    <!-- Tipo -->
    <div class="col-md-6 mb-2">
        <label>
            Tipo de oficio
            <span class="text-danger">*</span>
        </label>

        <div class="d-flex flex-wrap">

            <div class="custom-control custom-radio mr-4 mb-2">
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

            <div class="custom-control custom-radio mr-4 mb-2">
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

            <div class="custom-control custom-radio mb-2">
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

    <div class="col-md-4 mb-2">
        <label>Coordinación <span class="text-danger">*</span></label>

        <select name="coordinacion_origen_id" class="form-control" required>

            <option value="" selected disabled>
                Seleccionar
            </option>

            @foreach($coordinaciones as $coordinacion)

                <option value="{{ $coordinacion->id }}">
                    {{ $coordinacion->clave }} - {{ $coordinacion->nombre }}
                </option>

            @endforeach

        </select>
    </div>

</div>


{{-- =========================
    IDENTIFICACIÓN
========================= --}}
<h6 class="text-primary font-weight-bold mb-2">
    Información Principal
</h6>

<div class="row">

    <div class="col-md-6 mb-2">
        <label>Número de oficio <span class="text-danger">*</span></label>

        <input
            type="text"
            id="numero_oficio"
            name="numero_oficio"
            class="form-control form-control-sm"
            required
        >

        <small class="text-muted">
            Generado automáticamente.
        </small>
    </div>

    <!-- Consecutivo-->
    <input type="hidden" id="consecutivo" name="consecutivo">

</div>


{{-- =========================
    FECHAS
========================= --}}
<div class="row">

    <div class="col-md-4 mb-2">
        <label>Fecha creación oficio  <span class="text-danger">*</span></label>
        <input type="date" name="fecha_oficio"
            class="form-control form-control-sm"
            value="{{ now()->toDateString() }}"
            required>
    </div>

    <div class="col-md-4 mb-2">
        <label>Fecha de enviado <span class="text-danger">*</span></label>
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
    CONTENIDO
========================= --}}
<h6 class="text-primary font-weight-bold mb-2">
    Contenido
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
    <label>Link</label>
    <input type="text" name="link_documento" class="form-control form-control-sm">
</div>


{{-- =========================
    FLAGS OPERATIVOS
========================= --}}
<h6 class="text-primary font-weight-bold mb-2">
    Control del oficio
</h6>

<div class="row">

    <!-- Inicio o Respuesta -->
    <div class="form-group col-md-6 mb-2">

        <label>¿Responde a otro oficio? <span class="text-danger">*</span></label>

        <select name="respuesta_a_oficio_id" class="form-control" required>

            <option value="" selected disabled>
                Seleccionar
            </option>

            <option value="0">
                Inicia una petición
            </option>

            @foreach($oficiosRelacionables as $oficio)
                <option value="{{ $oficio->id }}">
                    {{ $oficio->numero_oficio }} - {{ $oficio->asunto }}
                </option>
            @endforeach

        </select>

    </div>

    <!-- Necesita Respuesta -->
    <div class="col-md-6 mb-2">

        <label>Requiere respuesta
            <span class="text-danger">*</span>
        </label>

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

    <!-- Tags -->
    <div class="form-group col-md-6 mb-2">

        <label>Palabras clave</label>

        <input
            type="text"
            name="tags"
            class="form-control"
            placeholder="usuarios, transparencia, accesos">

        <small class="text-muted">
            Separar con comas.
        </small>

    </div>

    <!-- Sensible -->
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

</div>

{{-- hidden sistema --}}
<input type="hidden" name="usuario_registro_id" value="{{ auth()->id() }}">