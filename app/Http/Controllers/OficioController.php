<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOficioRequest;
use App\Services\OficioService;
use App\Models\Oficio;
use App\Models\Turnado;
use App\Models\EstadoOficio;
use App\Models\TipoOficio;
use App\Models\Coordinacion;
use App\Models\User;
use App\Models\FolioReservado;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

        if ($request->filled('tipo_oficio_id')) {

            $query->where(
                'tipo_oficio_id',
                $request->tipo_oficio_id
            );

        }

        if ($request->filled('remitente_dependencia')) {
            $query->where(
                'remitente_dependencia',
                'like',
                '%' . $request->remitente_dependencia . '%'
            );
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

        $estados = EstadoOficio::all();

        $tiposOficio = TipoOficio::all();

        return view(
            'dashboard',
            compact(
                'oficios',
                'oficiosRelacionables',
                'coordinaciones',
                'estados',
                'tiposOficio'
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


        if (
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('coordinador')
        ) {

            $listosCerrar = Oficio::with([
                'turnados.tipoParticipacion'
            ])
            ->where(
                'estado_id',
                EstadoOficio::EN_SEGUIMIENTO
            )
            ->where('requiere_respuesta', false)
            ->get()
            ->filter(function ($oficio) {


                // Solo responsables operativos cuentan
                $responsables = $oficio->turnados
                    ->filter(function ($turnado) {

                        return $turnado->tipoParticipacion
                            && $turnado->tipoParticipacion->implica_responsabilidad;

                    });


                // Debe existir al menos un responsable
                if ($responsables->isEmpty()) {
                    return false;
                }


                // Todos los responsables deben haber atendido
                return $responsables->every(function ($turnado) {

                    return !is_null($turnado->atendido_en);

                });


            })
            ->values();

        }


        return view('home', compact(
            'turnados',
            'listosCerrar'
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

            /* Responder Oficios Cerrados - Deshabilitado

            if ($padre->estado_id === EstadoOficio::CERRADO) {
                return back()->with('error', 'No puedes responder un oficio cerrado');
            }
            */
        }

        $respuestaId = $request->input('respuesta_a_oficio_id');

        $tipo = (int) $request->tipo_oficio_id;

        // =====================================================
        // ENVIADO
        // =====================================================

        if ($tipo === 1) {

            // ----- CASO 1. Se usa el reservado -----
            if ($request->filled('folio_reservado_id')) {

                $folio = \App\Models\FolioReservado::where('id', $request->folio_reservado_id)
                    ->where('estado', 'reservado')
                    ->lockForUpdate()
                    ->first();

                if (!$folio) {
                    return back()->with('error', 'El folio reservado no está disponible');
                }

                $coordinacionId = $folio->coordinacion_id;
                $numero = $folio->numero;
                $anio = $folio->anio;

                $coordinacion = \App\Models\Coordinacion::findOrFail($coordinacionId);

                $numeroOficio =
                    "SESEA-{$coordinacion->clave}-" .
                    str_pad($numero, 3, '0', STR_PAD_LEFT) .
                    "-{$anio}";

                $consecutivo = $numero;

                $folio->update([
                    'estado' => 'usado',
                    'numero_oficio' => $numeroOficio,
                ]);
                
            // ----- CASO 2. Se consume el siguiente número -----
            } else {

                $datos = $oficioService->consumirSiguienteNumero(
                    $request->coordinacion_origen_id,
                    $request->fecha_oficio
                );

                $numeroOficio = $datos['numero_oficio'];
                $consecutivo = $datos['consecutivo'];

                $coordinacionId = $request->coordinacion_origen_id;
            }
        }

        // =====================================================
        // RECIBIDO / RECIBIDO CPC
        // =====================================================

        else {

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

            'usuario_registro_id' => auth()->id(),
            'responsable_inicial_id' => auth()->id(),

            'coordinacion_origen_id' => $coordinacionId,
        ]);

        // -------- Tags --------

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

        

        // ============= CIERRE AUTOMÁTICO OFICIOS ENVIADOS SIN RESPUESTA =============

        if (
            (int) $oficio->tipo_oficio_id === 1 &&
            !$oficio->requiere_respuesta
        ) {

            $oficio->update([
                'estado_id' => EstadoOficio::CERRADO,
                'cerrado_en' => now(),
            ]);

            $oficio->historial()->create([
                'usuario_id' => auth()->id(),
                'accion' => 'oficio_cerrado_automaticamente',
                'descripcion' => 'Oficio enviado cerrado automáticamente porque no requiere respuesta.',
                'estado_anterior_id' => EstadoOficio::REGISTRADO,
                'estado_nuevo_id' => EstadoOficio::CERRADO,
            ]);

        }

        // ============= ENVIADOS QUE REQUIEREN RESPUESTA =============

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
                'descripcion' => 'El oficio enviado requiere respuesta y queda en seguimiento.',
                'estado_anterior_id' => EstadoOficio::REGISTRADO,
                'estado_nuevo_id' => EstadoOficio::EN_SEGUIMIENTO,
            ]);

        }


        // ============= CIERRE AUTOMÁTICO DE OFICIO CON RESPUESTA =============

        if ($oficio->respuesta_a_oficio_id) {

            $padre = Oficio::find($oficio->respuesta_a_oficio_id);

            if ($padre && $padre->requiere_respuesta) {

                $estadoAnterior = $padre->estado_id;

                $padre->update([
                    'estado_id' => EstadoOficio::CERRADO,
                    'cerrado_en' => now(),
                ]);
               
                $padre->refresh();

                $padre->historial()->create([
                    'usuario_id' => auth()->id(),
                    'accion' => 'oficio_cerrado_automaticamente',
                    'descripcion' => 'El oficio fue cerrado automáticamente al registrar una respuesta.',
                    'estado_anterior_id' => $estadoAnterior,
                    'estado_nuevo_id' => EstadoOficio::CERRADO,
                ]);

            }

        }

        if ($request->ajax()) {

            $oficio->load('estado', 'tags');

            return response()->json([
                'row' => view(
                    'oficios.partials.oficio-row',
                    compact('oficio')
                )->render()
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Oficio creado correctamente');
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
        $editable = Gate::allows('update', $oficio);

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

        foreach ($participaciones as $coordId => $tipoId) {


            if (empty($tipoId)) {
                continue;
            }

            // Buscar coordinador de esa coordinación
            $coordinador = Coordinacion::find($coordId)
                ->users()
                ->whereHas('roles', function ($query) {

                    $query->where('clave','coordinador');

                })
                ->first();

            if (!$coordinador) {
                return back()->with(
                    'error',
                    'La coordinación seleccionada no tiene un coordinador asignado'
                );
            }

            $coordinador = Coordinacion::find($coordId)
                ->users()
                ->whereHas('roles', function ($query) {
                    $query->where('clave', 'coordinador');
                })
                ->first();


            Turnado::create([

                'oficio_id' => $oficio->id,

                'usuario_id' => $coordinador?->id,

                'coordinacion_id' => $coordId,

                'tipo_participacion_id' => $tipoId,

                'estado_turnado_id' => 1,

                'turnado_por_id' => auth()->id(),

                'turnado_en' => now(),

                'es_principal' => false,

                'observaciones' => $request->observaciones,

            ]);

            if ((int) $tipoId === 1) {

                $responsableEncontrado = true;

            }

        }// Fin foreach

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
        $coordinaciones = Coordinacion::where('activo', true)
            ->get();

        $tiposParticipacion = \App\Models\TipoParticipacion::where('activo', true)
            ->get();

        return view(
            'oficios.modals.turnar',
            compact(
                'oficio',
                'coordinaciones',
                'tiposParticipacion'
            )
        );
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

        $oficio = $turnado->oficio;

        $oficio->update([
            'estado_id' => EstadoOficio::EN_SEGUIMIENTO,
        ]);

        $turnado->oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'turnado_atendido',
            'descripcion' => 'El responsable marcó el turnado como atendido',
        ]);

        $pendientes = $turnado->oficio
            ->turnados()
            ->whereHas('tipoParticipacion', function ($q) {
                $q->where('implica_responsabilidad', true);
            })
            ->whereNull('atendido_en')
            ->count();


        if ($pendientes === 0) {

            if ($turnado->oficio->requiere_respuesta) {

                return back()->with(
                    'success',
                    'Todos los responsables atendieron. Este oficio requiere respuesta y se cerrará automáticamente al registrar el oficio correspondiente.'
                );

            }

            return back()->with([
                'success' => 'Turnado atendido',
                'mostrar_cierre' => true,
                'oficio_id' => $turnado->oficio_id,
            ]);

        }

        return back()->with(
            'success',
            'Turnado atendido'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function cerrar(Oficio $oficio)
    {
        $this->authorize('cerrar', $oficio);

        if ($oficio->requiere_respuesta) {
            return back()->with(
                'error',
                'Este oficio requiere respuesta y será cerrado automáticamente al registrar el oficio relacionado.'
            );
        }

        $pendientes = $oficio->turnados()
            ->where('tipo_participacion_id', 1)
            ->whereNull('atendido_en')
            ->exists();

        if ($pendientes) {
            return back()->with(
                'error',
                'Aún existen responsables pendientes por atender.'
            );
        }

        $estadoAnterior = $oficio->estado_id;

        $oficio->update([
            'estado_id' => EstadoOficio::CERRADO,
            'cerrado_en' => now(),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_cerrado',
            'descripcion' => 'El coordinador cerró el oficio',
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => EstadoOficio::CERRADO,
        ]);

        return back()->with(
            'success',
            'Oficio cerrado correctamente.'
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

        $estadoAnterior = $oficio->estado_id;

        $oficio->update([
            'estado_id' => EstadoOficio::CANCELADO,
            'cerrado_en' => now(),
        ]);

        $oficio->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => 'oficio_cancelado',
            'descripcion' => 'Oficio cancelado manualmente.',
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => EstadoOficio::CANCELADO,
        ]);

        return back()->with(
            'success',
            'Oficio cancelado correctamente'
        );
    }


    public function detalleJson(Oficio $oficio)
    {
        return response()->json([
            'id' => $oficio->id,

            'estado' => [
                'clave' => $oficio->estado->clave
            ],

            'puede_cancelar' => auth()->user()
                ->can('cancelar', $oficio)
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
            ->exists();
    }

}