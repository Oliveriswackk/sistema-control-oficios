<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SugerenciasController extends Controller
{
    public function personas(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $resultados = collect();

        $usuarios = User::query()
            ->where('name', 'like', "%{$q}%")
            ->with('coordinaciones')
            ->limit(10)
            ->get();

        foreach ($usuarios as $usuario) {
            $resultados->push([
                'origen' => 'interno',
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'cargo' => $usuario->cargo,
                'dependencia' => $usuario->coordinaciones->isNotEmpty()
                    ? 'Secretaría Ejecutiva del Sistema Estatal Anticorrupción'
                    : null,
            ]);
        }

        try {
            $respuesta = Http::timeout(3)->get(
                rtrim(config('app.directorio_api_url'), '/') . '/api/contactos',
                ['q' => $q]
            );

            if ($respuesta->successful()) {
                foreach ($respuesta->json() as $contacto) {
                    $resultados->push([
                        'origen' => 'directorio',
                        'id' => $contacto['id'],
                        'nombre' => $contacto['nombre'],
                        'cargo' => $contacto['cargo'],
                        'dependencia' => $contacto['dependencia'],
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('No fue posible consultar Directorio', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(
            $resultados->take(10)->values()
        );
    }
}