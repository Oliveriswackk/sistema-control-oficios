<div class="card border-0 shadow-sm rounded-lg mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table table-hover align-middle mb-0 w-100"
                id="{{ $tableId ?? 'tabla-oficios' }}"
            >
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-top-0 pl-3 py-3" style="width: 8%;">ID</th>
                        <th class="border-top-0 py-3" style="width: 16%;">No. Oficio</th>
                        <th class="border-top-0 py-3" style="width: 34%;">Asunto</th>
                        <th class="border-top-0 py-3" style="width: 18%;">Tags</th>
                        <th class="border-top-0 py-3" style="width: 12%;">Estado</th>
                        <th class="border-top-0 text-right pr-3 py-3" style="width: 12%;">Acciones</th>
                    </tr>
                </thead>

                <tbody class="text-sm">
                    @foreach($oficios as $oficio)
                        @include('oficios.partials.oficio-row')
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>