<div class="card border-0 shadow-sm rounded-lg mb-3">
    <div class="card-body p-3">

        <div class="row align-items-end mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Número de Oficio
                </label>
                <input
                    type="text"
                    name="numero_oficio"
                    class="form-control"
                    value="{{ request('numero_oficio') }}"
                    placeholder="Buscar por número..."
                >
            </div>

            <div class="col-md-6 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Asunto
                </label>
                <input
                    type="text"
                    name="asunto"
                    class="form-control"
                    value="{{ request('asunto') }}"
                    placeholder="Buscar por asunto..."
                >
            </div>
        </div>

        <div class="row align-items-end mb-3">
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Tipo de oficio
                </label>
                <select name="tipo_oficio_id" class="form-control">
                    <option value="">Todos</option>

                    @foreach($tiposOficio as $tipo)
                        <option
                            value="{{ $tipo->id }}"
                            {{ request('tipo_oficio_id') == $tipo->id ? 'selected' : '' }}
                        >
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Estado
                </label>
                <select name="estado_id" class="form-control">
                    <option value="">Todos</option>

                    @foreach($estados as $estado)
                        <option
                            value="{{ $estado->id }}"
                            {{ request('estado_id') == $estado->id ? 'selected' : '' }}
                        >
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Remitente
                </label>
                <input
                    type="text"
                    name="remitente_dependencia"
                    class="form-control"
                    value="{{ request('remitente_dependencia') }}"
                    placeholder="Buscar remitente..."
                >
            </div>

            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small text-muted font-weight-bold mb-1">
                    Coordinación origen
                </label>
                <select name="coordinacion_origen_id" class="form-control">
                    <option value="">Todas</option>

                    @foreach($coordinaciones as $coordinacion)
                        <option
                            value="{{ $coordinacion->id }}"
                            {{ request('coordinacion_origen_id') == $coordinacion->id ? 'selected' : '' }}
                        >
                            {{ $coordinacion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex align-items-center border-top pt-3">

            <button
                type="submit"
                id="btnBuscarOficios"
                class="btn btn-primary btn-sm rounded shadow-sm px-3"
            >
                <i class="fas fa-search mr-1"></i>
                Buscar
            </button>

            <a
                href="{{ url()->current() }}"
                class="btn btn-light border btn-sm text-secondary ml-2 rounded"
            >
                Limpiar filtros
            </a>

            @can('create', App\Models\Oficio::class)
                <button
                    type="button"
                    class="btn btn-primary btn-sm rounded shadow-sm px-3 font-weight-bold ml-2"
                    data-toggle="modal"
                    data-target="#modalCrearOficio"
                >
                    <i class="fas fa-plus mr-1"></i>
                    Registrar oficio
                </button>
            @endcan

        </div>

    </div>
</div>