{{-- TABLA OFICIOS --}}
<div class="table-responsive">

    <table class="table table-bordered" id="{{ $tableId ?? 'tabla-oficios' }}">

        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Asunto</th>
                <th>Tags</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
                @foreach($oficios as $oficio)

                        <tr>
                            <td>{{ $oficio->id }}</td>

                            <td>{{ $oficio->numero_oficio }}</td>

                            <td>{{ $oficio->asunto }}</td>

                            <td>
                                @foreach($oficio->tags as $tag)
                                    {{ $tag->nombre }}
                                @endforeach
                            </td>

                            <td>{{ $oficio->estado->nombre }}</td>

                            <td>
                                <button class="btn btn-primary btn-ver-oficio" data-id="{{ $oficio->id }}">
                                    Ver
                                </button>

                                @if($oficio->estado_id != 5)
                                    <button class="btn btn-sm btn-warning"
                                            onclick="abrirTurnar({{ $oficio->id }})">
                                        Turnar
                                    </button>
                                @else
                                    <span class="badge badge-secondary">
                                        Cerrado
                                    </span>
                                @endif
                                
                            </td>
                            
                        </tr>

                @endforeach
        </tbody>

    </table>

</div>