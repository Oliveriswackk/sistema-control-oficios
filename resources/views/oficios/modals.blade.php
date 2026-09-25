{{-- ============================================================
    1. MODAL CREAR OFICIO
============================================================ --}}
<div class="modal fade"
     id="modalCrearOficio"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalCrearOficioLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">

        <div class="modal-content border-0 shadow-lg" style="border-radius: .4rem; overflow: hidden;">

            <form id="formCrearOficio">

                {{-- HEADER --}}
                <div class="modal-header bg-light px-4 py-3 border-bottom">
                    <h5 class="modal-title text-primary font-weight-bold" id="modalCrearOficioLabel">
                        <i class="fas fa-file-alt mr-2"></i> Registrar oficio
                    </h5>
                    <button type="button" class="close text-gray-500" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body px-4 py-3 bg-white">

                    {{-- 1. TIPO DE OFICIO --}}
                    <div class="form-block mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="mb-1">
                                    Tipo de oficio <span class="text-danger">*</span>
                                </label>

                                <div class="tipo-oficio-selector">
                                    <button type="button"
                                            class="tipo-oficio-card active"
                                            id="btnTipoEnviado"
                                            onclick="setTipoOficio('1', 'enviado')">
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        Enviado
                                    </button>

                                    <button type="button"
                                            class="tipo-oficio-card"
                                            id="btnTipoRecibido"
                                            onclick="setTipoOficio('2', 'recibido')">
                                        <i class="fas fa-inbox mr-1"></i>
                                        Recibido
                                    </button>

                                    <button type="button"
                                            class="tipo-oficio-card"
                                            id="btnTipoRecibidoCPC"
                                            onclick="setTipoOficio('3', 'recibido_cpc')">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        CPC
                                    </button>

                                    <input type="radio"
                                           id="tipo_enviado"
                                           name="tipo_oficio_id"
                                           value="1"
                                           class="d-none"
                                           checked>

                                    <input type="radio"
                                           id="tipo_recibido"
                                           name="tipo_oficio_id"
                                           value="2"
                                           class="d-none">

                                    <input type="radio"
                                           id="tipo_cpc"
                                           name="tipo_oficio_id"
                                           value="3"
                                           class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. INFORMACIÓN PRINCIPAL --}}
                    <div class="form-block mb-3">
                        <div class="row">
                            <div class="col-md-4" id="contenedorCoordinacion">
                                <label class="mb-1">
                                    Coordinación emisora <span class="text-danger">*</span>
                                </label>

                                <select name="coordinacion_origen_id"
                                        id="selectCoordinacion"
                                        class="form-control form-control-sm">
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

                            <div class="col-md-4">
                                <label class="mb-1" id="labelNumeroOficio">
                                    Número de oficio <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    id="numero_oficio"
                                    name="numero_oficio"
                                    class="form-control form-control-sm font-mono"
                                    readonly
                                    required>

                                <input type="hidden"
                                    id="consecutivo"
                                    name="consecutivo">

                                <input type="hidden"
                                    id="folio_reservado_id"
                                    name="folio_reservado_id">
                            </div>

                            <div class="col-md-4">
                                <label class="mb-1">
                                    Fecha del oficio <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    name="fecha_oficio"
                                    class="form-control form-control-sm fecha-oficio"
                                    value="{{ now()->toDateString() }}"
                                    placeholder="dd/mm/yyyy"
                                    autocomplete="off"
                                    required>
                            </div>
                        </div>

                        <div id="opcionesNumeracion"
                             class="mt-3 p-3 bg-white border rounded"
                             style="display:none;">

                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="font-weight-bold text-dark small mb-2">
                                        Origen del número:
                                    </div>

                                    <div class="d-flex flex-column">
                                        <div class="custom-control custom-radio mb-1">
                                            <input type="radio"
                                                   id="usar_reservado"
                                                   name="modo_numeracion"
                                                   value="reservado"
                                                   class="custom-control-input"
                                                   checked>

                                            <label class="custom-control-label small font-weight-bold"
                                                   for="usar_reservado">
                                                Usar folio reservado
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input type="radio"
                                                   id="usar_consecutivo"
                                                   name="modo_numeracion"
                                                   value="consecutivo"
                                                   class="custom-control-input">

                                            <label class="custom-control-label small font-weight-bold"
                                                   for="usar_consecutivo">
                                                Consecutivo automático
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8 pl-md-4"
                                     id="contenedorSelectReservado">
                                    <label class="small mb-1">
                                        Seleccionar folio reservado disponible
                                    </label>

                                    <select id="folio_reservado_select"
                                            class="form-control form-control-sm">
                                    </select>

                                    <small id="textoReservados"
                                           class="text-muted d-block mt-1">
                                    </small>
                                </div>

                                <div class="col-md-8 pl-md-4"
                                     id="contenedorMensajeConsecutivo"
                                     style="display:none;">
                                    <span class="small text-muted">
                                        <i class="fas fa-info-circle text-primary mr-1"></i>
                                        Se asignará de forma automática el siguiente número
                                        consecutivo disponible para esta coordinación.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="mb-1">
                                    Asunto <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="asunto"
                                       class="form-control form-control-sm"
                                       required>
                            </div>
                        </div>
                    </div>

                    {{-- 3. CRONOLOGÍA Y FECHAS --}}
                    <div class="form-block mb-3">
                        <div class="row">

                            <div class="col-md-3">
                                <label class="mb-1" id="labelFechaCrear">
                                    Fecha de recepción <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    name="fecha_recepcion"
                                    class="form-control form-control-sm fecha-oficio"
                                    value="{{ now()->toDateString() }}"
                                    placeholder="dd/mm/yyyy"
                                    autocomplete="off"
                                    required>
                            </div>

                            <div class="col-md-3">
                                <label class="mb-1">
                                    Fecha límite de atención
                                </label>

                                <input type="text"
                                    name="fecha_limite"
                                    class="form-control form-control-sm fecha-oficio"
                                    value=""
                                    placeholder="dd/mm/yyyy"
                                    autocomplete="off">
                            </div>

                            <div class="col-md-3">
                                <label class="mb-1">
                                    ¿Requiere respuesta? <span class="text-danger">*</span>
                                </label>

                                <div class="d-flex pt-1">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio"
                                            id="req_no"
                                            name="requiere_respuesta"
                                            value="0"
                                            class="custom-control-input"
                                            checked
                                            required>

                                        <label class="custom-control-label small"
                                            for="req_no">
                                            No
                                        </label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio"
                                            id="req_si"
                                            name="requiere_respuesta"
                                            value="1"
                                            class="custom-control-input">

                                        <label class="custom-control-label small"
                                            for="req_si">
                                            Sí
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- 4. RELACIÓN Y CONTENIDO --}}
                    <div class="form-block mb-3">
                        <div class="row">
                            <div class="col-md-7">
                                <label class="small font-weight-bold text-dark mb-1">
                                    ¿Inicia nueva petición o responde a otro oficio?
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="d-flex pt-1 mb-2">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio"
                                               id="relacion_nueva"
                                               name="tipo_relacion"
                                               value="nueva"
                                               class="custom-control-input"
                                               required>

                                        <label class="custom-control-label small"
                                               for="relacion_nueva">
                                            Inicia nueva petición
                                        </label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio"
                                               id="relacion_existente"
                                               name="tipo_relacion"
                                               value="relacionado"
                                               class="custom-control-input">

                                        <label class="custom-control-label small"
                                               for="relacion_existente">
                                            Es respuesta a otro
                                        </label>
                                    </div>
                                </div>

                                <div id="bloqueOficioRelacionado"
                                     class="mt-2"
                                     style="display:none;">

                                    <label>
                                        Oficio relacionado <span class="text-danger">*</span>
                                    </label>

                                    <div class="position-relative">
                                        <input type="text"
                                               id="oficio_relacionado"
                                               class="form-control form-control-sm font-mono"
                                               autocomplete="off"
                                               placeholder="Buscar oficio anterior...">

                                        <input type="hidden"
                                               id="respuesta_a_oficio_id"
                                               name="respuesta_a_oficio_id">

                                        <div id="oficiosRelacionadosResultados"
                                             class="oficio-autocomplete">
                                        </div>
                                    </div>

                                    <small class="text-muted d-block mt-1">
                                        Si existe en el sistema, te lo sugerirá automáticamente.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-5 pl-md-4">
                                <label class="mb-1">
                                    Clasificación
                                </label>

                                <div class="d-flex pt-1">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio"
                                               id="sens_no"
                                               name="es_sensible"
                                               value="0"
                                               class="custom-control-input"
                                               checked>

                                        <label class="custom-control-label small"
                                               for="sens_no">
                                            Público
                                        </label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio"
                                               id="sens_si"
                                               name="es_sensible"
                                               value="1"
                                               class="custom-control-input">

                                        <label class="custom-control-label small"
                                               for="sens_si">
                                            Sensible / Reservado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 pt-2 border-top">
                            <div class="col-md-12">
                                <label class="mb-1">
                                    Descripcion
                                </label>

                                <textarea name="descripcion"
                                          class="form-control form-control-sm"
                                          rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-block mb-3">

                            <div class="d-flex align-items-center mb-2">
                                <div class="actor-title mb-0">
                                    Documento en Drive
                                </div>

                                <span id="asteriscoLinkDrive"
                                    class="text-danger font-weight-bold ml-1"
                                    style="display: none;">
                                    *
                                </span>
                            </div>

                            <label class="mb-1">
                                Enlace del documento
                            </label>

                            <input type="url"
                                id="link_drive"
                                name="link_drive"
                                class="form-control form-control-sm font-mono"
                                placeholder="https://drive.google.com/...">

                            <small id="ayudaLinkDrive"
                                class="text-muted d-block mt-1">
                                Opcional para oficios enviados.
                            </small>

                        </div>
                    </div>

                    {{-- 5. ACTORES --}}
                    <div class="form-block mb-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="actor-title mb-2">
                                    Remitente
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="mb-1">Nombre</label>
                                        <input type="text"
                                               name="remitente_nombre"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label class="mb-1">Cargo</label>
                                        <input type="text"
                                               name="remitente_cargo"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="mb-1">Dependencia</label>
                                        <input type="text"
                                               id="remitente_dependencia"
                                               name="remitente_dependencia"
                                               class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 pl-md-4 mt-3 mt-md-0">
                                <div class="actor-title mb-2">
                                    Destinatario
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="mb-1">Nombre</label>
                                        <input type="text"
                                               name="destinatario_nombre"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label class="mb-1">Cargo</label>
                                        <input type="text"
                                               name="destinatario_cargo"
                                               class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="mb-1">Dependencia</label>
                                        <input type="text"
                                               id="destinatario_dependencia"
                                               name="destinatario_dependencia"
                                               class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 pt-3 border-top">

                            <div class="col-md-6" id="bloqueElaborador">
                                <div class="actor-title mb-2">
                                    Elaborador interno
                                </div>

                                <div class="row">
                                    <div class="col-md-7">
                                        <label class="mb-1">Nombre</label>

                                        <input type="text"
                                            name="quien_elabora_nombre"
                                            class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-5">
                                        <label class="mb-1">Cargo</label>

                                        <input type="text"
                                            name="quien_elabora_cargo"
                                            class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row mt-3 pt-3 border-top">

                            <div class="col-md-12">

                                <div class="actor-title mb-2">
                                    Referencia externa
                                </div>

                                <label class="mb-1">
                                    Link de Transparencia
                                </label>

                                <input type="url"
                                    name="link_documento"
                                    class="form-control form-control-sm font-mono"
                                    placeholder="https://...">

                                <small class="text-muted d-block mt-1">
                                    Enlace a la versión pública del oficio.
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light py-2 px-4 justify-content-between">
                    <small class="text-muted">
                        Los campos marcados con
                        <span class="text-danger">*</span>
                        son obligatorios.
                    </small>

                    <div>
                        <button type="button"
                                class="btn btn-secondary btn-sm px-3"
                                data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="btn btn-primary btn-sm px-3 font-weight-bold">
                            Registrar oficio
                        </button>
                    </div>
                </div>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================
    2. MODAL RESERVAR FOLIOS
============================================================ --}}
<div class="modal fade"
     id="modalReservarFolios"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalReservarFoliosLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered" role="document">

        <div class="modal-content border-0 shadow-lg"
             style="border-radius: .4rem; overflow: hidden;">

            <form id="formReservarFolios">

                @csrf

                <input type="hidden"
                       name="fecha"
                       value="{{ now()->toDateString() }}">

                {{-- HEADER --}}
                <div class="modal-header bg-light px-4 py-3 border-bottom">
                    <h5 class="modal-title text-primary font-weight-bold"
                        id="modalReservarFoliosLabel">
                        <i class="fas fa-bookmark mr-2"></i> Reservar folios
                    </h5>

                    <button type="button"
                            class="close text-gray-500"
                            data-dismiss="modal"
                            aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body px-4 py-4 bg-white">

                    <div class="form-block mb-3">

                        <div class="row">

                            {{-- COORDINACIÓN --}}
                            <div class="col-md-8">
                                <label class="mb-1">
                                    Coordinación <span class="text-danger">*</span>
                                </label>

                                <select name="coordinacion_id"
                                        class="form-control form-control-sm"
                                        required>

                                    @foreach(App\Models\Coordinacion::all() as $coord)
                                        <option value="{{ $coord->id }}">
                                            {{ $coord->clave }} - {{ $coord->nombre }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- CANTIDAD --}}
                            <div class="col-md-4">
                                <label class="mb-1">
                                    Cantidad <span class="text-danger">*</span>
                                </label>

                                <input type="number"
                                       name="cantidad"
                                       class="form-control form-control-sm"
                                       min="1"
                                       max="200"
                                       value="10"
                                       required>

                            </div>

                        </div>

                    </div>

                    {{-- RESULTADO --}}
                    <div id="resultadoReserva"
                         class="small">
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light py-2 px-4 justify-content-end">

                    <button type="button"
                            class="btn btn-secondary btn-sm px-3"
                            data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary btn-sm px-3 font-weight-bold">
                        Reservar folios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================
    3. MODAL TURNAR OFICIO  
============================================================ --}}
<style>
    #modalTurnar .coord-item {
        cursor: pointer;
        transition: .15s ease;
    }

    #modalTurnar .coord-item:hover {
        background: #ffffff;
    }

    #modalTurnar .coord-item.selected {
        background: #e4edff;
        border-color: #4e73df !important;
        box-shadow: 0 0 0 1px rgba(78, 115, 223, .15);
    }
</style>

<div class="modal fade"
     id="modalTurnar"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalTurnarLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <div class="modal-content border-0 shadow-lg"
             style="border-radius: .4rem; overflow: hidden;">

            <form id="formTurnar" method="POST">

                @csrf
                
                <input type="hidden" name="returnar" id="turnarEsReturnado" value="0">

                <div class="modal-header bg-light px-4 py-3 border-bottom">

                    <h5 class="modal-title text-primary font-weight-bold"
                        id="modalTurnarLabel">
                        <i class="fas fa-share mr-2"></i>
                        <span id="turnarTitulo">Turnar oficio</span>
                    </h5>

                    <button type="button"
                            class="close text-gray-500"
                            data-dismiss="modal"
                            aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body px-4 py-3 bg-white">

                    <div class="form mb-3">

                        <div class="d-flex align-items-center">

                            <div class="mr-3">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>

                            <div>
                                <small class="text-muted d-block">
                                    Oficio
                                </small>

                                <strong id="turnarNumeroOficio">
                                    —
                                </strong>
                            </div>

                        </div>

                    </div>


                    <div class="form-block mb-3">

                        <label class="mb-2 font-weight-bold">
                            Turnar a
                        </label>

                        <div id="lista-coordinaciones">

                            @foreach(App\Models\Coordinacion::where('activo', true)->get() as $coord)

                                <div
                                    class="coord-item border rounded px-3 py-2 mb-2 d-flex align-items-center justify-content-between"
                                    data-coord="{{ $coord->id }}">

                                    <div>
                                        <strong>
                                            {{ $coord->nombre }}
                                        </strong>
                                    </div>

                                    <div style="width:250px;">

                                        <select
                                            class="form-control form-control-sm participacion-select"
                                            name="participacion[{{ $coord->id }}]"
                                            disabled>

                                            <option value="">
                                                Seleccione participación
                                            </option>

                                            @foreach(App\Models\TipoParticipacion::where('activo', true)->get() as $tipo)

                                                <option value="{{ $tipo->id }}">
                                                    {{ $tipo->nombre }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>


                    <div class="form-block">

                        <label class="mb-1">
                            Observación general
                        </label>

                        <textarea
                            class="form-control form-control-sm"
                            name="observaciones"
                            rows="3"></textarea>

                    </div>

                </div>


                <div class="modal-footer bg-light py-2 px-4 justify-content-end">

                    <button type="button"
                            class="btn btn-secondary btn-sm px-3"
                            data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary btn-sm px-3 font-weight-bold">
                        <i class="fas fa-share mr-1"></i>
                        <span id="turnarBotonTexto">Turnar oficio</span>
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================
    4. MODAL DETALLE DE OFICIO
============================================================ --}}
<div class="modal fade"
     id="modalDetalleOficio"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalDetalleOficioLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">

        <div class="modal-content border-0 shadow-lg"
             style="border-radius: .4rem; overflow: hidden;">

            {{-- =================================================
                 HEADER
            ================================================== --}}
            <div class="modal-header bg-light px-4 py-3 border-bottom">

                <h5 class="modal-title text-primary font-weight-bold mb-0"
                    id="modalDetalleOficioLabel">

                    <i class="fas fa-file-alt mr-2"></i>
                    Detalle del oficio

                </h5>

                <button type="button"
                        class="close text-gray-500 m-0 p-0"
                        data-dismiss="modal"
                        aria-label="Cerrar">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>


            {{-- =================================================
                 BODY
            ================================================== --}}
            <div class="modal-body px-4 py-3 bg-white">

                {{-- TABS --}}
                <ul class="nav nav-tabs mb-3"
                    id="detalleOficioTabs"
                    role="tablist">

                    <li class="nav-item">

                        <a class="nav-link active text-primary font-weight-bold"
                           id="detalle-info-tab"
                           data-toggle="tab"
                           href="#detalle-info"
                           role="tab"
                           aria-controls="detalle-info"
                           aria-selected="true">

                            <i class="fas fa-info-circle mr-1"></i>
                            Información

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link text-muted"
                           id="detalle-relaciones-tab"
                           data-toggle="tab"
                           href="#detalle-relaciones"
                           role="tab"
                           aria-controls="detalle-relaciones"
                           aria-selected="false">

                            <i class="fas fa-project-diagram mr-1"></i>
                            Relaciones

                        </a>

                    </li>

                </ul>


                <div class="tab-content"
                     id="detalleOficioTabContent">


                    {{-- =================================================
                         TAB: INFORMACIÓN
                    ================================================== --}}
                    <div class="tab-pane fade show active"
                         id="detalle-info"
                         role="tabpanel"
                         aria-labelledby="detalle-info-tab">

                        {{-- =================================================
                            RESPONSABLE
                        ================================================== --}}
                        <div id="detalleResponsable"
                             class="mb-3">
                        </div>

                        {{-- =================================================
                            IDENTIDAD Y CLASIFICACIÓN
                        ================================================== --}}
                        <div class="form-block mb-3">

                            <div class="row align-items-center">

                                {{-- NÚMERO --}}
                                <div class="col-12 col-lg-4">

                                    <div class="small text-muted mb-1">
                                        Número de oficio
                                    </div>

                                    <div class="d-flex align-items-baseline flex-wrap">

                                        <span id="detalleNumeroOficio"
                                              class="font-weight-bold text-dark"
                                              style="font-size: 1.25rem;">
                                            —
                                        </span>

                                    </div>

                                </div>


                                {{-- TAGS --}}
                                <div class="col-12 col-lg-5 mt-3 mt-lg-0">

                                    <div class="small text-muted mb-1">
                                        Etiquetas
                                    </div>

                                    <div id="detalleTags">
                                        {{-- JS --}}
                                    </div>

                                </div>


                                {{-- TIPO / ESTADO --}}
                                <div class="col-12 col-lg-3 mt-3 mt-lg-0 text-lg-right">

                                    <div id="detalleTipoOficio"
                                         class="d-inline-block">
                                    </div>

                                    <div class="mt-1">

                                        <a href="javascript:void(0)"
                                           id="btnCorregirIdentidad"
                                           class="small text-primary font-weight-bold">

                                            <i class="fas fa-pen mr-1"></i>
                                            Corregir identidad

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            CONTENIDO DEL OFICIO
                        ================================================== --}}
                        <div class="form-block mb-3">

                            <label class="mb-1">
                                Asunto
                            </label>

                            <input type="text"
                                   id="detalleAsunto"
                                   name="asunto"
                                   class="form-control form-control-sm">

                            <label class="mb-1 mt-3">
                                Descripción
                            </label>

                            <textarea id="detalleDescripcion"
                                      name="descripcion"
                                      class="form-control form-control-sm"
                                      rows="3"></textarea>

                        </div>


                        {{-- =================================================
                            DOCUMENTO
                        ================================================== --}}
                        <div class="form-block mb-3">

                            <label class="mb-1">
                                Documento
                            </label>

                            <div id="detalleDocumentos">
                                {{-- JS inyecta PDF --}}
                            </div>

                            <div class="mt-3 pt-3 border-top">

                                <label class="mb-1">
                                    Enlace del documento en Drive
                                </label>

                                <input
                                    type="url"
                                    id="detalleLinkDrive"
                                    name="link_drive"
                                    class="form-control form-control-sm font-mono"
                                    placeholder="https://drive.google.com/...">

                                <small class="text-muted d-block mt-1">
                                    Ubicación del documento institucional en Drive.
                                </small>

                            </div>

                        </div>


                        {{-- =================================================
                            PROCEDENCIA
                        ================================================== --}}
                        <div class="form-block mb-3">

                            <div class="row">

                                {{-- REMITENTE --}}
                                <div class="col-12 col-lg-6">

                                    <div class="font-weight-bold text-dark mb-2">

                                        <i class="fas fa-paper-plane text-primary mr-1"></i>
                                        Remitente

                                    </div>

                                    <div class="form-row">

                                        <div class="col-12 col-sm-6 mb-2">

                                            <label class="small text-muted mb-1">
                                                Nombre
                                            </label>

                                            <input type="text"
                                                   id="detalleRemitenteNombre"
                                                   name="remitente_nombre"
                                                   class="form-control form-control-sm">

                                        </div>

                                        <div class="col-12 col-sm-6 mb-2">

                                            <label class="small text-muted mb-1">
                                                Cargo
                                            </label>

                                            <input type="text"
                                                   id="detalleRemitenteCargo"
                                                   name="remitente_cargo"
                                                   class="form-control form-control-sm">

                                        </div>

                                        <div class="col-12">

                                            <label class="small text-muted mb-1">
                                                Dependencia
                                            </label>

                                            <input type="text"
                                                   id="detalleRemitenteDependencia"
                                                   name="remitente_dependencia"
                                                   class="form-control form-control-sm">

                                        </div>

                                    </div>

                                </div>


                                {{-- DESTINATARIO --}}
                                <div class="col-12 col-lg-6 mt-4 mt-lg-0">

                                    <div class="font-weight-bold text-dark mb-2">

                                        <i class="fas fa-inbox text-primary mr-1"></i>
                                        Destinatario

                                    </div>

                                    <div class="form-row">

                                        <div class="col-12 col-sm-6 mb-2">

                                            <label class="small text-muted mb-1">
                                                Nombre
                                            </label>

                                            <input type="text"
                                                   id="detalleDestinatarioNombre"
                                                   name="destinatario_nombre"
                                                   class="form-control form-control-sm">

                                        </div>

                                        <div class="col-12 col-sm-6 mb-2">

                                            <label class="small text-muted mb-1">
                                                Cargo
                                            </label>

                                            <input type="text"
                                                   id="detalleDestinatarioCargo"
                                                   name="destinatario_cargo"
                                                   class="form-control form-control-sm">

                                        </div>

                                        <div class="col-12">

                                            <label class="small text-muted mb-1">
                                                Dependencia
                                            </label>

                                            <input type="text"
                                                   id="detalleDestinatarioDependencia"
                                                   name="destinatario_dependencia"
                                                   class="form-control form-control-sm">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- =================================================
                            ELABORADOR INTERNO
                        ================================================== --}}
                        <div id="detalleElaborador"
                            class="form-block mb-3"
                            style="display: none;">

                            <div class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-user-edit text-primary mr-1"></i>
                                Elaborador interno
                            </div>

                            <div class="row">

                                <div class="col-12 col-md-7">
                                    <label class="mb-1">
                                        Nombre
                                    </label>

                                    <input type="text"
                                        id="detalleElaboradorNombre"
                                        name="quien_elabora_nombre"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="col-12 col-md-5 mt-3 mt-md-0">
                                    <label class="mb-1">
                                        Cargo
                                    </label>

                                    <input type="text"
                                        id="detalleElaboradorCargo"
                                        name="quien_elabora_cargo"
                                        class="form-control form-control-sm">
                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            ATENCIÓN
                        ================================================== --}}
                        <div class="form-block mb-3">

                            <div class="small text-muted text-uppercase font-weight-bold mb-3">
                                Atención
                            </div>

                            <div class="row">

                                {{-- FECHA DEL OFICIO --}}
                                <div class="col-12 col-md-6 col-lg-3">

                                    <label class="mb-1">
                                        Fecha del oficio
                                    </label>

                                    <input type="date"
                                           id="detalleFechaOficio"
                                           name="fecha_oficio"
                                           class="form-control form-control-sm">

                                </div>


                                {{-- RECEPCIÓN --}}
                                <div class="col-12 col-md-6 col-lg-3 mt-3 mt-md-0">

                                    <label class="mb-1">
                                        Fecha de recepción
                                    </label>

                                    <input type="date"
                                           id="detalleFechaRecepcion"
                                           name="fecha_recepcion"
                                           class="form-control form-control-sm">

                                </div>


                                {{-- LÍMITE --}}
                                <div class="col-12 col-md-6 col-lg-3 mt-3 mt-lg-0">

                                    <label class="mb-1">
                                        Límite de atención
                                    </label>

                                    <input type="date"
                                           id="detalleFechaLimite"
                                           name="fecha_limite"
                                           class="form-control form-control-sm">

                                </div>


                                {{-- RESPUESTA --}}
                                <div class="col-12 col-md-6 col-lg-3 mt-3 mt-lg-0">

                                    <label class="mb-1">
                                        Requiere respuesta
                                    </label>

                                    <div class="d-flex align-items-center pt-1">

                                        <div class="custom-control custom-radio mr-3">

                                            <input type="radio"
                                                   id="detalleRespuestaNo"
                                                   name="requiere_respuesta"
                                                   value="0"
                                                   class="custom-control-input">

                                            <label class="custom-control-label small"
                                                   for="detalleRespuestaNo">
                                                No
                                            </label>

                                        </div>

                                        <div class="custom-control custom-radio">

                                            <input type="radio"
                                                   id="detalleRespuestaSi"
                                                   name="requiere_respuesta"
                                                   value="1"
                                                   class="custom-control-input">

                                            <label class="custom-control-label small"
                                                   for="detalleRespuestaSi">
                                                Sí
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- CLASIFICACIÓN --}}
                            <div class="mt-3">

                                <label class="mb-1">
                                    Clasificación
                                </label>

                                <div class="d-flex align-items-center">

                                    <div class="custom-control custom-radio mr-3">

                                        <input type="radio"
                                               id="detalleSensibleNo"
                                               name="es_sensible"
                                               value="0"
                                               class="custom-control-input">

                                        <label class="custom-control-label small"
                                               for="detalleSensibleNo">

                                            Público

                                        </label>

                                    </div>

                                    <div class="custom-control custom-radio">

                                        <input type="radio"
                                               id="detalleSensibleSi"
                                               name="es_sensible"
                                               value="1"
                                               class="custom-control-input">

                                        <label class="custom-control-label small"
                                               for="detalleSensibleSi">

                                            <i class="fas fa-lock text-danger mr-1"></i>
                                            Sensible / reservado

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            INFORMACIÓN COMPLEMENTARIA
                        ================================================== --}}
                        <div class="form-block">

                            <label class="mb-1">
                                Enlace de transparencia
                            </label>

                            <input type="text"
                                   id="detalleLinkDocumento"
                                   name="link_documento"
                                   class="form-control form-control-sm font-mono"
                                   placeholder="Sin enlace">

                        </div>

                    </div>


                    {{-- =================================================
                        TAB: RELACIONES
                    ================================================== --}}
                    <div class="tab-pane fade"
                        id="detalle-relaciones"
                        role="tabpanel"
                        aria-labelledby="detalle-relaciones-tab">

                        <div id="contenedorArbolRelaciones"></div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 FOOTER
            ================================================== --}}
            <div class="modal-footer bg-light py-2 px-4 justify-content-between">

                <div>
                    <button type="button"
                            id="btnCancelarOficio"
                            class="btn btn-danger btn-sm px-3"
                            style="display: none;">
                        <i class="fas fa-ban mr-1"></i>
                        Cancelar oficio
                    </button>
                </div>

                <div>
                    <button type="button"
                            id="btnGuardarOficio"
                            class="btn btn-primary btn-sm px-3 font-weight-bold"
                            disabled>
                        <i class="fas fa-save mr-1"></i>
                        Guardar cambios
                    </button>

                    <button type="button"
                            id="btnCerrarDetalle"
                            class="btn btn-secondary btn-sm px-3"
                            data-dismiss="modal">
                        Cerrar
                    </button>
                </div>

            </div>

        </div>

    </div>

</div>


<!-- SCRIPT - Modal 1 -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioReservado = document.getElementById('usar_reservado');
        const radioConsecutivo = document.getElementById('usar_consecutivo');
        const boxReservado = document.getElementById('contenedorSelectReservado');
        const boxConsecutivo = document.getElementById('contenedorMensajeConsecutivo');

        if (radioReservado && radioConsecutivo) {
            radioReservado.addEventListener('change', function () {
                if (this.checked) {
                    boxReservado.style.display = 'block';
                    boxConsecutivo.style.display = 'none';
                }
            });

            radioConsecutivo.addEventListener('change', function () {
                if (this.checked) {
                    boxConsecutivo.style.display = 'block';
                    boxReservado.style.display = 'none';
                }
            });
        }
    });

</script>
