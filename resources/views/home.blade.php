@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <h1 class="h3 mb-0 text-gray-800">
            Mi Bandeja
        </h1>

    </div>

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

                                <td>
                                    {{ $turnado->oficio->id }}
                                </td>

                                <td>
                                    {{ $turnado->oficio->numero_oficio }}
                                </td>

                                <td>
                                    {{ $turnado->oficio->asunto }}
                                </td>

                                <td>
                                    {{ $turnado->turnado_en }}
                                </td>

                                <td>

                                    <a href="{{ route('oficios.show', $turnado->oficio->id) }}" class="btn btn-sm btn-primary" >
                                        Ver
                                    </a>

                                    <form method="POST" action="{{ route('turnados.atender', $turnado) }}" class="d-inline">
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

@endsection