<div class="card border-0 shadow-sm rounded-lg mb-3">
    <div class="card-body p-3">

        <form id="formFiltrosOficios" method="GET" action="{{ route('dashboard') }}">

            <input type="hidden" name="vista" value="{{ request('vista', 'enviados') }}">

            <div class="mb-3">
                <div class="card border-0 rounded-lg bg-light mb-0">
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

                <div class="col-md-6 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">
                        Coordinación origen
                    </label>

                    <select name="coordinacion_origen_id" class="form-control filtro-auto">
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

                <div class="col-md-6 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">
                        Estado
                    </label>

                    <select name="estado_id" class="form-control filtro-auto">
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

            </div>

            <div class="row align-items-end mb-3">

                <div class="col-md-6 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">
                        Remitente
                    </label>

                    <input
                        type="text"
                        name="remitente_dependencia"
                        class="form-control filtro-auto-texto"
                        value="{{ request('remitente_dependencia') }}"
                        placeholder="Buscar remitente..."
                        autocomplete="off"
                    >
                </div>

                <div class="col-md-6 mb-2 mb-md-0">

                    <label class="small text-muted font-weight-bold mb-1">
                        Periodo
                    </label>

                    <div class="d-flex align-items-center">
                        <input
                            type="date"
                            name="fecha_desde"
                            class="form-control filtro-auto mr-2"
                            value="{{ request()->has('fecha_desde') ? request('fecha_desde') : now()->startOfYear()->format('Y-m-d') }}"
                        >

                        <span class="text-muted small mr-2">
                            a
                        </span>

                        <input
                            type="date"
                            name="fecha_hasta"
                            class="form-control filtro-auto"
                            value="{{ request()->has('fecha_hasta') ? request('fecha_hasta') : now()->format('Y-m-d') }}"
                        >
                    </div>

                </div>

            </div>

            <div class="d-flex align-items-center border-top pt-3">

                @can('create', App\Models\Oficio::class)
                    <button
                        type="button"
                        class="btn btn-primary btn-sm rounded shadow-sm px-3 font-weight-bold"
                        data-toggle="modal"
                        data-target="#modalCrearOficio"
                    >
                        <i class="fas fa-plus mr-1"></i>
                        Registrar oficio
                    </button>
                @endcan

                <a
                    href="{{ route('dashboard', ['vista' => request('vista', 'enviados')]) }}"
                    class="btn btn-light border btn-sm text-secondary ml-2 rounded"
                >
                    <i class="fas fa-eraser mr-1"></i>
                    Limpiar filtros
                </a>

            </div>

        </form>

    </div>
</div>