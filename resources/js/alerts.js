import Swal from 'sweetalert2';

// ALERTA BASE
function alertBase(icon, title, text = '') {
    return Swal.fire({
        icon,
        title,
        text,
        confirmButtonText: 'OK'
    });
}

// SUCCESS
export function success(message) {
    return alertBase('success', 'Éxito', message);
}

// ERROR
export function error(message) {
    return alertBase('error', 'Error', message);
}

// WARNING
export function warning(message) {
    return alertBase('warning', 'Atención', message);
}

// CONFIRM (importante)
export function confirm(message) {
    return Swal.fire({
        icon: 'warning',
        title: '¿Estás seguro?',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });
}