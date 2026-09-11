<tr style="cursor: pointer;" onclick="Oficios.open({{ $oficio->id }}, false)">

    <td class="align-middle pl-3 text-muted small">
        {{ $oficio->id }}
    </td>

    <td class="align-middle">
        <div class="font-weight-bold text-dark" style="font-size: .9rem;">
            {{ $oficio->numero_oficio }}
        </div>
    </td>

    <td class="align-middle">
        <div class="text-dark" style="font-size: .9rem; line-height: 1.4;">
            {{ $oficio->asunto }}
        </div>
    </td>

    <td class="align-middle">
        @forelse($oficio->tags as $tag)
            <span
                class="text-muted small"
                style="display: inline-block; margin-right: .4rem;"
            >
                {{ $tag->nombre }}
            </span>
        @empty
            <span class="text-muted small">—</span>
        @endforelse
    </td>

    <td class="align-middle">
        @if($oficio->estado_id == \App\Models\EstadoOficio::CERRADO)

            <span
                class="font-weight-bold"
                style="
                    color: #858796;
                    font-size: .82rem;
                "
            >
                Cerrado
            </span>

        @elseif($oficio->estado_id == \App\Models\EstadoOficio::CANCELADO)

            <span
                class="font-weight-bold"
                style="
                    color: #e74a3b;
                    font-size: .82rem;
                "
            >
                {{ $oficio->estado->nombre }}
            </span>

        @else

            <span
                class="font-weight-bold"
                style="
                    color: #4e73df;
                    font-size: .82rem;
                "
            >
                {{ $oficio->estado->nombre }}
            </span>

        @endif
    </td>

    <td class="align-middle text-right pr-3" onclick="event.stopPropagation();">

        <div class="btn-group" role="group">

            <button
                type="button"
                class="btn btn-sm btn-light text-primary border-0 rounded mr-1 px-2"
                data-id="{{ $oficio->id }}"
                title="Ver oficio"
                onclick="Oficios.open({{ $oficio->id }}, false)"
            >
                <i class="fas fa-eye fa-xs"></i>
            </button>

            @if(
                $oficio->estado_id != \App\Models\EstadoOficio::CERRADO &&
                $oficio->estado_id != \App\Models\EstadoOficio::CANCELADO
            )

                @if(
                    $oficio->tipo_oficio_id == 2 &&
                    (
                        auth()->user()->hasRole('admin') ||
                        auth()->user()->hasPermission('puede_turnar')
                    )
                )

                    <button
                        type="button"
                        class="btn btn-sm btn-light text-info border-0 rounded px-2"
                        title="Turnar oficio"
                        onclick="abrirTurnar(
                            {{ $oficio->id }},
                            '{{ $oficio->numero_oficio }}',
                            '{{ route('oficios.turnar', $oficio->id) }}'
                        )"
                    >
                        <i class="fas fa-share fa-xs"></i>
                    </button>

                @endif

            @endif

        </div>

    </td>

</tr>