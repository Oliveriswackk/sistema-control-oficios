<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use Illuminate\Support\Str;
use App\Models\Oficio;
use Illuminate\Http\Request;

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
    | GUARDAR OFICIO
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
        ->route('oficios.ui')
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

        return response()->json($oficio); //Pendiente cambiar
    }

    /*
    |--------------------------------------------------------------------------
    | TURNAR
    |--------------------------------------------------------------------------
    */
    public function turnar(Request $request, Oficio $oficio)
    {
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

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | CERRAR
    |--------------------------------------------------------------------------
    */
    public function cerrar(Oficio $oficio)
    {
        $this->authorize('cerrar', $oficio);

        $oficio->update([
            'cerrado_en' => now(),
        ]);

        return response()->json([
            'message' => 'Oficio cerrado',
            'oficio_id' => $oficio->id
        ]);
    }
}