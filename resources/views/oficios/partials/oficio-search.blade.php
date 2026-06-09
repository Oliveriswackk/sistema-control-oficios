<div class="card shadow mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <label>No. Oficio</label>
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
                <label>Destinatario</label>
                <input
                    type="text"
                    name="destinatario_dependencia"
                    class="form-control"
                    value="{{ request('destinatario_dependencia') }}"
                >
            </div>

            <div class="col-md-4">
                <label>Coordinación origen</label>
                <input
                    type="text"
                    name="coordinacion_origen"
                    class="form-control"
                    value="{{ request('coordinacion_origen') }}"
                >
            </div>

        </div>

        <div class="mt-3">

            <button class="btn btn-primary">
                Buscar
            </button>

            <a href="{{ url()->current() }}" class="btn btn-secondary">
                Limpiar
            </a>

        </div>

    </div>

</div>