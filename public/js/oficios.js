window.OficiosApi = {

    getTurnarModal(oficioId) {

        return $.ajax({
            url: `{{ url('oficios') }}/${oficioId}/turnar`,
            method: 'GET'
        });

    },

    update(oficioId, data) {
        return $.ajax({

            url: `{{ url('oficios') }}/${oficioId}`,
            method: 'POST',
            data: {
                ...data,
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    datatable(filters = {}) {

        return $.ajax({
            url: "{{ url('oficios/datatable') }}",

            method: 'GET',

            data: filters

        });

    }

};