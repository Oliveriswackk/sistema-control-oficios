<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use App\Models\Oficio;
use App\Models\Turnado;
use App\Models\EstadoOficio;
use App\Models\Coordinacion;
use App\Models\Tag;
use App\Services\OficioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        if ($request->filled('numero_oficio')) {
            $query->where(
                'numero_oficio',
                'like',
                '%' . $request->numero_oficio . '%'
            );
        }

        if ($request->filled('asunto')) {
            $query->where(
                'asunto',
                'like',
                '%' . $request->asunto . '%'
            );
        }

        if ($request->filled('remitente_dependencia')) {
            $query->where(
                'remitente_dependencia',
                'like',
                '%' . $request->remitente_dependencia . '%'
            );
        }

        if ($request->filled('destinatario_dependencia')) {
            $query->where(
                'destinatario_dependencia',
                'like',
                '%' . $request->destinatario_dependencia . '%'
            );
        }

        if ($request->filled('coordinacion_origen_id')) {
            $query->where(
                'coordinacion_origen_id',
                $request->coordinacion_origen_id
            );
        }

        return $query;
    }


    public function dashboard(Request $request)
    {
        $query = Oficio::with([
            'estado',
            'turnados',
            'tags'
        ]);

        $this->aplicarFiltros(
            $query,
            $request
        );

        $oficios = $query
            ->latest()
            ->get();

        $oficiosRelacionables = Oficio::select(
            'id',
            'numero_oficio',
            'asunto'
        )
        ->latest()
        ->get();

        $coordinaciones = Coordinacion::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'dashboard',
            compact(
                'oficios',
                'oficiosRelacionables',
                'coordinaciones'
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
            ->whereIn('estado_turnado_id', [1, 2])
            ->latest()
            ->get();

        $listosCerrar = collect();

        if (auth()->user()->hasPermission('puede_cerrar')) {

            $listosCerrar = Oficio::with('turnados')
                ->where('estado_id', 3) // Solo activos en estado "en proceso / listo"
                ->get()
                ->filter(function ($oficio) {

                    return $oficio->turnados->isNotEmpty()
                        && $oficio->turnados->every(fn ($t) => $t->estado_turnado_id === 3);
                })
                ->values();
        }

        return view('home', compact('turnados', 'listosCerrar'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR FORM 
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', Oficio::class);

        return response()->json(['message' => 'ok']);
    }


    /*
    |--------------------------------------------------------------------------
    | NO. OFICIO CONSECUTIVO
    |--------------------------------------------------------------------------
    */
    public function proximoConsecutivo(Request $request)
    {
        if (!$request->coordinacion_id) {
            return response()->json([
                'error' => 'Coordinación requerida'
            ], 422);
        }

        $coordinacion = Coordinacion::findOrFail($request->coordinacion_id);

        $anio = Carbon::now()->year;
        
        $ultimo = Oficio::where('coordinacion_origen_id', $coordinacion->id)
            ->whereYear('fecha_oficio', $anio)
            ->max('consecutivo');

        $consecutivo = $ultimo ? $ultimo + 1 : 1;

        return response()->json([
            'numero_oficio' => "SESEA-{$coordinacion->clave}-" .
                str_pad($consecutivo, 3, '0', STR_PAD_LEFT) .
                "-{$anio}",
            'consecutivo' => $consecutivo,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR / CREAR
    |--------------------------------------------------------------------------
    */
    public function store(StoreOficioRequest $request)
    {
        $this->authorize('create', Oficio::class);

        $esRespuesta = $request->respuesta_a_oficio_id != 0;

        if ($esRespuesta) {

            $padre = Oficio::find($request->respuesta_a_oficio_id);

            if (!$padre) {
                return back()->with('error', 'Oficio padre no existe');
            }

            if ($padre->estado_id === EstadoOficio::CERRADO) {
                return back()->with('error', 'No puedes responder un oficio cerrado');
            }
        }

        $coordinacion = Coordinacion::findOrFail($request->coordinacion_origen_id);

        $año = Carbon::parse($request->fecha_oficio)->year;

        $ultimoConsecutivo = Oficio::where('coordinacion_origen_id', $coordinacion->id)
            ->whereYear('fecha_oficio', $año)
            ->max('consecutivo');

        $consecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;

        $numeroOficio = "SESEA-{$coordinacion->clave}-" .
            str_pad($consecutivo, 3, '0', STR_PAD_LEFT) .
            "-{$año}";

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
                $request->respuesta_a_oficio_id == 0
                    ? null
                    : $request->respuesta_a_oficio_id,

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

            'usuario_registro_id' => auth()->id(),
            'responsable_inicial_id' => auth()->id(),

            'coordinacion_origen_id' => $coordinacion->id,
        ]);

        if ($request->filled('tags')) {

            $tagsIds = [];

            foreach (explode(',', $request->tags) as $tag) {

                $tag = trim($tag);

                if (!$tag) {
                    continue;
                }

                $tagModel = Tag::firstOrCreate([
                    'nombre' => mb_strtolower($tag)
                ]);

                $tagsIds[] = $tagModel->id;
            }

            $oficio->tags()->sync($tagsIds);
        }

        $oficio->registrarEvento(
            'oficio_creado',
            'Oficio creado desde interfaz web'
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Oficio creado correctamente');
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Oficio $oficio)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('puede_registrar_oficios')) {
            abort(403);
        }

        $oficio->update([

            'numero_oficio' => $request->numero_oficio,
            'consecutivo' => $request->consecutivo,

            'asunto' => $request->asunto,
            'descripcion' => $request->descripcion,

            'fecha_oficio' => $request->fecha_oficio,
            'fecha_recepcion' => $request->fecha_recepcion,
            'fecha_limite' => $request->fecha_limite,

            'requiere_respuesta' => $request->boolean('requiere_respuesta'),
            'es_sensible' => $request->boolean('es_sensible'),
            'tipo_oficio_id' => $request->tipo_oficio_id,

            'remitente_nombre' => $request->remitente_nombre,
            'remitente_cargo' => $request->remitente_cargo,
            'remitente_dependencia' => $request->remitente_dependencia,

            'destinatario_nombre' => $request->destinatario_nombre,
            'destinatario_cargo' => $request->destinatario_cargo,
            'destinatario_dependencia' => $request->destinatario_dependencia,

            'quien_elabora_nombre' => $request->quien_elabora_nombre,
            'quien_elabora_cargo' => $request->quien_elabora_cargo,

            'link_documento' => $request->link_documento,
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_editado',
            'descripcion' => 'Se actualizaron datos generales del oficio',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Oficio actualizado correctamente'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLES
    |--------------------------------------------------------------------------
    */
    public function detalle(Oficio $oficio)
    {
        $editable =
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasPermission('puede_registrar_oficios');

        $oficio->load([
            'archivos.versiones',
            'estado',
            'responsableActual.usuario',
            'responsableActual.coordinacion'
        ]);

        return view(
            'oficios.modals.detalle',
            compact('oficio', 'editable')
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
                'tipoOficio',
                'turnados.usuario',
                'historial.usuario'
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
        if ($oficio->estado_id == EstadoOficio::CERRADO) {

            return back()->with(
                'error',
                'No se puede turnar un oficio cerrado'
            );
        }

        $participaciones = $request->input('participacion', []);

        $responsableEncontrado = false;

        foreach ($participaciones as $coordId => $usuarios) {

            foreach ($usuarios as $userId => $tipoId) {

                if (empty($tipoId)) {
                    continue;
                }

                Turnado::create([

                    'oficio_id' => $oficio->id,

                    'usuario_id' => $userId,

                    'coordinacion_id' => $coordId,

                    'tipo_participacion_id' => $tipoId,

                    'estado_turnado_id' => 1,

                    'turnado_por_id' => auth()->id(),

                    'turnado_en' => now(),

                    'es_principal' => false,

                    'observaciones' => $request->observaciones,
                ]);

                if ((int)$tipoId === 1) {

                    $responsableEncontrado = true;

                    $oficio->update([
                        'coordinacion_origen_id' => $coordId
                    ]);
                }
            }
        }

        if (!$responsableEncontrado) {

            return back()->with(
                'error',
                'Debe existir al menos un Responsable Operativo'
            );
        }

        //Registro bitácora - Turnado
        $estadoAnterior = $oficio->estado_id;

        $oficio->update([
            'estado_id' => EstadoOficio::TURNADO
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_turnado',
            'descripcion' => 'Se registró un nuevo turnado',
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => EstadoOficio::TURNADO,
        ]);

        return back()->with(
            'success',
            'Turnado registrado correctamente'
        );
    }


    public function turnarModal(Oficio $oficio)
    {
        $coordinaciones = \App\Models\Coordinacion::with('users')->get();

        $tiposParticipacion = \App\Models\TipoParticipacion::all();

        return view('oficios.modals.turnar', compact(
            'oficio',
            'coordinaciones',
            'tiposParticipacion'
        ));
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

        if ($turnado->atendido_en) {
            return back();
        }

        $turnado->update([
            'estado_turnado_id' => 3,
            'atendido_en' => now(),
        ]);

        $turnado->oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'turnado_atendido',
            'descripcion' => 'El responsable marcó el turnado como atendido',
        ]);

        return back()->with(
            'success',
            'Turnado atendido'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR
    |--------------------------------------------------------------------------
    */
    public function cerrar(Oficio $oficio)
    {
        $this->authorize('cerrar', $oficio);

        // validar que todos los turnados estén atendidos o cerrados
        $pendientes = $oficio->turnados()
            ->whereIn('estado_turnado_id', [1, 2])
            ->exists();

        if ($pendientes) {
            return back()->with('error', 'No se puede cerrar: hay turnados pendientes');
        }

        $oficio->update([
            'estado_id' => EstadoOficio::CERRADO,
            'cerrado_en' => now(),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_cerrado',
            'descripcion' => 'Oficio cerrado desde recepción',
        ]);

        return back()->with('success', 'Oficio cerrado');
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
}