<div class="card border-0 shadow-sm rounded-lg mb-3">
    <div class="card-body p-3">

        <div class="mb-3">
            <div class="card border-0 shadow-sm rounded-lg bg-light">
                <div class="card-body p-2">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>

                        <input
                            type="text"
                            id="inputBuscadorGlobal"
                            class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Escribe para buscar un oficio de manera global..."
                            autocomplete="off"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-end mb-3">
            <div class="col-md-7 mb-2 mb-md-0">
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

            <div class="col-md-5 mb-2 mb-md-0">
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

        <div class="row align-items-end mb-3">

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
        </div>

        <div class="d-flex align-items-center border-top pt-3">

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

            <a
                href="{{ url()->current() }}"
                class="btn btn-light border btn-sm text-secondary ml-2 rounded"
            >
                <i class="fas fa-eraser mr-1"></i>Limpiar filtros
            </a>

            

        </div>

    </div>
</div>