<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio turnado</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; color:#2d3748; -webkit-font-smoothing:antialiased;">

    <div style="max-width:580px; margin:0 auto; padding:28px 16px;">

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0, 0, 0, 0.03);">

            <div style="padding:28px;">

                <!-- Pill badge contextual (Reemplaza al banner cuadrado) -->
                <div style="margin-bottom:20px;">
                    <span style="display:inline-block; padding:4px 10px; background:#f0f4ff; color:#4e73df; font-size:11px; font-weight:700; border-radius:20px; letter-spacing:0.5px; text-transform:uppercase;">
                        Notificación Interna • SESEA
                    </span>
                </div>

                <!-- Saludo -->
                <p style="margin:0 0 10px 0; font-size:16px; line-height:1.5; color:#1a202c; font-weight:700;">
                    Un gusto saludarte, {{ $turnado->usuario->name }}
                </p>

                <p style="margin:0 0 20px 0; font-size:15px; line-height:1.5; color:#4a5568;">
                    Te compartimos un oficio que corresponde a tu coordinación:
                </p>

                <!-- Bloque Datos Clave: Oficio + Asunto -->
                <div style="margin-bottom:20px; padding:18px; background:#f8fafc; border-left:4px solid #4e73df; border-radius:0 8px 8px 0;">
                    
                    <div style="margin-bottom:12px;">
                        <span style="display:block; font-size:11px; font-weight:700; color:#718096; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                            Número de oficio
                        </span>
                        <span style="font-size:16px; font-weight:700; color:#1a202c;">
                            {{ $turnado->oficio->numero_oficio }}
                        </span>
                    </div>

                    <div>
                        <span style="display:block; font-size:11px; font-weight:700; color:#718096; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                            Asunto
                        </span>
                        <span style="font-size:14px; font-weight:500; line-height:1.5; color:#2d3748;">
                            {{ $turnado->oficio->asunto }}
                        </span>
                    </div>

                </div>

                <!-- Fecha Límite destacada (si existe) -->
                @if($turnado->oficio->fecha_limite)
                    <div style="margin-bottom:20px; padding:12px 16px; background:#fff7ed; border:1px solid #ffedd5; border-radius:8px;">
                        <span style="font-size:12px; font-weight:700; color:#c2410c; text-transform:uppercase; letter-spacing:0.5px;">
                            Fecha límite:
                        </span>
                        <span style="font-size:14px; font-weight:700; color:#9a3412; margin-left:6px;">
                            {{ \Carbon\Carbon::parse($turnado->oficio->fecha_limite)->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    </div>
                @endif

                <!-- Observaciones / Nota de Recepción -->
                @if($turnado->observaciones)
                    <div style="margin-bottom:20px; padding:14px; background:#fffdf5; border:1px solid #fef3c7; border-radius:8px;">
                        <span style="display:block; font-size:11px; font-weight:700; color:#b45309; text-transform:uppercase; margin-bottom:4px; letter-spacing:0.5px;">
                            Nota desde Recepción
                        </span>
                        <span style="font-size:14px; line-height:1.5; color:#78350f; font-weight:500;">
                            {{ $turnado->observaciones }}
                        </span>
                    </div>
                @endif

                <!-- Botones de Acción principales con azul #4e73df -->
                <div style="margin:24px 0;">
                    @if($esResponsable)
                        <a href="{{ URL::temporarySignedRoute('turnados.atender.correo', now()->addDays(7), ['turnado' => $turnado->id]) }}"
                           style="display:inline-block; padding:11px 18px; background:#4e73df; color:#ffffff; text-decoration:none; border-radius:6px; font-size:14px; font-weight:700; margin-right:8px; margin-bottom:6px;">
                            Marcar como atendido
                        </a>
                    @endif

                    <a href="{{ route('home', ['buscar' => $turnado->oficio->numero_oficio]) }}"
                       style="display:inline-block; padding:10px 16px; background:#ffffff; color:#334155; text-decoration:none; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; font-weight:700; margin-bottom:6px;">
                        Ver en SCO
                    </a>
                </div>

                <!-- Documentos anexos / Drive de forma fluida -->
                <p style="margin:0 0 24px 0; font-size:14px; line-height:1.5; color:#4a5568;">
                    📎 Te adjuntamos el archivo en PDF en este correo
                    @if($turnado->oficio->link_drive)
                        o si lo prefieres, puedes <a href="{{ $turnado->oficio->link_drive }}" style="color:#4e73df; font-weight:600; text-decoration:underline;" target="_blank">consultarlo directamente en Google Drive</a>.
                    @else
                        .
                    @endif
                </p>

                <!-- Asignación de Coordinaciones -->
                @php
                    $responsables = $turnado->oficio->turnados->where('tipo_participacion_id', 1);
                    $ccp = $turnado->oficio->turnados->where('tipo_participacion_id', 2);
                @endphp

                @if($responsables->count() || $ccp->count())
                    <div style="margin-bottom:24px; padding-top:16px; border-top:1px solid #edf2f7;">
                        @if($responsables->count())
                            <div style="font-size:11px; font-weight:700; color:#718096; text-transform:uppercase; margin-bottom:6px; letter-spacing:0.5px;">
                                Coordinación responsable
                            </div>
                            @foreach($responsables as $responsable)
                                <div style="font-size:13px; font-weight:600; color:#2d3748; margin-bottom:4px;">
                                    • {{ $responsable->coordinacion->nombre }}
                                </div>
                            @endforeach
                        @endif

                        @if($ccp->count())
                            <div style="font-size:11px; font-weight:700; color:#718096; text-transform:uppercase; margin-top:12px; margin-bottom:6px; letter-spacing:0.5px;">
                                Con copia para (C.C.P.)
                            </div>
                            @foreach($ccp as $copia)
                                <div style="font-size:13px; color:#4a5568; margin-bottom:4px;">
                                    • {{ $copia->coordinacion->nombre }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif

                <!-- Cierre con firma institucional e imagen ASCII -->
                <div style="border-top:1px solid #edf2f7; padding-top:20px; margin-top:24px;">

                    <p style="margin:0 0 2px 0; font-size:13px; font-weight:600; color:#4a5568;">
                        Enviado por el equipo de <strong>Recepción</strong>.
                    </p>

                    <p style="margin:0; font-size:12px; color:#a0aec0;">
                        Control y seguimiento gestionado vía SCO.
                    </p>

                    <div style="margin-top:16px; text-align:right;">
                        <div style="display:inline-block; text-align:left; font-family:monospace; font-size:10px; line-height:1.05; color:#cbd5e1; white-space:pre;">
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

    </div>

</body>
</html>