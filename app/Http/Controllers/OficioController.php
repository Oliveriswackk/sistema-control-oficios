<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\OficioService;
use App\Mail\OficioTurnadoMail;
use App\Models\Oficio;
use App\Models\Turnado;
use App\Models\EstadoOficio;
use App\Models\TipoOficio;
use App\Models\Coordinacion;
use App\Models\User;
use App\Models\FolioReservado;
use App\Models\Tag;
use App\Models\NotificacionTurnado;
use App\Models\TipoParticipacion;
use App\Jobs\DetectarRebotesJob;
use Carbon\Carbon;

class OficioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $oficios = Oficio::with(['estado', 'turnados'])
            ->latest()
            ->paginate(20);

        return view('oficios.index-ui', compact('oficios'));
    }


    public function indexUi()
    {
        $oficios = Oficio::with('estado')->latest()->get();

        return view('oficios.index-ui', compact('oficios'));
    }


    // Filtros
    private function aplicarFiltros($query, Request $request)
    {
        if ($request->filled('busqueda')) {
            $terminos = preg_split(
                '/\s+/',
                trim($request->busqueda)
            );

            foreach ($terminos as $termino) {
                $like = "%{$termino}%";

                $query->where(function ($q) use ($like) {
                    $q->where('numero_oficio', 'like', $like)
                        ->orWhere('asunto', 'like', $like)
                        ->orWhere('descripcion', 'like', $like)
                        ->orWhere('remitente_nombre', 'like', $like)
                        ->orWhere('remitente_cargo', 'like', $like)
                        ->orWhere('remitente_dependencia', 'like', $like)
                        ->orWhere('destinatario_nombre', 'like', $like)
                        ->orWhere('destinatario_cargo', 'like', $like)
                        ->orWhere('destinatario_dependencia', 'like', $like)
                        ->orWhere('quien_elabora_nombre', 'like', $like)
                        ->orWhere('quien_elabora_cargo', 'like', $like)
                        ->orWhereHas('tags', function ($tagQuery) use ($like) {
                            $tagQuery->where(
                                'nombre',
                                'like',
                                $like
                            );
                        });
                });
            }
        }

        if ($request->filled('estado_id')) {
            $query->where(
                'estado_id',
                $request->estado_id
            );
        }

        if ($request->filled('coordinacion_origen_id')) {
            $query->where(
                'coordinacion_origen_id',
                $request->coordinacion_origen_id
            );
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate(
                'fecha_oficio',
                '>=',
                $request->fecha_desde
            );
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate(
                'fecha_oficio',
                '<=',
                $request->fecha_hasta
            );
        }

        return $query;
    }


    public function dashboard(Request $request)
    {
        $request->validate([
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $fechaMinima = Oficio::min('fecha_oficio');
        $fechaMaxima = Oficio::max('fecha_oficio');

        if (!$request->filled('fecha_desde')) {
            $request->merge([
                'fecha_desde' => $fechaMinima,
            ]);
        }

        if (!$request->filled('fecha_hasta')) {
            $request->merge([
                'fecha_hasta' => $fechaMaxima,
            ]);
        }

        $vista = $request->input('vista', 'enviados');

        $puedeVerTodos =
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('coordinador');

        if ($vista === 'todos' && !$puedeVerTodos) {
            $vista = 'enviados';
        }

        $tipoVista = [
            'enviados' => 'enviado',
            'recibidos' => 'recibido',
            'recibidos_cpc' => 'recibido_cpc',
        ];

        $oficiosQuery = Oficio::with([
            'estado',
            'turnados.usuario',
            'turnados.tipoParticipacion',
            'turnados.estadoTurnado',
            'turnados.notificacion',
            'tags',
        ]);

        if ($vista !== 'todos') {
            $tipoOficioId = TipoOficio::where(
                'clave',
                $tipoVista[$vista]
            )->value('id');

            $oficiosQuery->where(
                'tipo_oficio_id',
                $tipoOficioId
            );
        }

        $this->aplicarFiltros(
            $oficiosQuery,
            $request
        );

        $oficios = $oficiosQuery
            ->latest()
            ->get();

        if ($request->ajax()) {
            return $oficios
                ->map(function ($oficio) {
                    return view(
                        'oficios.partials.oficio-row',
                        compact('oficio')
                    )->render();
                })
                ->implode('');
        }

        $oficiosRelacionables = Oficio::select(
            'id',
            'numero_oficio',
            'asunto'
        )
        ->where(
            'estado_id',
            '!=',
            EstadoOficio::CANCELADO
        )
        ->latest()
        ->get();

        $coordinaciones = Coordinacion::where(
            'activo',
            true
        )
        ->orderBy('nombre')
        ->get();

        $estados = EstadoOficio::all();

        $tiposOficio = TipoOficio::all();

        return view(
            'dashboard',
            compact(
                'oficios',
                'oficiosRelacionables',
                'coordinaciones',
                'estados',
                'tiposOficio',
                'vista',
                'puedeVerTodos',
                'fechaMinima',
                'fechaMaxima'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME (BANDEJA TRABAJO)
    |--------------------------------------------------------------------------
    */
    public function home()
    {
        $turnados = Turnado::with('oficio')
            ->where('usuario_id', Auth::id())
            ->whereHas('estadoTurnado', function ($query) {
                $query->whereIn('clave', [
                    \App\Models\EstadoTurnado::ACTIVO,
                    \App\Models\EstadoTurnado::EN_ATENCION,
                    \App\Models\EstadoTurnado::COMUNICADO_EXTERNAMENTE,
                ]);
            })
            ->latest()
            ->get();

        $oficiosRelacionables = Oficio::select(
            'id',
            'numero_oficio',
            'asunto'
        )
        ->where(
            'estado_id',
            '!=',
            EstadoOficio::CANCELADO
        )
        ->latest()
        ->get();

        $coordinaciones = Coordinacion::where(
            'activo',
            true
        )
        ->orderBy('nombre')
        ->get();

        $estados = EstadoOficio::all();

        $tiposOficio = TipoOficio::all();

        return view('home', compact(
            'turnados',
            'oficiosRelacionables',
            'coordinaciones',
            'estados',
            'tiposOficio'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR FORM 
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', Oficio::class);

        $foliosReservados = \App\Models\FolioReservado::where('estado', 'reservado')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'foliosReservados' => $foliosReservados,
        ]);
    }


    // CONSECUTIVOS
    public function proximoConsecutivo(Request $request, OficioService $oficioService)
    {

        if (!$request->coordinacion_id) {

            return response()->json([
                'error' => 'Coordinación requerida'
            ], 422);

        }

        return response()->json(

            $oficioService->obtenerSiguienteNumero(
                (int) $request->coordinacion_id,
                now()->toDateString()
            )

        );

    }


    public function coordinadorDeCoordinacion(Coordinacion $coordinacion)
    {
        $coordinador = $coordinacion->users()
            ->whereHas('roles', function ($query) {
                $query->where('clave', 'coordinador');
            })
            ->first();

        if (!$coordinador) {
            return response()->json([
                'coordinador' => null,
            ]);
        }

        return response()->json([
            'coordinador' => [
                'nombre' => $coordinador->name,
                'cargo' => $coordinador->cargo,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR NUEVO OFICIO
    |--------------------------------------------------------------------------
    */
    public function store(StoreOficioRequest $request, OficioService $oficioService)
    {
        $this->authorize('create', Oficio::class);

        $esRespuesta = $request->respuesta_a_oficio_id != 0;

        if ($esRespuesta) {
            $padre = Oficio::find($request->respuesta_a_oficio_id);

            if (!$padre) {
                return back()->with('error', 'Oficio padre no existe');
            }
        }

        $respuestaId = $request->input('respuesta_a_oficio_id');
        $tipo = (int) $request->tipo_oficio_id;

        if ($tipo === 1) {

            if ($request->filled('folio_reservado_id')) {

                $folio = \App\Models\FolioReservado::where(
                    'id',
                    $request->folio_reservado_id
                )
                ->where('estado', 'reservado')
                ->lockForUpdate()
                ->first();

                if (!$folio) {
                    return back()->with(
                        'error',
                        'El folio reservado no está disponible'
                    );
                }

                $coordinacionId = $folio->coordinacion_id;
                $numero = $folio->numero;
                $anio = $folio->anio;

                $coordinacion = \App\Models\Coordinacion::findOrFail(
                    $coordinacionId
                );

                $numeroOficio =
                    "SESEA-{$coordinacion->clave}-" .
                    str_pad($numero, 3, '0', STR_PAD_LEFT) .
                    "-{$anio}";

                $consecutivo = $numero;

                $folio->update([
                    'estado' => 'usado',
                    'numero_oficio' => $numeroOficio,
                ]);

            } else {

                $datos = $oficioService->consumirSiguienteNumero(
                    $request->coordinacion_origen_id,
                    $request->fecha_oficio
                );

                $numeroOficio = $datos['numero_oficio'];
                $consecutivo = $datos['consecutivo'];

                $coordinacionId = $request->coordinacion_origen_id;
            }

        } else {

            $numeroOficio = $request->numero_oficio;
            $consecutivo = 0;
            $coordinacionId = null;
        }

        $oficio = Oficio::create([

            'uuid' => (string) Str::uuid(),

            'numero_oficio' => $numeroOficio,
            'consecutivo' => $consecutivo,

            'estado_id' => EstadoOficio::REGISTRADO,

            'tipo_oficio_id' => $request->tipo_oficio_id,

            'asunto' => $request->asunto,
            'descripcion' => $request->descripcion,

            'fecha_oficio' => $request->fecha_oficio,
            'fecha_recepcion' => $request->fecha_recepcion,
            'fecha_limite' => $request->fecha_limite,

            'requiere_respuesta' => $request->requiere_respuesta ?? 0,

            'respuesta_a_oficio_id' =>
                $respuestaId && $respuestaId != 0
                    ? $respuestaId
                    : null,

            'es_sensible' => $request->es_sensible ?? 0,

            'remitente_nombre' => $request->remitente_nombre,
            'remitente_cargo' => $request->remitente_cargo,
            'remitente_dependencia' => $request->remitente_dependencia,

            'destinatario_nombre' => $request->destinatario_nombre,
            'destinatario_cargo' => $request->destinatario_cargo,
            'destinatario_dependencia' => $request->destinatario_dependencia,

            'quien_elabora_nombre' => $request->quien_elabora_nombre,
            'quien_elabora_cargo' => $request->quien_elabora_cargo,

            'link_documento' => $request->link_documento,
            'link_drive' => $request->link_drive,

            'usuario_registro_id' => auth()->id(),
            'responsable_inicial_id' => auth()->id(),

            'coordinacion_origen_id' => $coordinacionId,
        ]);

        if ($request->filled('tags')) {

            $tagsIds = [];

            foreach (explode(',', $request->tags) as $tag) {

                $tag = trim($tag);

                if (!$tag) {
                    continue;
                }

                $tagModel = Tag::firstOrCreate([
                    'nombre' => mb_strtolower($tag),
                ]);

                $tagsIds[] = $tagModel->id;
            }

            $oficio->tags()->sync($tagsIds);
        }

        $oficio->registrarEvento(
            'oficio_creado',
            'Oficio creado desde interfaz web'
        );

        if (
            (int) $oficio->tipo_oficio_id === 1 &&
            $oficio->requiere_respuesta
        ) {

            $oficio->update([
                'estado_id' => EstadoOficio::EN_SEGUIMIENTO,
            ]);

            $oficio->historial()->create([
                'usuario_id' => auth()->id(),
                'accion' => 'oficio_en_seguimiento',
                'descripcion' =>
                    'El oficio enviado requiere respuesta y queda en seguimiento.',
                'estado_anterior_id' => EstadoOficio::REGISTRADO,
                'estado_nuevo_id' => EstadoOficio::EN_SEGUIMIENTO,
            ]);
        }

        if ($oficio->respuesta_a_oficio_id) {

            $padre = Oficio::find(
                $oficio->respuesta_a_oficio_id
            );

            if ($padre && $padre->requiere_respuesta) {

                $pendientes = $padre->turnados()
                    ->where('tipo_participacion_id', 1)
                    ->whereHas('estadoTurnado', function ($query) {
                        $query->whereNotIn('clave', [
                            \App\Models\EstadoTurnado::ATENDIDO,
                            \App\Models\EstadoTurnado::COMUNICADO_EXTERNAMENTE,
                            \App\Models\EstadoTurnado::CERRADO,
                        ]);
                    })
                    ->exists();

                if (!$pendientes) {

                    $estadoAnterior = $padre->estado_id;

                    $padre->update([
                        'estado_id' => EstadoOficio::CERRADO,
                        'cerrado_en' => now(),
                    ]);

                    $padre->historial()->create([
                        'usuario_id' => auth()->id(),
                        'accion' => 'oficio_cerrado_automaticamente',
                        'descripcion' =>
                            'El oficio fue cerrado automáticamente al registrar una respuesta y quedar atendidos todos los responsables.',
                        'estado_anterior_id' => $estadoAnterior,
                        'estado_nuevo_id' => EstadoOficio::CERRADO,
                    ]);
                }
            }
        }

        if ($request->ajax()) {

            $oficio->load('estado', 'tags');

            return response()->json([
                'row' => view(
                    'oficios.partials.oficio-row',
                    compact('oficio')
                )->render(),
            ]);
        }

        $vistaDestino = match ((int) $oficio->tipo_oficio_id) {
            1 => 'enviados',
            2 => 'recibidos',
            3 => 'recibidos_cpc',
            default => 'enviados',
        };

        return redirect()
            ->route('dashboard', [
                'vista' => $vistaDestino,
            ])
            ->with(
                'success',
                'Oficio creado correctamente'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RESERVAR NO. OFICIOS
    |--------------------------------------------------------------------------
    */
    public function reservarFolios(Request $request, OficioService $service)
    {
        $this->authorize('create', Oficio::class);

        $request->validate([
            'coordinacion_id' => 'required|exists:coordinaciones,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|integer|min:1|max:200',
        ]);

        return response()->json(
            $service->reservarNumeros(
                $request->coordinacion_id,
                $request->fecha,
                $request->cantidad
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Oficio $oficio)
    {
        $this->authorize('update', $oficio);

        if (in_array($oficio->estado_id, [
            EstadoOficio::CERRADO,
            EstadoOficio::CANCELADO,
        ])) {
            return response()->json([
                'success' => false,
                'message' =>
                    'El oficio está cerrado o cancelado. No puede modificarse.',
            ], 422);
        }

        $request->validate([
            'link_drive' => [
                'nullable',
                'string',
                'required_if:tipo_oficio_id,2,3',
            ],
        ]);

        $oficio->update([
            'asunto' => $request->input('asunto'),
            'descripcion' => $request->input('descripcion'),
            'fecha_oficio' => $request->input('fecha_oficio'),
            'fecha_recepcion' => $request->input('fecha_recepcion'),
            'fecha_limite' => $request->input('fecha_limite'),
            'requiere_respuesta' =>
                $request->boolean('requiere_respuesta'),
            'es_sensible' =>
                $request->boolean('es_sensible'),

            'remitente_nombre' =>
                $request->input('remitente_nombre'),
            'remitente_cargo' =>
                $request->input('remitente_cargo'),
            'remitente_dependencia' =>
                $request->input('remitente_dependencia'),

            'destinatario_nombre' =>
                $request->input('destinatario_nombre'),
            'destinatario_cargo' =>
                $request->input('destinatario_cargo'),
            'destinatario_dependencia' =>
                $request->input('destinatario_dependencia'),

            'quien_elabora_nombre' =>
                $request->input('quien_elabora_nombre'),
            'quien_elabora_cargo' =>
                $request->input('quien_elabora_cargo'),

            'link_documento' =>
                $request->input('link_documento'),
            'link_drive' =>
                $request->input('link_drive'),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_editado',
            'descripcion' =>
                'Se actualizaron datos generales del oficio',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Oficio actualizado correctamente',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLES
    |--------------------------------------------------------------------------
    */
    public function detalle(Oficio $oficio)
    {
        $editable = Gate::allows('update', $oficio);

        $oficio->load([
            'archivos.versiones',
            'estado',
            'responsableActual.usuario',
            'responsableActual.coordinacion'
        ]);

        $oficiosRelacionables = Oficio::where('id', '!=', $oficio->id)
            ->orderBy('numero_oficio')
            ->get();

        return view(
            'oficios.modals.detalle',
            compact(
                'oficio',
                'editable',
                'oficiosRelacionables'
            )
        );
    }

    
    /*
    |--------------------------------------------------------------------------
    | VER
    |--------------------------------------------------------------------------
    */
    public function show(Oficio $oficio)
    {
        return response()->json(
            $oficio->load([
                'estado',
                'tipo',
                'turnados.usuario',
                'historial.usuario',
            ])
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TURNAR
    |--------------------------------------------------------------------------
    */
    public function turnar(Request $request, Oficio $oficio)
    {
        if ((int) $oficio->tipo_oficio_id === 1) {
            return back()->with(
                'error',
                'Los oficios enviados no se pueden turnar.'
            );
        }

        if (in_array($oficio->estado_id, [
            EstadoOficio::CERRADO,
            EstadoOficio::CANCELADO,
        ])) {
            return back()->with(
                'error',
                'No se puede turnar un oficio cerrado o cancelado.'
            );
        }

        $esReturnado = $request->boolean('returnar');

        $participaciones = $request->input('participacion', []);

        if (!is_array($participaciones) || empty($participaciones)) {
            return back()->with(
                'error',
                'Debe seleccionar al menos una coordinación.'
            );
        }

        $participacionesValidas = [];
        $responsableEncontrado = false;

        foreach ($participaciones as $coordId => $tipoId) {

            if (empty($tipoId)) {
                continue;
            }

            $coordinacion = Coordinacion::where('id', $coordId)
                ->where('activo', true)
                ->first();

            if (!$coordinacion) {
                return back()->with(
                    'error',
                    'Una de las coordinaciones seleccionadas no está disponible.'
                );
            }

            $tipoParticipacion = TipoParticipacion::where(
                'id',
                $tipoId
            )
            ->where('activo', true)
            ->first();

            if (!$tipoParticipacion) {
                return back()->with(
                    'error',
                    'Uno de los tipos de participación seleccionados no es válido.'
                );
            }

            $coordinador = $coordinacion->users()
                ->whereHas('roles', function ($query) {
                    $query->where('clave', 'coordinador');
                })
                ->first();

            if (!$coordinador) {
                return back()->with(
                    'error',
                    "La coordinación {$coordinacion->nombre} no tiene un coordinador asignado."
                );
            }

            if ((int) $tipoId === 1) {
                $responsableEncontrado = true;
            }

            $participacionesValidas[] = [
                'coordinacion_id' => $coordinacion->id,
                'tipo_participacion_id' => (int) $tipoId,
                'coordinador' => $coordinador,
            ];
        }

        if (!$responsableEncontrado) {
            return back()->with(
                'error',
                'Debe existir al menos un Responsable Operativo.'
            );
        }

        $archivoActual = $oficio->archivos()
            ->whereHas('versiones', function ($query) {
                $query->where('es_actual', true);
            })
            ->with([
                'versiones' => function ($query) {
                    $query->where('es_actual', true);
                }
            ])
            ->first();

        if (!$archivoActual || $archivoActual->versiones->isEmpty()) {
            return back()->with(
                'error',
                'No se puede turnar el oficio porque aún no tiene un PDF adjunto.'
            );
        }

        $versionActual = $archivoActual->versiones->first();

        if (!Storage::disk('public')->exists($versionActual->ruta)) {
            return back()->with(
                'error',
                'No se puede turnar el oficio porque el PDF adjunto no está disponible.'
            );
        }

        if (
            in_array((int) $oficio->tipo_oficio_id, [2, 3]) &&
            (!is_string($oficio->link_drive) || trim($oficio->link_drive) === '')
        ) {
            return back()->with(
                'error',
                'No se puede turnar el oficio porque falta el enlace de Drive.'
            );
        }

        /* ESTADOS DE TURNADO */

        $estadoActivo = \App\Models\EstadoTurnado::where(
            'clave',
            \App\Models\EstadoTurnado::ACTIVO
        )->firstOrFail();

        $estadoEnAtencion = \App\Models\EstadoTurnado::where(
            'clave',
            \App\Models\EstadoTurnado::EN_ATENCION
        )->firstOrFail();

        $estadoCerrado = \App\Models\EstadoTurnado::where(
            'clave',
            \App\Models\EstadoTurnado::CERRADO
        )->firstOrFail();

        /* CREAR TURNADOS */

        $turnados = DB::transaction(function () use (
            $oficio,
            $participacionesValidas,
            $request,
            $esReturnado,
            $estadoActivo,
            $estadoEnAtencion,
            $estadoCerrado
        ) {

            /*
            |--------------------------------------------------------------------------
            | RETURNAR
            |--------------------------------------------------------------------------
            |
            | Si es returnado, se cierran únicamente los turnados que
            | actualmente están vigentes.
            |
            | Los registros NO se eliminan. Permanecen como historial.
            |
            */

            if ($esReturnado) {

                $turnadosActuales = $oficio->turnados()
                    ->whereIn('estado_turnado_id', [
                        $estadoActivo->id,
                        $estadoEnAtencion->id,
                    ])
                    ->get();

                foreach ($turnadosActuales as $turnadoAnterior) {

                    $turnadoAnterior->update([
                        'estado_turnado_id' => $estadoCerrado->id,
                        'cerrado_en' => now(),
                    ]);

                    $oficio->historial()->create([
                        'usuario_id' => auth()->id(),
                        'accion' => 'turnado_returnado',
                        'descripcion' =>
                            'El turnado fue returnado y sustituido por un nuevo turnado.',
                        'entidad_relacionada' => Turnado::class,
                        'entidad_relacionada_id' => $turnadoAnterior->id,
                    ]);
                }
            }

            /* NUEVO TURNADO */

            $turnados = [];

            foreach ($participacionesValidas as $participacion) {

                $turnado = Turnado::create([
                    'oficio_id' => $oficio->id,
                    'usuario_id' => $participacion['coordinador']->id,
                    'coordinacion_id' => $participacion['coordinacion_id'],
                    'tipo_participacion_id' => $participacion['tipo_participacion_id'],
                    'estado_turnado_id' => $estadoActivo->id,
                    'turnado_por_id' => auth()->id(),
                    'turnado_en' => now(),
                    'es_principal' => false,
                    'observaciones' => $request->observaciones,
                ]);

                $notificacion = NotificacionTurnado::create([
                    'turnado_id' => $turnado->id,
                    'destinatario_email' => $participacion['coordinador']->email,
                    'estado' => NotificacionTurnado::ESTADO_FALLIDO,
                    'intentos' => 0,
                ]);

                $turnados[] = [
                    'turnado' => $turnado,
                    'notificacion' => $notificacion,
                    'coordinador' => $participacion['coordinador'],
                ];
            }

            /* ESTADO DEL OFICIO */

            $estadoAnterior = $oficio->estado_id;

            $oficio->update([
                'estado_id' => EstadoOficio::TURNADO,
            ]);

            $oficio->historial()->create([
                'usuario_id' => auth()->id(),
                'accion' => $esReturnado
                    ? 'oficio_returnado'
                    : 'oficio_turnado',
                'descripcion' => $esReturnado
                    ? 'Se registró un returnado y se generó un nuevo turnado.'
                    : 'Se registró un nuevo turnado.',
                'estado_anterior_id' => $estadoAnterior,
                'estado_nuevo_id' => EstadoOficio::TURNADO,
            ]);

            return $turnados;
        });

        /* NOTIFICACIONES */

        $notificacionesExitosas = 0;
        $notificacionesFallidas = 0;

        foreach ($turnados as $item) {

            $turnado = $item['turnado'];
            $notificacion = $item['notificacion'];
            $coordinador = $item['coordinador'];

            $messageId = 'sco-notificacion-' .
                $notificacion->id .
                '-' .
                Str::uuid() .
                '@sco.local';

            $notificacion->update([
                'message_id' => $messageId,
                'intentos' => $notificacion->intentos + 1,
                'ultimo_intento_en' => now(),
                'ultimo_error' => null,
                'no_entregado_en' => null,
            ]);

            try {

                Mail::to($coordinador->email)
                    ->send(
                        new OficioTurnadoMail(
                            $turnado->load('oficio.archivos.versiones'),
                            $notificacion
                        )
                    );

                $notificacion->update([
                    'estado' => NotificacionTurnado::ESTADO_EXITOSO,
                    'enviado_en' => now(),
                    'ultimo_error' => null,
                ]);

                $notificacionesExitosas++;

            } catch (\Throwable $e) {

                report($e);

                $notificacion->update([
                    'estado' => NotificacionTurnado::ESTADO_FALLIDO,
                    'ultimo_error' => $e->getMessage(),
                ]);

                $notificacionesFallidas++;
            }
        }

        /* RESPUESTA */

        $accion = $esReturnado
            ? 'Returnado'
            : 'Turnado';

        if ($notificacionesFallidas > 0) {

            return back()->with(
                'warning',
                "{$accion} registrado. {$notificacionesExitosas} notificación(es) enviada(s) correctamente y {$notificacionesFallidas} quedó(aron) pendiente(s) de atención."
            );
        }

        return back()->with(
            'success',
            $esReturnado
                ? 'Returnado registrado y nuevo turnado notificado correctamente.'
                : 'Turnado registrado y notificado correctamente.'
        );
    }


    public function turnarModal(Oficio $oficio)
    {
        $coordinaciones = Coordinacion::where('activo', true)
            ->get();

        $tiposParticipacion = \App\Models\TipoParticipacion::where('activo', true)
            ->get();

        return view(
            'oficios.modals',
            compact(
                'oficio',
                'coordinaciones',
                'tiposParticipacion'
            )
        )->with('turnarModal', true);
    }


    /*
    |--------------------------------------------------------------------------
    | ATENDER TURNADO
    |--------------------------------------------------------------------------
    */
    public function atender(Turnado $turnado)
    {
        abort_unless(
            $turnado->usuario_id === auth()->id(),
            403
        );

        $this->procesarAtendido(
            $turnado,
            auth()->id()
        );

        return back()->with(
            'success',
            'Turnado atendido correctamente.'
        );
    }


    // SCO
    private function procesarAtendido(Turnado $turnado, int $usuarioId)
    {
        if ($turnado->atendido_en) {
            return;
        }

        if (
            $turnado->estadoTurnado &&
            $turnado->estadoTurnado->clave === \App\Models\EstadoTurnado::CERRADO
        ) {
            return;
        }

        $turnado->update([
            'estado_turnado_id' => 3,
            'atendido_en' => now(),
        ]);

        $oficio = $turnado->oficio;

        $oficio->historial()->create([
            'usuario_id' => $usuarioId,
            'accion' => 'turnado_atendido',
            'descripcion' => 'El responsable marcó el turnado como atendido.',
        ]);

        if ($oficio->estado_id === EstadoOficio::CERRADO) {
            return;
        }

        $pendientes = $this->tieneResponsablesPendientes($oficio);

        if ($pendientes) {
            $oficio->update([
                'estado_id' => EstadoOficio::EN_SEGUIMIENTO,
            ]);

            return;
        }

        $tieneRespuesta = Oficio::where(
            'respuesta_a_oficio_id',
            $oficio->id
        )->exists();

        if (
            !$oficio->requiere_respuesta ||
            $tieneRespuesta
        ) {
            $estadoAnterior = $oficio->estado_id;

            $oficio->update([
                'estado_id' => EstadoOficio::CERRADO,
                'cerrado_en' => now(),
            ]);

            $oficio->historial()->create([
                'usuario_id' => $usuarioId,
                'accion' => 'oficio_cerrado_automaticamente',
                'descripcion' =>
                    'Oficio cerrado automáticamente al quedar atendidos o comunicados externamente todos los responsables y cumplirse las condiciones de cierre.',
                'estado_anterior_id' => $estadoAnterior,
                'estado_nuevo_id' => EstadoOficio::CERRADO,
            ]);

            return;
        }

        $oficio->update([
            'estado_id' => EstadoOficio::EN_SEGUIMIENTO,
        ]);
    }


    // Correo
    public function atenderDesdeCorreo(Turnado $turnado)
    {
        if ($turnado->atendido_en) {
            return view('turnados.atendido', [
                'turnado' => $turnado->load('oficio'),
                'yaAtendido' => true,
            ]);
        }

        $this->procesarAtendido(
            $turnado,
            $turnado->usuario_id
        );

        return view('turnados.atendido', [
            'turnado' => $turnado->load('oficio'),
            'yaAtendido' => false,
        ]);
    }


    public function reintentarNotificacion(NotificacionTurnado $notificacion)
    {
        $notificacion->load([
            'turnado.oficio.archivos.versiones',
            'turnado.usuario',
            'turnado.coordinacion',
        ]);

        if ($notificacion->estado === NotificacionTurnado::ESTADO_EXITOSO) {
            return back()->with(
                'info',
                'Esta notificación ya fue enviada correctamente.'
            );
        }

        if (!in_array($notificacion->estado, [
            NotificacionTurnado::ESTADO_FALLIDO,
            NotificacionTurnado::ESTADO_NO_ENTREGADO,
        ], true)) {
            return back()->with(
                'info',
                'Esta notificación ya fue comunicada externamente.'
            );
        }

        $turnado = $notificacion->turnado;

        $messageId = 'sco-notificacion-' .
            $notificacion->id .
            '-' .
            Str::uuid() .
            '@sco.local';

        $notificacion->update([
            'message_id' => $messageId,
            'intentos' => $notificacion->intentos + 1,
            'ultimo_intento_en' => now(),
            'ultimo_error' => null,
            'no_entregado_en' => null,
        ]);

        try {

            Mail::to($notificacion->destinatario_email)
                ->send(
                    new OficioTurnadoMail(
                        $turnado,
                        $notificacion
                    )
                );

            $notificacion->update([
                'estado' => NotificacionTurnado::ESTADO_EXITOSO,
                'enviado_en' => now(),
                'ultimo_error' => null,
            ]);

            DetectarRebotesJob::dispatch()
                ->delay(now()->addSeconds(30));

            return back()->with(
                'success',
                'La notificación fue enviada correctamente.'
            );

        } catch (\Throwable $e) {

            report($e);

            $notificacion->update([
                'estado' => NotificacionTurnado::ESTADO_FALLIDO,
                'ultimo_error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No fue posible enviar la notificación. Puede intentar nuevamente.'
            );
        }
    }


    public function copiarAvisoNotificacion(NotificacionTurnado $notificacion)
    {
        $notificacion->load([
            'turnado.estadoTurnado',
        ]);

        if ($notificacion->estado === NotificacionTurnado::ESTADO_EXITOSO) {
            return back()->with(
                'info',
                'Esta notificación ya fue enviada correctamente.'
            );
        }

        if ($notificacion->estado === NotificacionTurnado::ESTADO_ENVIADO_MANUAL) {
            return back()->with(
                'info',
                'Esta comunicación ya fue registrada como externa.'
            );
        }

        $turnado = $notificacion->turnado;

        $notificacion->update([
            'estado' => NotificacionTurnado::ESTADO_ENVIADO_MANUAL,
            'notificado_manualmente_en' => now(),
            'notificado_manualmente_por_id' => auth()->id(),
        ]);

        if (!in_array(
            $turnado->estadoTurnado->clave,
            [
                \App\Models\EstadoTurnado::ATENDIDO,
                \App\Models\EstadoTurnado::CERRADO,
            ],
            true
        )) {
            $estadoExterno = \App\Models\EstadoTurnado::where(
                'clave',
                \App\Models\EstadoTurnado::COMUNICADO_EXTERNAMENTE
            )->firstOrFail();

            $turnado->update([
                'estado_turnado_id' => $estadoExterno->id,
            ]);

            /*
            * Solo el Responsable Operativo participa
            * en la condición de cierre del oficio.
            */
            if ((int) $turnado->tipo_participacion_id === 1) {

                $oficio = $turnado->oficio;

                if (
                    $oficio->estado_id !== EstadoOficio::CERRADO &&
                    $oficio->estado_id !== EstadoOficio::CANCELADO &&
                    !$this->tieneResponsablesPendientes($oficio)
                ) {
                    $tieneRespuesta = Oficio::where(
                        'respuesta_a_oficio_id',
                        $oficio->id
                    )->exists();

                    if (
                        !$oficio->requiere_respuesta ||
                        $tieneRespuesta
                    ) {
                        $estadoAnterior = $oficio->estado_id;

                        $oficio->update([
                            'estado_id' => EstadoOficio::CERRADO,
                            'cerrado_en' => now(),
                        ]);

                        $oficio->historial()->create([
                            'usuario_id' => auth()->id(),
                            'accion' => 'oficio_cerrado_automaticamente',
                            'descripcion' =>
                                'Oficio cerrado automáticamente al quedar atendidos o comunicados externamente todos los responsables y cumplirse las condiciones de cierre.',
                            'estado_anterior_id' => $estadoAnterior,
                            'estado_nuevo_id' => EstadoOficio::CERRADO,
                        ]);
                    }
                }
            }
        }

        return back()->with(
            'success',
            'La comunicación externa quedó registrada.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCELAR
    |--------------------------------------------------------------------------
    */
    public function cancelar(Oficio $oficio)
    {
        $this->authorize('cancelar', $oficio);

        if (in_array($oficio->estado_id, [
            EstadoOficio::CERRADO,
            EstadoOficio::CANCELADO,
        ])) {
            return response()->json([
                'success' => false,
                'message' =>
                    'El oficio está cerrado o cancelado y no puede cancelarse.',
            ], 422);
        }

        $estadoAnterior = $oficio->estado_id;

        $oficio->update([
            'estado_id' => EstadoOficio::CANCELADO,
            'cancelado_en' => now(),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_cancelado',
            'descripcion' => 'Oficio cancelado manualmente.',
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => EstadoOficio::CANCELADO,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Oficio cancelado correctamente',
        ]);
    }


    private function obtenerTrayectoria(Oficio $oficio): array
    {
        $raiz = $oficio;

        while ($raiz->respuesta_a_oficio_id) {
            $padre = $raiz->oficioPadre;

            if (!$padre) {
                break;
            }

            $raiz = $padre;
        }

        $nodos = collect();

        $agregarDescendientes = function (Oficio $actual) use (&$agregarDescendientes, &$nodos) {
            $actual->loadMissing([
                'estado',
                'tipo',
            ]);

            $nodos->push($actual);

            foreach ($actual->respuestas as $respuesta) {
                $agregarDescendientes($respuesta);
            }
        };

        $agregarDescendientes($raiz);

        return [
            'raiz_id' => $raiz->id,
            'actual_id' => $oficio->id,
            'nodos' => $nodos->values()->all(),
        ];
    }


    public function detalleJson(Oficio $oficio)
    {
        $oficio->load([
            'estado',
            'tipo',
            'responsableActual.usuario',
            'responsableActual.coordinacion',
            'tags',
            'archivos.versiones',
        ]);

        $trayectoria = $this->obtenerTrayectoria($oficio);

        return response()->json([
            'id' => $oficio->id,
            'numero_oficio' => $oficio->numero_oficio,
            'consecutivo' => $oficio->consecutivo,
            'asunto' => $oficio->asunto,
            'descripcion' => $oficio->descripcion,
            'fecha_oficio' => optional($oficio->fecha_oficio)->format('Y-m-d'),
            'fecha_recepcion' => optional($oficio->fecha_recepcion)->format('Y-m-d'),
            'fecha_limite' => optional($oficio->fecha_limite)->format('Y-m-d'),
            'tipo_oficio_id' => $oficio->tipo_oficio_id,
            'remitente_nombre' => $oficio->remitente_nombre,
            'remitente_cargo' => $oficio->remitente_cargo,
            'remitente_dependencia' => $oficio->remitente_dependencia,
            'destinatario_nombre' => $oficio->destinatario_nombre,
            'destinatario_cargo' => $oficio->destinatario_cargo,
            'destinatario_dependencia' => $oficio->destinatario_dependencia,
            'quien_elabora_nombre' => $oficio->quien_elabora_nombre,
            'quien_elabora_cargo' => $oficio->quien_elabora_cargo,
            'link_documento' => $oficio->link_documento,
            'link_drive' => $oficio->link_drive,
            'requiere_respuesta' => $oficio->requiere_respuesta,
            'es_sensible' => $oficio->es_sensible,
            'respuesta_a_oficio_id' => $oficio->respuesta_a_oficio_id,
            'estado' => [
                'clave' => $oficio->estado->clave,
                'nombre' => $oficio->estado->nombre,
                'color' => $oficio->estado->color,
            ],
            'responsable' => $oficio->responsableActual ? [
                'coordinacion' => $oficio->responsableActual->coordinacion->nombre,
                'usuario' => $oficio->responsableActual->usuario->name,
            ] : null,
            'tags' => $oficio->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'nombre' => $tag->nombre,
                ];
            })->values(),
            'archivos' => $oficio->archivos->map(function ($archivo) {
                return [
                    'id' => $archivo->id,
                    'nombre_original' => $archivo->nombre_original,
                    'versiones' => $archivo->versiones->map(function ($version) {
                        return [
                            'version' => $version->version,
                            'ruta' => $version->ruta,
                            'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($version->ruta),
                            'es_actual' => $version->es_actual,
                        ];
                    })->values(),
                ];
            })->values(),
            'puede_cancelar' =>
                !in_array($oficio->estado_id, [
                    EstadoOficio::CERRADO,
                    EstadoOficio::CANCELADO,
                ]) &&
                auth()->user()->can('cancelar', $oficio),
            'trayectoria' => [
                'raiz_id' => $trayectoria['raiz_id'],
                'actual_id' => $trayectoria['actual_id'],
                'nodos' => collect($trayectoria['nodos'])->map(function ($nodo) {
                    $fechaPrincipal = $nodo->cerrado_en
                        ? $nodo->cerrado_en->format('Y-m-d H:i:s')
                        : optional($nodo->fecha_oficio)->format('Y-m-d');

                    return [
                        'id' => $nodo->id,
                        'numero_oficio' => $nodo->numero_oficio,
                        'asunto' => $nodo->asunto,
                        'tipo_oficio_id' => $nodo->tipo_oficio_id,
                        'tipo_nombre' => $nodo->tipo?->nombre,
                        'estado_id' => $nodo->estado_id,
                        'estado' => [
                            'clave' => $nodo->estado?->clave,
                            'nombre' => $nodo->estado?->nombre,
                            'color' => $nodo->estado?->color,
                        ],
                        'fecha_oficio' => optional($nodo->fecha_oficio)->format('Y-m-d'),
                        'fecha_recepcion' => optional($nodo->fecha_recepcion)->format('Y-m-d'),
                        'fecha_limite' => optional($nodo->fecha_limite)->format('Y-m-d'),
                        'fecha_principal' => $fechaPrincipal,
                        'requiere_respuesta' => $nodo->requiere_respuesta,
                        'cerrado_en' => optional($nodo->cerrado_en)->format('Y-m-d H:i:s'),
                        'respuesta_a_oficio_id' => $nodo->respuesta_a_oficio_id,
                    ];
                })->values(),
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */
    public function datatable(Request $request)
    {
        $query = Oficio::with('estado');

        $this->aplicarFiltros(
            $query,
            $request
        );

        return response()->json(
            $query->latest()->get()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSABLES OFICIO
    |--------------------------------------------------------------------------
    */
    private function tieneResponsablesPendientes(Oficio $oficio): bool
    {
        return $oficio->turnados()
            ->where('tipo_participacion_id', 1)
            ->whereNull('atendido_en')
            ->whereHas('estadoTurnado', function ($query) {
                $query->whereNotIn('clave', [
                    \App\Models\EstadoTurnado::ATENDIDO,
                    \App\Models\EstadoTurnado::COMUNICADO_EXTERNAMENTE,
                    \App\Models\EstadoTurnado::CERRADO,
                ]);
            })
            ->exists();
    }

}