<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use App\Models\Oficio;
use App\Models\Turnado;
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
    | GUARDAR OFICIO / CREAR
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
            'estado_id' => $request->estado_id,

            'asunto' => $request->asunto,
            'descripcion' => $request->descripcion,

            'fecha_oficio' => $request->fecha_oficio,
            'fecha_recepcion' => $request->fecha_recepcion,
            'fecha_limite' => $request->fecha_limite,

            'requiere_respuesta' => $request->requiere_respuesta ?? false,
            'es_sensible' => $request->es_sensible ?? false,

            'remitente_nombre' => $request->remitente_nombre,
            'remitente_cargo' => $request->remitente_cargo,
            'remitente_dependencia' => $request->remitente_dependencia,

            'destinatario_nombre' => $request->destinatario_nombre,
            'destinatario_cargo' => $request->destinatario_cargo,
            'destinatario_dependencia' => $request->destinatario_dependencia,

            'quien_elabora_nombre' => $request->quien_elabora_nombre,
            'quien_elabora_cargo' => $request->quien_elabora_cargo,

            'responsable_inicial_id' => auth()->id(),
            'coordinacion_origen_id' => $request->coordinacion_origen_id,
            'usuario_registro_id' => auth()->id(),

            'link_documento' => $request->link_documento,

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
    | VER OFICIO
    |--------------------------------------------------------------------------
    */
    public function show(Oficio $oficio)
    {
        $this->authorize('view', $oficio);

        return view('oficios.show', compact('oficio'));
    }


    /*
    |--------------------------------------------------------------------------
    | TURNAR
    |--------------------------------------------------------------------------
    */
    public function turnar(Request $request, Oficio $oficio)
    {
        if ($oficio->estado_id == 5) {
            return back()->with('error', 'No se puede turnar un oficio cerrado');
        }
        
        $request->validate([
            'usuario_id' => 'required',
            'coordinacion_id' => 'required',
            'tipo_participacion_id' => 'required',
        ]);

        \App\Models\Turnado::create([
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
            'estado_id' => 5, // cerrado
            'cerrado_en' => now(),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_cerrado',
            'descripcion' => 'Oficio cerrado desde recepción',
        ]);

        return back()->with('success', 'Oficio cerrado');
    }
}