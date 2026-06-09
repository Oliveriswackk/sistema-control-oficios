<div class="p-2">

    <h6 class="mb-2 text-primary font-weight-bold">
        Turnar oficio: {{ $oficio->numero_oficio }}
    </h6>

    {{-- COORDINACIONES --}}
    <div class="mb-3">

        <label>Coordinaciones</label>

        <div class="d-flex flex-wrap">

            @foreach($coordinaciones as $coord)

                <div class="custom-control custom-checkbox mr-3 mb-2">

                    <input type="checkbox"
                           class="custom-control-input coord-toggle"
                           id="coord-{{ $coord->id }}"
                           data-id="{{ $coord->id }}">

                    <label class="custom-control-label" for="coord-{{ $coord->id }}">
                        {{ $coord->nombre }}
                    </label>

                </div>

            @endforeach

        </div>
    </div>


    {{-- BLOQUES POR COORDINACIÓN --}}
    @foreach($coordinaciones as $coord)

        <div class="coord-block border rounded p-2 mb-3"
             data-coord="{{ $coord->id }}"
             style="display:none;">

            <h6 class="text-secondary">
                {{ $coord->nombre }}
            </h6>

            <table class="table table-sm">

                <thead>
                    <tr>
                        <th></th>
                        <th>Persona</th>
                        <th>Participación</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($coord->users as $user)

                        <tr class="user-row" data-user="{{ $user->id }}">

                            <td>
                                <input type="checkbox"
                                       class="user-check">
                            </td>

                            <td>
                                {{ $user->name }}
                            </td>

                            <td>
                                <select class="form-control form-control-sm participation-select"
                                        disabled>

                                    @foreach($tiposParticipacion as $tipo)
                                        <option value="{{ $tipo->id }}">
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach

                                </select>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endforeach


    {{-- OBSERVACIÓN --}}
    <div class="mt-3">

        <label>Observación general</label>

        <textarea class="form-control" name="observaciones"></textarea>

    </div>


    {{-- BOTÓN --}}
    <div class="mt-3 text-right">

        <button class="btn btn-primary">
            Guardar turnado
        </button>

    </div>

</div>

@section('scripts')
<script>
    $(document).on('change', '.coord-toggle', function () {

    const id = $(this).data('id');

    const block = $(`.coord-block[data-coord="${id}"]`);

    if ($(this).is(':checked')) {
        block.show();
    } else {
        block.hide();
    }

});


$(document).on('change', '.user-check', function () {

    const row = $(this).closest('tr');

    const select = row.find('.participation-select');

    if ($(this).is(':checked')) {

        row.addClass('table-primary');
        select.prop('disabled', false);

    } else {

        row.removeClass('table-primary');
        select.prop('disabled', true);

    }

});
</script>
@endsection