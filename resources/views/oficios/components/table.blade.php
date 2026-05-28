<div class="card shadow mb-4">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>No. Oficio</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($oficios as $oficio)
                        <tr>
                            <td>{{ $oficio->numero_oficio }}</td>
                            <td>{{ $oficio->asunto }}</td>
                            <td>{{ $oficio->estado->nombre }}</td>

                            <td>

                                <a href="{{ route('oficios.show', $oficio) }}"
                                   class="btn btn-info btn-sm">
                                    Ver
                                </a>

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#turnarModal"
                                        data-id="{{ $oficio->id }}">
                                    Turnar
                                </button>

                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>