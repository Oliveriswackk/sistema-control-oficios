@extends('layouts.sbadmin')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Oficios
    </h1>

    <button
        class="btn btn-primary"
        data-toggle="modal"
        data-target="#modalCrearOficio"
    >
        Nuevo Oficio
    </button>
</div>

<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Sistema Control de Oficios
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
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($oficios as $oficio)

                        <tr>
                            <td>{{ $oficio->id }}</td>

                            <td>
                                {{ $oficio->numero_oficio }}
                            </td>

                            <td>
                                {{ $oficio->asunto }}
                            </td>

                            <td>
                                {{ $oficio->estado->nombre }}
                            </td>

                            <td>
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalTurnar{{ $oficio->id }}">
                                    Turnar
                                </button>
                            </td>
                            
                        </tr>
                        
                    {{-- MODAL TURNAR --}}
                    <div class="modal fade" id="modalTurnar{{ $oficio->id }}" tabindex="-1" role="dialog">

                        <div class="modal-dialog" role="document">

                            <div class="modal-content">

                                <form
                                    method="POST"
                                    action="{{ route('oficios.turnar', $oficio->id) }}"
                                >

                                    @csrf

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Turnar Oficio
                                        </h5>

                                        <button
                                            type="button"
                                            class="close"
                                            data-dismiss="modal"
                                        >
                                            <span>&times;</span>
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <div class="form-group">

                                            <label>Usuario</label>

                                            <select
                                                name="usuario_id"
                                                class="form-control"
                                                required
                                            >

                                                @foreach(\App\Models\User::all() as $usuario)

                                                    <option value="{{ $usuario->id }}">
                                                        {{ $usuario->name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Coordinación</label>

                                            <select
                                                name="coordinacion_id"
                                                class="form-control"
                                                required
                                            >

                                                @foreach(\App\Models\Coordinacion::all() as $coord)

                                                    <option value="{{ $coord->id }}">
                                                        {{ $coord->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Tipo participación</label>

                                            <select
                                                name="tipo_participacion_id"
                                                class="form-control"
                                                required
                                            >

                                                @foreach(\App\Models\TipoParticipacion::all() as $tipo)

                                                    <option value="{{ $tipo->id }}">
                                                        {{ $tipo->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Observaciones</label>

                                            <textarea
                                                name="observaciones"
                                                class="form-control"
                                                rows="3"
                                            ></textarea>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button
                                            type="submit"
                                            class="btn btn-warning"
                                        >
                                            Turnar
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                        >
                                            Cancelar
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- MODAL CREAR OFICIO --}}
<div
    class="modal fade"
    id="modalCrearOficio"
    tabindex="-1"
    role="dialog"
>

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('oficios.store') }}"
            >

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Crear Oficio
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        <span>&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Número Oficio</label>

                        <input
                            type="text"
                            name="numero_oficio"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Consecutivo</label>

                        <input
                            type="number"
                            name="consecutivo"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Asunto</label>

                        <input
                            type="text"
                            name="asunto"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>

                        <textarea
                            name="descripcion"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>

                    <input
                        type="hidden"
                        name="tipo_oficio_id"
                        value="1"
                    >

                    <input
                        type="hidden"
                        name="estado_id"
                        value="1"
                    >

                    <input
                        type="hidden"
                        name="coordinacion_origen_id"
                        value="1"
                    >

                    <input
                        type="hidden"
                        name="fecha_oficio"
                        value="{{ now()->toDateString() }}"
                    >

                    <input
                        type="hidden"
                        name="fecha_recepcion"
                        value="{{ now()->toDateString() }}"
                    >

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Guardar
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Cancelar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection