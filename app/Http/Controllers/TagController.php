<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function buscar(Request $request)
    {
        return Tag::query()
            ->where(
                'nombre',
                'like',
                '%' . $request->q . '%'
            )
            ->limit(10)
            ->get();
    }

    public function agregarTag(Request $request, \App\Models\Oficio $oficio)
    {
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
        $oficio->tags()->detach($tag->id);

        return response()->json([
            'success' => true,
            'tag' => $tag
        ]);
    }
}