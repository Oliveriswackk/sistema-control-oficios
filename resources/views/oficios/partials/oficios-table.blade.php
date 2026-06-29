{{-- TABLA OFICIOS --}}

<table class="table table-bordered table-hover w-100" id="{{ $tableId ?? 'tabla-oficios' }}">

    <thead>
        <tr>
            <th>ID</th>
            <th>No. Oficio</th>
            <th>Asunto</th>
            <th>Tags</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>

        @foreach($oficios as $oficio)

            @include('oficios.partials.oficio-row')

        @endforeach

    </tbody>
</table>