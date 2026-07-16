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

        <button
            class="btn btn-primary btn-ver-oficio"
            data-id="{{ $oficio->id }}">
            Ver
        </button>


        @if($oficio->estado_id == \App\Models\EstadoOficio::CERRADO)

            <span class="badge badge-secondary">
                Cerrado
            </span>


        @elseif($oficio->estado_id == \App\Models\EstadoOficio::CANCELADO)

            <span class="badge" style="background-color: {{ $oficio->estado->color }}; color:white;"
>
                {{ $oficio->estado->nombre }}
            </span>


        @else

            @if(
                $oficio->tipo_oficio_id == 2 &&
                (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasPermission('puede_turnar')
                )
            )

                <button 
                    class="btn btn-sm btn-info"
                    onclick="abrirTurnar({{ $oficio->id }})">
                    Turnar
                </button>

            @endif

        @endif

    </td>

</tr>