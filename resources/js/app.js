import './bootstrap';

import Alpine from 'alpinejs';

import * as Alerts from './alerts';

window.Alerts = Alerts;

console.log('APP JS CARGADO');

window.Alpine = Alpine;

Alpine.start();