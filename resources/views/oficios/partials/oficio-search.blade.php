{{-- FILTROS BUSQUEDA --}}

<div class="card shadow mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <label>Número de Oficio</label>
                <input
                    type="text"
                    name="numero_oficio"
                    class="form-control"
                    value="{{ request('numero_oficio') }}"
                >
            </div>

            <div class="col-md-6">
                <label>Asunto</label>
                <input
                    type="text"
                    name="asunto"
                    class="form-control"
                    value="{{ request('asunto') }}"
                >
            </div>

        </div>

        <div class="row mt-2">

            <div class="col-md-6">

                <label>Tipo de oficio</label>

                <select
                    name="tipo_oficio_id"
                    class="form-control">

                    <option value="">
                        Todos
                    </option>

                    @foreach($tiposOficio as $tipo)

                        <option
                            value="{{ $tipo->id }}"
                            {{ request('tipo_oficio_id') == $tipo->id ? 'selected' : '' }}>

                            {{ $tipo->nombre }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-md-6">

            <label>Estado</label>

            <select
                name="estado_id"
                class="form-control">

                <option value="">
                    Todos
                </option>

                @foreach($estados as $estado)

                    <option
                        value="{{ $estado->id }}"
                        {{ request('estado_id') == $estado->id ? 'selected' : '' }}>

                        {{ $estado->nombre }}

                    </option>

                @endforeach

            </select>

        </div>

        </div>

        <div class="row mt-2">

            <div class="col-md-4">
                <label>Remitente</label>
                <input
                    type="text"
                    name="remitente_dependencia"
                    class="form-control"
                    value="{{ request('remitente_dependencia') }}"
                >
            </div>

            <div class="col-md-4">
                <label>Coordinación origen</label>
                <select
                    name="coordinacion_origen_id"
                    class="form-control">

                    <option value="">
                        Todas
                    </option>

                    @foreach($coordinaciones as $coordinacion)

                        <option
                            value="{{ $coordinacion->id }}"
                            {{ request('coordinacion_origen_id') == $coordinacion->id ? 'selected' : '' }}>

                            {{ $coordinacion->nombre }}

                        </option>

                    @endforeach

                </select>
            </div>

        </div>

        <div class="mt-3">

            <button type="submit" id="btnBuscarOficios" class="btn btn-primary">
                Buscar
            </button>

            <a href="{{ url()->current() }}" class="btn btn-secondary">
                Limpiar filtros
            </a>

            @can('create', App\Models\Oficio::class)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCrearOficio">
                    Registrar Oficio
                </button>
            @endcan

        </div>

    </div>

</div>