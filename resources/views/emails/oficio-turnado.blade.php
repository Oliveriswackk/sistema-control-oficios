<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio turnado</title>
</head>

<body style="margin:0; padding:0; background:#f5f6f8; font-family:'Segoe UI', Arial, sans-serif; color:#2d3748;">

    <div style="max-width:600px; margin:0 auto; padding:32px 16px;">

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">

            {{-- Encabezado sutil --}}
            <div style="padding:20px 28px; background:#f8fafc; border-bottom:1px solid #edf2f7; display:flex; align-items:center;">
                <span style="font-size:13px; font-weight:700; color:#4e73df; letter-spacing:0.5px; text-transform:uppercase;">
                    Mensajería interna • SESEA
                </span>
            </div>

            {{-- Contenido --}}
            <div style="padding:28px;">

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.5; color:#1a202c; font-weight:600;">
                    Buen día, {{ $turnado->usuario->name }} 
                </p>

                <p style="margin:0 0 20px 0; font-size:15px; line-height:1.6; color:#4a5568;">
                    Te compartimos un nuevo oficio que acaba de ingresar a Recepción para tu área:
                </p>

                {{-- Datos del oficio (Tarjeta destacada) --}}
                <div style="margin-bottom:24px; padding:16px 20px; background:#f8fafc; border-left:4px solid #4e73df; border-radius:4px;">
                    <div style="font-size:16px; font-weight:bold; color:#2d3748; margin-bottom:6px;">
                        {{ $turnado->oficio->numero_oficio }}
                    </div>
                    <div style="font-size:14px; line-height:1.5; color:#4a5568;">
                        {{ $turnado->oficio->asunto }}
                    </div>
                </div>

                {{-- Fecha límite --}}
                @if($turnado->oficio->fecha_limite)
                    <div style="margin-bottom:24px; padding:14px 16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px;">
                         <div style="font-size:12px; font-weight:bold; color:#718096; text-transform:uppercase; margin-bottom:4px; letter-spacing:0.5px;">
                            Fecha límite
                        </div>
                        <div style="font-size:14px; color:#2d3748;">
                            {{ \Carbon\Carbon::parse($turnado->oficio->fecha_limite)->translatedFormat('d \d\e F \d\e Y') }}
                         </div>
                    </div>
                @endif

                {{-- Nota de Recepción --}}
                @if($turnado->observaciones)
                    <div style="margin-bottom:24px; padding:14px; background:#fffaf0; border:1px solid #feebc8; border-radius:6px;">
                        <div style="font-size:12px; font-weight:bold; color:#c05621; text-transform:uppercase; margin-bottom:4px; letter-spacing:0.5px;">
                            Nota desde Recepción
                        </div>
                        <div style="font-size:14px; line-height:1.5; color:#744210;">
                            {{ $turnado->observaciones }}
                        </div>
                    </div>
                @endif

                {{-- Áreas involucradas --}}
                <div style="margin-bottom:24px; padding-top:8px;">
                    @php
                        $responsables = $turnado->oficio->turnados->where('tipo_participacion_id', 1);
                        $ccp = $turnado->oficio->turnados->where('tipo_participacion_id', 2);
                    @endphp

                    @if($responsables->count())
                        <div style="font-size:12px; font-weight:bold; color:#a0aec0; text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">
                            Coordinación responsable
                        </div>
                        @foreach($responsables as $responsable)
                            <div style="font-size:14px; color:#2d3748; margin-bottom:4px;">
                                • {{ $responsable->coordinacion->nombre }}
                            </div>
                        @endforeach
                    @endif

                    @if($ccp->count())
                        <div style="font-size:12px; font-weight:bold; color:#a0aec0; text-transform:uppercase; margin-top:14px; margin-bottom:8px; letter-spacing:0.5px;">
                            Con copia para (C.C.P.)
                        </div>
                        @foreach($ccp as $copia)
                            <div style="font-size:14px; color:#718096; margin-bottom:4px;">
                                • {{ $copia->coordinacion->nombre }}
                            </div>
                        @endforeach
                    @endif
                </div>

                <p style="margin:0 0 20px 0; font-size:14px; color:#718096; italic;">
                    📎 Adjunto a este correo encuentras el PDF del documento.
                </p>

                {{-- Acciones --}}
                <div style="margin:28px 0 20px 0; display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="#" style="display:inline-block; padding:12px 20px; background:#4e73df; color:#ffffff; text-decoration:none; border-radius:6px; font-size:14px; font-weight:bold;">
                        Marcar como atendido
                    </a>
                    
                    <a
                        href="{{ route('home', ['buscar' => $turnado->oficio->numero_oficio]) }}"
                        style="display:inline-block; padding:11px 18px; background:#ffffff; color:#4a5568; text-decoration:none; border:1px solid #cbd5e0; border-radius:6px; font-size:14px; font-weight:600;"
                    >
                        Ver en SCO
                    </a>
                </div>

                {{-- Pie humano --}}
                <div style="border-top:1px solid #edf2f7; padding-top:20px; margin-top:28px;">
                    <p style="margin:0 0 4px 0; font-size:13px; color:#718096;">
                        Enviado por el equipo de <strong>Recepción</strong>.
                    </p>
                    <p style="margin:0; font-size:12px; color:#a0aec0;">
                        Control y seguimiento gestionado vía SCO.
                    </p>
                </div>

                {{-- Pájaro Mensajero --}}
                <div style="margin-top:24px; text-align:right;">
                    <div style="display:inline-block; text-align:left; font-family:monospace; font-size:11px; line-height:1.05; color:#a0aec0; white-space:pre;">
                 __________
                / ___  ___ \
               / / @ \/ @ \ \
               \ \___/\___/ /\
                \____\/____/||
                /     /\\\\\//
                |     |\\\\\\
                \      \\\\\\
                \______/\\\\
                    *||_||*
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>