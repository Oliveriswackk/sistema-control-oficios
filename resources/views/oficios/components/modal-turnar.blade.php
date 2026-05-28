<div class="modal fade" id="turnarModal" tabindex="-1">

    <div class="modal-dialog">

        <form method="POST" action="/oficios/{{ $oficio->id }}/turnar">

            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Turnar oficio</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="oficio_id" id="oficio_id">

                    <div class="mb-2">
                        <label>Usuario</label>
                        <select name="usuario_id" class="form-control">
                            @foreach(App\Models\User::all() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Coordinación</label>
                        <select name="coordinacion_id" class="form-control">
                            @foreach(App\Models\Coordinacion::all() as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Participación</label>
                        <select name="tipo_participacion_id" class="form-control">
                            @foreach(App\Models\TipoParticipacion::all() as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">
                        Turnar
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>