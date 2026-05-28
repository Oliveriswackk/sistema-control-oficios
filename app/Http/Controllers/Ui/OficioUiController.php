<?php

namespace App\Http\Controllers\Ui;

use App\Http\Controllers\Controller;
use App\Models\Oficio;

class OficioUiController extends Controller
{
    public function index()
    {
        $oficios = Oficio::with('estado')->latest()->get();

        return view('oficios.index-ui', compact('oficios'));
    }

    public function show(Oficio $oficio)
    {
        $oficio->load(['estado', 'tipo', 'turnados']);

        return view('oficios.show-ui', compact('oficio'));
    }
}