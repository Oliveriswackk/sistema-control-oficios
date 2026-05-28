@extends('layouts.sbadmin')

@section('content')

<h1>Oficios</h1>

<a href="/oficios-ui/create" class="btn btn-primary mb-3">
    Nuevo Oficio
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Número</th>
            <th>Asunto</th>
            <th>Estado</th>
        </tr>
    </thead>

    <tbody>
        @foreach($oficios as $oficio)
        <tr>
            <td>{{ $oficio->id }}</td>
            <td>{{ $oficio->numero_oficio }}</td>
            <td>{{ $oficio->asunto }}</td>
            <td>{{ $oficio->estado->nombre ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection