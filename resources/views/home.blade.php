@extends('layouts.app')

<style>
    #homeTabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        transition: all 0.15s ease;
    }

    #homeTabs .nav-link:hover {
        color: #495057;
    }

    #homeTabs .nav-link.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
    }
</style>

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">

        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Mi Bandeja
            </h1>

            <p class="mb-0 text-muted small">
                Gestión y seguimiento de oficios turnados
            </p>
        </div>

    </div>

    <div class="card border-0 shadow-sm rounded-lg mb-4">

        <div class="card-body p-0">

            <div class="nav oficios-vistas px-3" id="homeTabs" role="tablist">

                <a
                    href="#bandeja"
                    class="nav-link active"
                    data-toggle="tab"
                    role="tab"
                    aria-controls="bandeja"
                    aria-selected="true"
                >
                    <i class="fas fa-inbox mr-1"></i>
                    Bandeja
                    <span class="badge badge-light border ml-1">
                        {{ $turnados->count() }}
                    </span>
                </a>

            </div>

            <div class="tab-content">

                <div
                    class="tab-pane fade show active px-3 pt-3 pb-3"
                    id="bandeja"
                    role="tabpanel"
                >

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle mb-0 w-100"
                            id="tabla-bandeja"
                        >

                            <thead class="bg-light text-muted small text-uppercase">

                                <tr>

                                    <th
                                        class="border-top-0 pl-3 py-3"
                                        style="width: 8%;"
                                    >
                                        ID
                                    </th>

                                    <th
                                        class="border-top-0 py-3"
                                        style="width: 18%;"
                                    >
                                        No. Oficio
                                    </th>

                                    <th
                                        class="border-top-0 py-3"
                                        style="width: 36%;"
                                    >
                                        Asunto
                                    </th>

                                    <th
                                        class="border-top-0 py-3"
                                        style="width: 18%;"
                                    >
                                        Turnado en
                                    </th>

                                    <th
                                        class="border-top-0 text-right pr-3 py-3"
                                        style="width: 20%;"
                                    >
                                        Acciones
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="text-sm">

                                @foreach($turnados as $turnado)

                                    <tr>

                                        <td class="pl-3">
                                            {{ $turnado->oficio->id }}
                                        </td>

                                        <td>
                                            <span class="font-weight-bold text-gray-800">
                                                {{ $turnado->oficio->numero_oficio }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="text-gray-700">
                                                {{ $turnado->oficio->asunto }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="text-muted">
                                                {{ $turnado->turnado_en }}
                                            </span>
                                        </td>

                                        <td class="text-right pr-3">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light text-primary border-0 rounded mr-1 px-2"
                                                title="Ver oficio"
                                                onclick="Oficios.open({{ $turnado->oficio->id }}, false)"
                                            >
                                                <i class="fas fa-eye fa-xs"></i>
                                            </button>

                                            <form
                                                method="POST"
                                                action="{{ route('turnados.atender', $turnado) }}"
                                                class="d-inline form-atender-turnado"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-success rounded px-2"
                                                    title="Marcar como atendido"
                                                >
                                                    <i class="fas fa-check"></i>
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@include('oficios.modals')

@endsection

@section('scripts')

<script>

$(document).ready(function () {

    const params = new URLSearchParams(window.location.search);
    const buscar = params.get('buscar') || '';

    const tablaBandeja = $('#tabla-bandeja').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
        }
    });

    if (buscar) {
        tablaBandeja.search(buscar).draw();
    }

});

</script>

@endsection