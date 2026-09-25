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

    cancelar(oficioId) {
        return $.ajax({
            url: `${window.LaravelBaseUrl}/oficios/${oficioId}/cancelar`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });$('#btnCancelarOficio').toggle(puedeCancelar);
    },

    datatable(filters = {}) {
        return $.ajax({
            url: `${window.LaravelBaseUrl}/oficios/datatable`,
            method: 'GET',
            data: filters
        });
    }

};


/*
|--------------------------------------------------------------------------
| NOTIFICACIONES
|--------------------------------------------------------------------------
*/

window.reintentarNotificaciones = function (notificacionIds) {

    if (
        !Array.isArray(notificacionIds) ||
        notificacionIds.length === 0
    ) {
        return;
    }

    const boton = event.currentTarget;

    $(boton)
        .prop('disabled', true)
        .html(`
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Reenviando...
        `);

    const peticiones = notificacionIds.map(function (notificacionId) {

        return $.ajax({
            url: `${window.LaravelBaseUrl}/notificaciones/${notificacionId}/reintentar`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });

    });

    Promise.allSettled(peticiones)
        .then(function () {
            location.reload();
        });
};


window.copiarLinkNotificaciones = function (notificacionIds, link) {

    if (!link) {

        Alerts.error(
            'Este oficio no tiene un enlace de Drive disponible.'
        );

        return;
    }

    if (
        !Array.isArray(notificacionIds) ||
        notificacionIds.length === 0
    ) {
        return;
    }

    const boton = event.currentTarget;

    $(boton)
        .prop('disabled', true)
        .html(`
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Registrando...
        `);

    const peticiones = notificacionIds.map(function (notificacionId) {

        return $.ajax({
            url: `${window.LaravelBaseUrl}/notificaciones/${notificacionId}/copiar-aviso`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });

    });

    Promise.allSettled(peticiones)
        .then(function () {

            navigator.clipboard.writeText(link)
                .finally(function () {
                    location.reload();
                });

        });
};