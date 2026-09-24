<tr style="cursor: pointer;" onclick="Oficios.open({{ $oficio->id }}, false)">

    {{-- ID --}}
    <td class="align-middle pl-3 text-muted small">
        {{ $oficio->id }}
    </td>


    {{-- Número de oficio --}}
    <td class="align-middle">
        <div
            class="font-weight-bold text-dark"
            style="font-size:.9rem;"
        >
            {{ $oficio->numero_oficio }}
        </div>
    </td>


    {{-- Asunto --}}
    <td class="align-middle">
        <div
            class="text-dark"
            style="font-size:.9rem; line-height:1.4;"
        >
            {{ $oficio->asunto }}
        </div>
    </td>


    {{-- Etiquetas --}}
    <td class="align-middle">

        @forelse($oficio->tags as $tag)

            <span
                class="text-muted small"
                style="
                    display:inline-block;
                    margin-right:.4rem;
                "
            >
                {{ $tag->nombre }}
            </span>

        @empty

            <span class="text-muted small">—</span>

        @endforelse

    </td>


    {{-- Estado del oficio --}}
    <td class="align-middle">

        @if($oficio->estado_id == \App\Models\EstadoOficio::CERRADO)

            <span
                class="font-weight-bold"
                style="
                    color:#858796;
                    font-size:.82rem;
                "
            >
                Cerrado
            </span>

        @elseif($oficio->estado_id == \App\Models\EstadoOficio::CANCELADO)

            <span
                class="font-weight-bold"
                style="
                    color:#e74a3b;
                    font-size:.82rem;
                "
            >
                {{ $oficio->estado->nombre }}
            </span>

        @else

            <span
                class="font-weight-bold"
                style="
                    color:#4e73df;
                    font-size:.82rem;
                "
            >
                {{ $oficio->estado->nombre }}
            </span>

        @endif

    </td>


    {{-- Acciones --}}
    <td
        class="align-middle text-right pr-3"
        onclick="event.stopPropagation();"
    >

        <div class="btn-group" role="group">


            {{-- Ver --}}
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


                {{-- Turnar --}}
                @if(
                    $oficio->tipo_oficio_id == 2 &&
                    (
                        auth()->user()->hasRole('admin') ||
                        auth()->user()->hasPermission('puede_turnar')
                    )
                )

                    <button
                        type="button"
                        class="btn btn-sm btn-light text-info border-0 rounded mr-1 px-2"
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


                {{-- Notificaciones --}}
                @if(
                    $oficio->tipo_oficio_id == 2 &&
                    $oficio->turnados->count() > 0
                )

                    @php

                        $notificaciones = $oficio->turnados
                            ->filter(fn($turnado) => $turnado->notificacion);

                        $enviadas = $notificaciones
                            ->filter(fn($turnado) =>
                                $turnado->notificacion->estado ===
                                \App\Models\NotificacionTurnado::ESTADO_EXITOSO
                            )
                            ->count();

                        $totalNotificaciones = $notificaciones->count();

                        $fallidas = $notificaciones
                            ->filter(fn($turnado) =>
                                in_array(
                                    $turnado->notificacion->estado,
                                    [
                                        \App\Models\NotificacionTurnado::ESTADO_FALLIDO,
                                        \App\Models\NotificacionTurnado::ESTADO_NO_ENTREGADO,
                                    ],
                                    true
                                )
                            );

                    @endphp


                    {{-- Botón + menú de notificaciones --}}
                    <div
                        class="d-inline-block position-relative"
                        onclick="event.stopPropagation();"
                    >

                        {{-- Indicador --}}
                        <button
                            type="button"
                            class="btn btn-sm btn-light text-secondary border-0 rounded px-2"
                            title="Notificaciones por correo"
                            onclick="
                                event.stopPropagation();
                                toggleNotificaciones({{ $oficio->id }})
                            "
                        >
                            <i class="fas fa-envelope fa-xs"></i>

                            <span style="font-size:.82rem;">
                                {{ $enviadas }}/{{ $totalNotificaciones }}
                            </span>
                        </button>


                        {{-- Menú --}}
                        <div
                            id="notificaciones-{{ $oficio->id }}"
                            class="notificaciones-menu shadow-sm"
                            style="
                                display:none;
                                position:absolute;
                                right:0;
                                top:calc(100% + 4px);
                                z-index:1050;
                                width:320px;
                                background:#fff;
                                border:1px solid #e3e6f0;
                                border-radius:.4rem;
                                overflow:hidden;
                            "
                            onclick="event.stopPropagation();"
                        >


                            {{-- Turnados --}}
                            @foreach($notificaciones as $turnado)

                                @php

                                    $estado =
                                        $turnado->notificacion->estado;

                                    $esExitosa =
                                        $estado ===
                                        \App\Models\NotificacionTurnado::ESTADO_EXITOSO;

                                    $esFallida =
                                        in_array(
                                            $estado,
                                            [
                                                \App\Models\NotificacionTurnado::ESTADO_FALLIDO,
                                                \App\Models\NotificacionTurnado::ESTADO_NO_ENTREGADO,
                                            ],
                                            true
                                        );

                                    $esManual =
                                        $estado ===
                                        \App\Models\NotificacionTurnado::ESTADO_ENVIADO_MANUAL;

                                    $color =
                                        $esExitosa
                                            ? '#28a745'
                                            : ($esFallida
                                                ? '#dc3545'
                                                : '#f6c23e');

                                    $textoEstado =
                                        $esExitosa
                                            ? 'Correo enviado y sin rebote'
                                            : ($esFallida
                                                ? (
                                                    $estado === \App\Models\NotificacionTurnado::ESTADO_NO_ENTREGADO
                                                        ? 'Correo no entregado'
                                                        : 'No se pudo enviar'
                                                )
                                                : 'Comunicado externamente');

                                @endphp


                                <div
                                    class="px-3 py-2 d-flex align-items-center"
                                    style="
                                        border-bottom:1px solid #f1f1f1;
                                    "
                                >

                                    {{-- Nombre + responsabilidad --}}
                                    <div
                                        style="
                                            flex:1;
                                            min-width:0;
                                        "
                                    >

                                        <div
                                            class="text-dark font-weight-bold"
                                            style="
                                                font-size:.8rem;
                                                line-height:1.2;
                                            "
                                        >
                                            {{ $turnado->usuario->name }}
                                        </div>

                                        <div
                                            class="text-muted"
                                            style="
                                                font-size:.68rem;
                                            "
                                        >
                                            {{ $turnado->tipoParticipacion->nombre ?? 'Turnado' }}
                                        </div>

                                    </div>


                                    {{-- Punto de estado --}}
                                    <span
                                        title="{{ $textoEstado }}"
                                        style="
                                            width:9px;
                                            height:9px;
                                            border-radius:50%;
                                            background:{{ $color }};
                                            display:block;
                                            flex:none;
                                            margin-left:12px;
                                        "
                                    ></span>

                                </div>

                            @endforeach


                            {{-- Acciones para fallidas --}}
                            @if($fallidas->count() > 0)

                                <div class="px-3 py-2">

                                    <div
                                        class="text-muted mb-2"
                                        style="
                                            font-size:.68rem;
                                        "
                                    >
                                        {{ $fallidas->count() }}
                                        notificación(es) no enviada(s)
                                    </div>


                                    <div class="d-flex">

                                        {{-- Reenviar --}}
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger mr-2"
                                            onclick="
                                                event.stopPropagation();
                                                reintentarNotificaciones(
                                                    @json($fallidas->pluck('notificacion.id')->values())
                                                )
                                            "
                                        >
                                            <i
                                                class="fas fa-redo-alt mr-1"
                                            ></i>
                                            Reenviar
                                        </button>


                                        {{-- Copiar link --}}
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick='
                                                event.stopPropagation();
                                                copiarLinkNotificaciones(
                                                    @json($fallidas->pluck("notificacion.id")->values()),
                                                    @json($oficio->link_drive)
                                                )
                                            '
                                        >
                                            <i
                                                class="fas fa-link mr-1"
                                            ></i>
                                            Copiar link
                                        </button>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

            @endif

        </div>

    </td>

</tr>