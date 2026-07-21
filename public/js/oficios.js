window.OficiosApi = {

    getTurnarModal(oficioId) {
        return $.ajax({
            url: `${window.LaravelBaseUrl}/oficios/${oficioId}/turnar`,
            method: 'GET'
        });
    },

    update(oficioId, data) {
        return $.ajax({
            url: `${window.LaravelBaseUrl}/oficios/${oficioId}`,
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
            url: `${window.LaravelBaseUrl}/oficios/datatable`,
            method: 'GET',
            data: filters
        });
    }

};