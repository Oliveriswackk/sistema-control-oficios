{{-- Vista Bandeja de Trabajo --}}

@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <h1 class="h3 mb-0 text-gray-800">
            Mi Bandeja
        </h1>

        <ul class="nav nav-tabs mb-3" id="homeTabs" role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-toggle="tab" href="#bandeja" role="tab">
                    {{ $turnados->count() }} Bandeja
                </a>
            </li>

            @if(auth()->user()->hasPermission('puede_cerrar'))

                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-toggle="tab" href="#cerrar" role="tab">
                        {{ $listosCerrar->count() }} Listos para cerrar
                    </a>
                </li>

            @endif

        </ul>

    </div>

    <div class="tab-content">

        {{-- BANDEJA --}}

        <div class="tab-pane fade show active" id="bandeja" role="tabpanel" aria-labelledby="bandeja-tab">

            <div class="card shadow mb-4">

                <div class="card-header py-3">

                    <h6 class="m-0 font-weight-bold text-primary">
                        Oficios Pendientes
                    </h6>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Asunto</th>
                                    <th>Turnado en</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($turnados as $turnado)

                                    <tr>

                                        <td>{{ $turnado->oficio->id }}</td>

                                        <td>{{ $turnado->oficio->numero_oficio }}</td>

                                        <td>{{ $turnado->oficio->asunto }}</td>

                                        <td>{{ $turnado->turnado_en }}</td>

                                        <td>

                                            <button class="btn btn-primary btn-ver-oficio" data-id="{{ $turnado->oficio->id }}">
                                                Ver
                                            </button>

                                            <form method="POST" action="{{ route('turnados.atender', $turnado) }}" class="d-inline form-atender-turnado">
                                                @csrf

                                                <button type="submit" class="btn btn-sm btn-success">
                                                    Atendido
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="5" class="text-center">
                                            Sin oficios pendientes
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        {{-- LISTOS PARA CERRAR --}}

        @if(auth()->user()->hasPermission('puede_cerrar'))
            <div class="tab-pane fade" id="cerrar" role="tabpanel" aria-labelledby="cerrar-tab">

                <div class="card shadow mb-4">

                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-success">
                            Oficios Listos para Cierre
                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>
                                        <th>ID</th>
                                        <th>Número</th>
                                        <th>Asunto</th>
                                        <th>Acciones</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($listosCerrar as $oficio)

                                        <tr>

                                            <td>{{ $oficio->id }}</td>

                                            <td>{{ $oficio->numero_oficio }}</td>

                                            <td>{{ $oficio->asunto }}</td>

                                            <td>
                                                <button class="btn btn-primary btn-ver-oficio" data-id="{{ $oficio->id }}">
                                                    Ver
                                                </button>

                                                <form method="POST" action="{{ route('oficios.cerrar', $oficio) }}" class="d-inline form-cerrar-oficio">
                                                    @csrf

                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        Cerrar oficio
                                                    </button>

                                                </form>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="4" class="text-center">
                                                No hay oficios listos para cerrar
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>
</div>
   
@endsection

@section('scripts')
<script>

$(document).ready(function () {

    // Tabs de las tablas
    $('#homeTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    let hash = window.location.hash;

    if (hash) {
        $('#homeTabs a[href="' + hash + '"]').tab('show');
    }

    // Modal detalles del oficio
    $('.btn-ver-oficio').on('click', function () {
        Oficios.open($(this).data('id'), false);
    });
});

</script>
@endsection