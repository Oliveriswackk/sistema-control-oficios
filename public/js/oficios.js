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

            let copiaExitosa = false;

            if (
                navigator.clipboard &&
                window.isSecureContext
            ) {
                copiaExitosa = navigator.clipboard.writeText(link);
            } else {
                const textarea = document.createElement('textarea');

                textarea.value = link;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';

                document.body.appendChild(textarea);

                textarea.focus();
                textarea.select();

                try {
                    copiaExitosa = document.execCommand('copy');
                } catch (error) {
                    copiaExitosa = false;
                }

                textarea.remove();
            }

            if (copiaExitosa instanceof Promise) {
                copiaExitosa
                    .then(function () {
                        location.reload();
                    })
                    .catch(function (error) {
                        console.error('No se pudo copiar el enlace:', error);
                        location.reload();
                    });
            } else if (copiaExitosa) {
                location.reload();
            } else {
                Alerts.error(
                    'No se pudo copiar el enlace al portapapeles.'
                );

                location.reload();
            }
        });
};