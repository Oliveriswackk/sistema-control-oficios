<?php

namespace App\Http\Controllers;

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
        $this->authorize('viewAny', Oficio::class);

        $oficios = Oficio::query()
            ->latest()
            ->paginate(20);

        return response()->json($oficios);
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR FORM (si luego hay vista)
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
    public function store(Request $request)
    {
        $this->authorize('create', Oficio::class);

        $oficio = Oficio::create([
            'uuid' => \Str::uuid(),

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

        return response()->json($oficio);
    }

    /*
    |--------------------------------------------------------------------------
    | VER OFICIO
    |--------------------------------------------------------------------------
    */
    public function show(Oficio $oficio)
    {
        $this->authorize('view', $oficio);

        return response()->json($oficio);
    }

    /*
    |--------------------------------------------------------------------------
    | TURNAR
    |--------------------------------------------------------------------------
    */
    public function turnar(Request $request, Oficio $oficio)
    {
        $this->authorize('turnar', $oficio);

        // aquí después conectamos tabla turnados
        return response()->json([
            'message' => 'Oficio turnado (placeholder)',
            'oficio_id' => $oficio->id
        ]);
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