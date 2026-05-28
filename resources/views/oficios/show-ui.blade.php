@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h3>{{ $oficio->numero_oficio }}</h3>

    <p>{{ $oficio->asunto }}</p>

    <hr>

    <h5>Estado</h5>
    <span>{{ $oficio->estado->nombre }}</span>

    <hr>

    <h5>Turnados</h5>

    @foreach($oficio->turnados as $turnado)
        <div>
            {{ $turnado->usuario_id }} - {{ $turnado->observaciones }}
        </div>
    @endforeach

    <hr>

    <h5>Historial</h5>

    @foreach($oficio->historial as $h)
        <div>
            {{ $h->evento }} - {{ $h->descripcion }}
        </div>
    @endforeach

</div>

@endsection