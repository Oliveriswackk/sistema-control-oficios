<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function buscar(Request $request)
    {
        $query = Tag::query()
            ->where(
                'nombre',
                'like',
                '%' . $request->q . '%'
            );

        if (!$request->user()->hasPermission('puede_ver_sensibles')) {
            $query->whereHas('oficios', function ($oficiosQuery) {
                $oficiosQuery->where('es_sensible', false);
            });
        }

        return $query->limit(10)->get();
    }

    public function agregarTag(Request $request, \App\Models\Oficio $oficio)
    {
        $this->authorize('verSensible', $oficio);

        $tag = Tag::firstOrCreate([
            'nombre' => trim($request->nombre)
        ]);

        $oficio->tags()->syncWithoutDetaching([$tag->id]);

        return response()->json([
            'success' => true,
            'tag' => $tag
        ]);
    }
    

    public function eliminarTag(\App\Models\Oficio $oficio, Tag $tag)
    {
        $this->authorize('verSensible', $oficio);

        $oficio->tags()->detach($tag->id);

        return response()->json([
            'success' => true,
            'tag' => $tag
        ]);
    }
}