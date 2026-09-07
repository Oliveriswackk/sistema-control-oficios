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

                                <input type="date"
                                       name="fecha_oficio"
                                       class="form-control form-control-sm"
                                       value="{{ now()->toDateString() }}"
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

                                <input type="date"
                                       name="fecha_recepcion"
                                       class="form-control form-control-sm"
                                       value="{{ now()->toDateString() }}"
                                       required>
                            </div>

                            <div class="col-md-3">
                                <label class="mb-1">
                                    Fecha límite de atención
                                </label>

                                <input type="date"
                                       name="fecha_limite"
                                       class="form-control form-control-sm">
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

                            <div class="col-md-6 pl-md-4 mt-3 mt-md-0">
                                <div class="actor-title mb-2">
                                    Transparencia
                                </div>

                                <label class="mb-1">
                                    Link de referencia
                                </label>

                                <input type="text"
                                       name="link_documento"
                                       class="form-control form-control-sm font-mono"
                                       placeholder="https://...">
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

                <div class="modal-header bg-light px-4 py-3 border-bottom">

                    <h5 class="modal-title text-primary font-weight-bold"
                        id="modalTurnarLabel">
                        <i class="fas fa-share mr-2"></i>
                        Turnar oficio
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
                        Turnar oficio
                    </button>

                </div>

            </form>

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

    function setTipoOficio(valorId, tipoStr) {
        const radioTipo = document.querySelector(
            'input[name="tipo_oficio_id"][value="' + valorId + '"]'
        );

        if (radioTipo) {
            radioTipo.checked = true;
        }

        document
            .querySelectorAll('.tipo-oficio-card')
            .forEach(function (card) {
                card.classList.remove('active');
            });

        if (valorId === '1') {
            document.getElementById('btnTipoEnviado').classList.add('active');
        } else if (valorId === '2') {
            document.getElementById('btnTipoRecibido').classList.add('active');
        } else if (valorId === '3') {
            document.getElementById('btnTipoRecibidoCPC').classList.add('active');
        }

        const contenedorCoordinacion =
            document.getElementById('contenedorCoordinacion');

        const selectCoordinacion =
            document.getElementById('selectCoordinacion');

        const inputNumeroOficio =
            document.getElementById('numero_oficio');

        const opcionesNumeracion =
            document.getElementById('opcionesNumeracion');

        const bloqueElaborador =
            document.getElementById('bloqueElaborador');

        const remitenteDependencia =
            document.getElementById('remitente_dependencia');

        const destinatarioDependencia =
            document.getElementById('destinatario_dependencia');

        if (valorId === '1') {
            contenedorCoordinacion.style.display = 'block';

            selectCoordinacion.setAttribute(
                'required',
                'required'
            );

            inputNumeroOficio.setAttribute(
                'readonly',
                'readonly'
            );

            bloqueElaborador.style.display = 'block';

            if (remitenteDependencia) {
                remitenteDependencia.value =
                    'Secretaría Ejecutiva del Sistema Estatal Anticorrupción';
            }

            if (destinatarioDependencia) {
                destinatarioDependencia.value = '';
            }

            document.getElementById('labelFechaCrear').innerHTML =
                'Fecha de envío <span class="text-danger">*</span>';

            if (typeof generarNumeroOficio === 'function') {
                generarNumeroOficio();
            }
        } else {
            contenedorCoordinacion.style.display = 'none';

            selectCoordinacion.removeAttribute(
                'required'
            );

            selectCoordinacion.value = '';

            inputNumeroOficio.removeAttribute(
                'readonly'
            );

            inputNumeroOficio.value = '';

            opcionesNumeracion.style.display = 'none';

            bloqueElaborador.style.display = 'none';

            if (remitenteDependencia) {
                remitenteDependencia.value = '';
            }

            if (destinatarioDependencia) {
                destinatarioDependencia.value =
                    'Secretaría Ejecutiva del Sistema Estatal Anticorrupción';
            }

            document.getElementById('labelFechaCrear').innerHTML =
                'Fecha de recepción <span class="text-danger">*</span>';
        }
    }

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

    document.addEventListener('DOMContentLoaded', function () {
        setTipoOficio('1', 'enviado');
    });
</script>

<!-- SCRIPT - Modal 3 -->
<script>

</script>
