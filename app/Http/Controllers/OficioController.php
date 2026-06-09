<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use App\Models\Oficio;
use App\Models\Turnado;
use App\Models\EstadoOficio;
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


    public function dashboard()
    {
        $oficios = Oficio::with(['estado', 'turnados'])
            ->latest()
            ->get();

        return view('dashboard', compact('oficios'));
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
    | GUARDAR / CREAR
    |--------------------------------------------------------------------------
    */
    public function store(StoreOficioRequest $request)
    {
        $this->authorize('create', Oficio::class);

        $oficio = Oficio::create([
        'uuid' => (string) Str::uuid(),

        'numero_oficio' => $request->numero_oficio,
        'consecutivo' => $request->consecutivo,
        'tipo_oficio_id' => $request->tipo_oficio_id,
        'asunto' => $request->asunto,
        'descripcion' => $request->descripcion,

        'fecha_oficio' => $request->fecha_oficio,
        'fecha_recepcion' => $request->fecha_recepcion,
        'fecha_limite' => $request->fecha_limite,

        'requiere_respuesta' => $request->requiere_respuesta ?? 0,
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

        // SISTEMA 
        'estado_id' => EstadoOficio::REGISTRADO,
        'usuario_registro_id' => auth()->id(),
        'responsable_inicial_id' => auth()->id(),
        'coordinacion_origen_id' => 1,
    ]);

        // EVENTO BASE DE TRAZABILIDAD (mínimo viable)
        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_creado',
            'descripcion' => 'Oficio creado desde interfaz web',
        ]);

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
    | DETALLE OFICIO
    |--------------------------------------------------------------------------
    */
    public function detalle(Oficio $oficio)
    {
        return response()->json([

            'id' => $oficio->id,

            'numero_oficio' => $oficio->numero_oficio,
            'consecutivo' => $oficio->consecutivo,

            'asunto' => $oficio->asunto,
            'descripcion' => $oficio->descripcion,

            'fecha_oficio' => $oficio->fecha_oficio,
            'fecha_recepcion' => $oficio->fecha_recepcion,
            'fecha_limite' => $oficio->fecha_limite,

            'requiere_respuesta' => $oficio->requiere_respuesta,
            'es_sensible' => $oficio->es_sensible,

            'remitente_nombre' => $oficio->remitente_nombre,
            'remitente_cargo' => $oficio->remitente_cargo,
            'remitente_dependencia' => $oficio->remitente_dependencia,

            'destinatario_nombre' => $oficio->destinatario_nombre,
            'destinatario_cargo' => $oficio->destinatario_cargo,
            'destinatario_dependencia' => $oficio->destinatario_dependencia,

            'quien_elabora_nombre' => $oficio->quien_elabora_nombre,
            'quien_elabora_cargo' => $oficio->quien_elabora_cargo,

            'link_documento' => $oficio->link_documento,

            'estado' => $oficio->estado->nombre ?? null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VER OFICIO
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
        if ($oficio->estado_id == EstadoOficio::CERRADO ) {
            return back()->with('error', 'No se puede turnar un oficio cerrado');
        }

        $request->validate([
            'usuario_id' => 'required',
            'coordinacion_id' => 'required',
            'tipo_participacion_id' => 'required',
        ]);

        $turnado = \App\Models\Turnado::create([
            'oficio_id' => $oficio->id,
            'usuario_id' => $request->usuario_id,
            'coordinacion_id' => $request->coordinacion_id,
            'tipo_participacion_id' => $request->tipo_participacion_id,
            'estado_turnado_id' => 1,

            'turnado_por_id' => auth()->id(),
            'turnado_en' => now(),

            'es_principal' => true,
            'observaciones' => $request->observaciones,
        ]);

        /* Si el tipo de participación es "responsable", actualizar la coordinación origen del oficio */
        if ((int) $request->tipo_participacion_id === 1) {

            $oficio->update([
                'coordinacion_origen_id' => $request->coordinacion_id,
            ]);
        }

        return back()->with('success', 'Oficio turnado correctamente');
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

        /*
        |--------------------------------------------------------------------------
        | Número de oficio
        |--------------------------------------------------------------------------
        */

        if ($request->filled('numero_oficio')) {

            $query->where(
                'numero_oficio',
                'like',
                '%' . $request->numero_oficio . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Asunto
        |--------------------------------------------------------------------------
        */

        if ($request->filled('asunto')) {

            $query->where(
                'asunto',
                'like',
                '%' . $request->asunto . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remitente
        |--------------------------------------------------------------------------
        */

        if ($request->filled('remitente_dependencia')) {

            $query->where(
                'remitente_dependencia',
                'like',
                '%' . $request->remitente_dependencia . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Destinatario
        |--------------------------------------------------------------------------
        */

        if ($request->filled('destinatario_dependencia')) {

            $query->where(
                'destinatario_dependencia',
                'like',
                '%' . $request->destinatario_dependencia . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Coordinación origen
        |--------------------------------------------------------------------------
        */

        if ($request->filled('coordinacion_origen_id')) {

            $query->where(
                'coordinacion_origen_id',
                $request->coordinacion_origen_id
            );
        }

        return response()->json(
            $query->latest()->get()
        );
    }
}