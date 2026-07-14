<style>

    .coord-item{

        cursor:pointer;

        transition:.15s;

    }

    .coord-item:hover{

        background:#f8f9fc;

    }

    .coord-item.selected{

        background:#e8f2ff;

        border:2px solid #4e73df !important;

    }

</style>


<form id="formTurnar"  method="POST" action="{{ route('oficios.turnar', $oficio->id) }}">
    @csrf

    <div class="p-2">

        <h6 class="mb-2 text-primary font-weight-bold">
            Turnar oficio: {{ $oficio->numero_oficio }}
        </h6>

        {{-- LISTADO DE COORDINACIONES --}}
        <div id="lista-coordinaciones">

            @foreach($coordinaciones as $coord)

                <div
                    class="coord-item border rounded px-3 py-2 mb-2 d-flex align-items-center justify-content-between"
                    data-coord="{{ $coord->id }}">

                    <div>

                        <strong>
                            {{ $coord->nombre }}
                        </strong>

                    </div>

                    <div style="width:250px;">

                        <select
                            class="form-control participacion-select"
                            name="participacion[{{ $coord->id }}]"
                            disabled>

                            <option value="">
                                Seleccione participación
                            </option>

                            @foreach($tiposParticipacion as $tipo)

                                <option value="{{ $tipo->id }}">
                                    {{ $tipo->nombre }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            @endforeach

        </div>

        {{-- OBSERVACIÓN --}}
        <div class="mt-3">

            <label>Observación general</label>

            <textarea class="form-control" name="observaciones"></textarea>

        </div>

    </div>

</form>


<script>

$(function(){

    $('.coord-item').on('click',function(e){

        if($(e.target).is('select') || $(e.target).is('option')){
            return;
        }

        let fila=$(this);

        fila.toggleClass('selected');

        let select=fila.find('select');

        if(fila.hasClass('selected')){

            select.prop('disabled',false);

        }else{

            select.prop('disabled',true);

            select.val('');

        }

    });

});

</script>