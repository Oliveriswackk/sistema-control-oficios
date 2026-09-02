{{-- ============================================================
    MODAL CREAR OFICIO
============================================================ --}}

<div class="modal fade"
     id="modalCrearOficio"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalCrearOficioLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <div class="modal-content">

            <form id="formCrearOficio">

                {{-- HEADER --}}
                <div class="modal-header py-2">

                    <h5 class="modal-title text-primary font-weight-bold"
                        id="modalCrearOficioLabel">

                        <i class="fas fa-file-alt mr-1"></i>
                        Registrar oficio

                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Cerrar">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>


                {{-- BODY --}}
                <div class="modal-body px-4 py-3">

                    {{-- =================================================
                        TIPO (INTOCABLE - ARRIBA)
                    ================================================== --}}
                    <div class="tipo-oficio-selector mb-4">

                        <button type="button"
                                class="tipo-oficio-card active"
                                id="btnTipoEnviado"
                                onclick="setTipoOficio('1', 'enviado')">

                            <i class="fas fa-paper-plane"></i>
                            <span>Enviado</span>

                        </button>


                        <button type="button"
                                class="tipo-oficio-card"
                                id="btnTipoRecibido"
                                onclick="setTipoOficio('2', 'recibido')">

                            <i class="fas fa-inbox"></i>
                            <span>Recibido</span>

                        </button>


                        <button type="button"
                                class="tipo-oficio-card"
                                id="btnTipoRecibidoCPC"
                                onclick="setTipoOficio('3', 'recibido_cpc')">

                            <i class="fas fa-shield-alt"></i>
                            <span>Recibido CPC</span>

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


                    {{-- =================================================
                        INFORMACIÓN PRINCIPAL
                    ================================================== --}}
                    <div class="form-block mb-3">

                        <div class="row">

                            <div class="col-md-4" id="bloqueCoordinacion">

                                <label>Coordinación <span class="text-danger">*</span></label>

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

                                <label id="labelNumeroOficio">
                                    Número <span class="text-danger">*</span>
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

                                <label>
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
                             class="numeracion-box mt-2"
                             style="display:none;">

                            <div class="row align-items-center">

                                <div class="col-md-4">

                                    <div class="custom-control custom-radio">

                                        <input type="radio"
                                               id="usar_reservado"
                                               name="modo_numeracion"
                                               value="reservado"
                                               class="custom-control-input">

                                        <label class="custom-control-label"
                                               for="usar_reservado">

                                            Usar folio reservado

                                        </label>

                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <select id="folio_reservado_select"
                                            class="form-control form-control-sm">

                                    </select>

                                </div>


                                <div class="col-md-4">

                                    <div class="custom-control custom-radio">

                                        <input type="radio"
                                               id="usar_consecutivo"
                                               name="modo_numeracion"
                                               value="consecutivo"
                                               class="custom-control-input">

                                        <label class="custom-control-label"
                                               for="usar_consecutivo">

                                            Consecutivo automático

                                        </label>

                                    </div>

                                </div>

                            </div>

                            <small id="textoReservados"
                                   class="text-muted">
                            </small>

                        </div>

                        <div class="row mt-2">

                            <div class="col-md-12">

                                <label>
                                    Asunto <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="asunto"
                                       class="form-control form-control-sm"
                                       required>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        FECHAS Y CONTROL
                    ================================================== --}}
                    <div class="form-block mb-3">

                        <div class="row">

                            <div class="col-md-4">

                                <label id="labelFechaCrear">
                                    Fecha de envío <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="fecha_recepcion"
                                       class="form-control form-control-sm"
                                       value="{{ now()->toDateString() }}"
                                       required>

                            </div>


                            <div class="col-md-4">

                                <label>
                                    Fecha límite
                                </label>

                                <input type="date"
                                       name="fecha_limite"
                                       class="form-control form-control-sm">

                            </div>


                            <div class="col-md-4">

                                <label>
                                    Requiere respuesta <span class="text-danger">*</span>
                                </label>

                                <div class="d-flex pt-1">

                                    <div class="custom-control custom-radio mr-4">

                                        <input type="radio"
                                               id="req_no"
                                               name="requiere_respuesta"
                                               value="0"
                                               class="custom-control-input"
                                               required>

                                        <label class="custom-control-label"
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

                                        <label class="custom-control-label"
                                               for="req_si">

                                            Sí

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        ACTORES (REMITENTE / DESTINATARIO)
                    ================================================== --}}
                    <div class="form-block mb-3">

                        <div class="row">

                            {{-- REMITENTE --}}
                            <div class="col-md-6 pr-md-3">

                                <div class="actor-title">
                                    Remitente
                                </div>


                                <div class="row">

                                    <div class="col-md-6">

                                        <label>Nombre</label>

                                        <input type="text"
                                               name="remitente_nombre"
                                               class="form-control form-control-sm">

                                    </div>


                                    <div class="col-md-6">

                                        <label>Cargo</label>

                                        <input type="text"
                                               name="remitente_cargo"
                                               class="form-control form-control-sm">

                                    </div>


                                    <div class="col-md-12 mt-2">

                                        <label>Dependencia</label>

                                        <input type="text"
                                               id="remitente_dependencia"
                                               name="remitente_dependencia"
                                               class="form-control form-control-sm">

                                    </div>

                                </div>

                            </div>


                            {{-- DESTINATARIO --}}
                            <div class="col-md-6 pl-md-3 mt-3 mt-md-0 border-left-subtle">

                                <div class="actor-title">
                                    Destinatario
                                </div>


                                <div class="row">

                                    <div class="col-md-6">

                                        <label>Nombre</label>

                                        <input type="text"
                                               name="destinatario_nombre"
                                               class="form-control form-control-sm">

                                    </div>


                                    <div class="col-md-6">

                                        <label>Cargo</label>

                                        <input type="text"
                                               name="destinatario_cargo"
                                               class="form-control form-control-sm">

                                    </div>


                                    <div class="col-md-12 mt-2">

                                        <label>Dependencia</label>

                                        <input type="text"
                                               id="destinatario_dependencia"
                                               name="destinatario_dependencia"
                                               class="form-control form-control-sm">

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        ELABORADOR + DOCUMENTO DIGITAL
                    ================================================== --}}
                    <div class="form-block mb-3">

                        <div class="row">

                            <div class="col-md-6 pr-md-3" id="bloqueElaborador">

                                <div class="actor-title">
                                    Elaborador
                                </div>


                                <div class="row">

                                    <div class="col-md-7">

                                        <label>Nombre</label>

                                        <input type="text"
                                               name="quien_elabora_nombre"
                                               class="form-control form-control-sm">

                                    </div>


                                    <div class="col-md-5">

                                        <label>Cargo</label>

                                        <input type="text"
                                               name="quien_elabora_cargo"
                                               class="form-control form-control-sm">

                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6 pl-md-3 mt-3 mt-md-0 border-left-subtle">

                                <div class="actor-title">
                                    Documento digital
                                </div>

                                <label>Link</label>

                                <input type="text"
                                       name="link_documento"
                                       class="form-control form-control-sm font-mono"
                                       placeholder="https://...">

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        CONTENIDO + RELACIÓN + SENSIBLE
                    ================================================== --}}
                    <div class="form-block mb-0">

                        <div class="row">

                            <div class="col-md-7">

                                <label>Descripción del contenido</label>

                                <textarea name="descripcion"
                                          class="form-control form-control-sm"
                                          rows="2"></textarea>

                            </div>


                            <div class="col-md-5 mt-3 mtCreo-md-0">

                                <label>
                                    Oficio relacionado
                                </label>

                                <div class="position-relative">

                                    <input type="text"
                                           id="oficio_relacionado"
                                           class="form-control form-control-sm font-mono"
                                           autocomplete="off"
                                           placeholder="Número de oficio">

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


                        <div class="row mt-3 pt-2 border-top">

                            <div class="col-md-6">

                                <label class="mb-1">
                                    Documento sensible
                                </label>

                                <div class="d-flex pt-1">

                                    <div class="custom-control custom-radio mr-4">

                                        <input type="radio"
                                               id="sens_no"
                                               name="es_sensible"
                                               value="0"
                                               class="custom-control-input"
                                               checked>

                                        <label class="custom-control-label"
                                               for="sens_no">

                                            No

                                        </label>

                                    </div>


                                    <div class="custom-control custom-radio">

                                        <input type="radio"
                                               id="sens_si"
                                               name="es_sensible"
                                               value="1"
                                               class="custom-control-input">

                                        <label class="custom-control-label"
                                               for="sens_si">

                                            Sí

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer py-2 justify-content-between">

                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        La información puede completarse posteriormente.
                    </small>

                    <div>

                        <button type="button"
                                class="btn btn-secondary btn-sm"
                                data-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="submit"
                                class="btn btn-primary btn-sm">

                            <i class="fas fa-save mr-1"></i>
                            Registrar oficio

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<style>

    #modalCrearOficio .modal-dialog {
        max-width: 880px;
    }

    #modalCrearOficio .modal-content {
        border: 0;
        border-radius: .4rem;
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .12);
    }

    #modalCrearOficio .modal-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }

    #modalCrearOficio .modal-body {
        background: #fff;
        max-height: 75vh;
        overflow-y: auto;
    }

    #modalCrearOficio .modal-footer {
        background: #f8f9fc;
        border-top: 1px solid #e3e6f0;
    }


    /* ============================================================
        SELECTOR DE TIPO
    ============================================================ */

    .tipo-oficio-selector {
        display: flex;
        gap: .5rem;
    }

    .tipo-oficio-card {
        flex: 1;
        height: 48px;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
        background: #fff;
        color: #5a5c69;
        font-size: .9rem;
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
        box-shadow: 0 .1rem .3rem rgba(78, 115, 223, .25);
    }


    /* ============================================================
        BLOQUES LIMPIOS 
    ============================================================ */

    .form-block {
        padding: .8rem 1rem;
        background: #f8f9fc;
        border-radius: .35rem;
        border: 1px solid #eaecf4;
    }

    .actor-title {
        margin-bottom: .5rem;
        color: #4e73df;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .border-left-subtle {
        border-left: 1px solid #e3e6f0;
    }

    @media (max-width: 767.98px) {
        .border-left-subtle {
            border-left: 0;
            border-top: 1px solid #e3e6f0;
            padding-top: .8rem;
            margin-top: .8rem;
        }
    }


    /* ============================================================
        CAMPOS
    ============================================================ */

    #modalCrearOficio label {
        margin-bottom: .2rem;
        color: #8f8f8f;
        font-size: .85rem;
        font-weight: 600;
    }

    #modalCrearOficio .form-control {
        min-height: 34px;
        font-size: .85rem;
        border-color: #d1d3e2;
    }

    #modalCrearOficio .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.1rem rgba(78, 115, 223, .25);
    }


    /* ============================================================
        NUMERACIÓN Y AUTOCOMPLETE
    ============================================================ */

    .numeracion-box {
        padding: .5rem .75rem;
        border: 1px solid #d1d3e2;
        border-radius: .25rem;
        background: #fff;
    }

    .folio-dropdown-box,
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


    @media (max-width: 767.98px) {
        #modalCrearOficio .modal-dialog {
            max-width: 95%;
        }

        .tipo-oficio-selector {
            flex-direction: column;
        }

        .tipo-oficio-card {
            height: 42px;
        }
    }

</style>