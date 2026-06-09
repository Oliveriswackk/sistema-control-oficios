<div class="table-responsive">

    <table class="table table-bordered" id="{{ $tableId ?? 'tabla-oficios' }}">

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
                                <button class="btn btn-primary btn-ver-oficio" data-id="{{ $oficio->id }}">
                                    Ver
                                </button>

                                @if($oficio->estado_id != 5)
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalTurnar{{ $oficio->id }}">
                                        Turnar
                                    </button>
                                @else
                                    <span class="badge badge-secondary">
                                        Cerrado
                                    </span>
                                @endif
                                
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

                                        <button type="button"  class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <div class="form-group">

                                            <label>Coordinación</label>

                                            <select name="coordinacion_id" class="form-control" required>

                                                @foreach(\App\Models\Coordinacion::all() as $coord)

                                                    <option value="{{ $coord->id }}">
                                                        {{ $coord->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Persona a cargo</label>

                                            <select name="usuario_id" class="form-control" required>

                                                @foreach(\App\Models\User::all() as $usuario)

                                                    <option value="{{ $usuario->id }}">
                                                        {{ $usuario->name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Tipo participación</label>

                                            <select name="tipo_participacion_id" class="form-control" required>

                                                @foreach(\App\Models\TipoParticipacion::all() as $tipo)

                                                    <option value="{{ $tipo->id }}">
                                                        {{ $tipo->nombre }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="form-group">

                                            <label>Observaciones</label>

                                            <textarea  name="observaciones" class="form-control" rows="3"></textarea>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="submit" class="btn btn-warning">
                                            Turnar
                                        </button>

                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
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