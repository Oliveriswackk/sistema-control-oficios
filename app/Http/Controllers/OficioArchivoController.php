<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Oficio;
use App\Models\OficioArchivo;
use App\Models\OficioArchivoVersion;

class OficioArchivoController extends Controller
{
    public function store(Request $request, Oficio $oficio)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('archivo');

        $hash = hash_file('sha256', $file->getRealPath());

        // 1. Crear registro lógico si no existe
        $archivo = OficioArchivo::firstOrCreate(
            [
                'oficio_id' => $oficio->id,
            ],
            [
                'nombre_original' => $file->getClientOriginalName(),
                'tipo_archivo' => $file->getClientMimeType(),
                'nivel_acceso' => 'publico',
            ]
        );

        $archivo->update([
            'nombre_original' => $file->getClientOriginalName(),
            'tipo_archivo' => $file->getClientMimeType(),
        ]);

        // 2. Calcular versión
        $ultimaVersion = $archivo->versiones()->max('version');

        $version = ($ultimaVersion ?? 0) + 1;

        $esReemplazo = $ultimaVersion != null;

        // 3. Guardar archivo físico
        $path = $file->store("oficios/{$oficio->id}", 'public');

        // 4. Marcar anteriores como no actuales
        $archivo->versiones()->update([
            'es_actual' => false
        ]);

        // 5. Crear nueva versión
        $archivo->versiones()->create([
            'ruta' => $path,
            'mime_type' => $file->getClientMimeType(),
            'tamano' => $file->getSize(),
            'hash_sha256' => $hash,
            'version' => $version,
            'es_actual' => true,
            'es_publica' => true,
            'subido_por_id' => auth()->id(),
        ]);

        // 6. Registro bitácora - Archivo del Oficio
        $oficio->historial()->create([
            'usuario_id' => auth()->id(),

            'accion' => $esReemplazo
                ? 'archivo_reemplazado'
                : 'archivo_subido',

            'descripcion' => $esReemplazo
                ? "Se reemplazó el documento principal (versión {$version})"
                : 'Se cargó el documento principal del oficio',

            'entidad_relacionada' => 'oficio_archivo',

            'entidad_relacionada_id' => $archivo->id,
        ]);

        return back()->with('success', 'Archivo subido correctamente');
    }
}
