/**
 * Sistema global de sugerencias
 * Fuentes: usuarios internos + Directorio
 */

window.Sugerencias = {

    buscarPersonas: function (termino) {
        return fetch(
            `${window.LaravelBaseUrl}/api/sugerencias/personas?q=${encodeURIComponent(termino)}`
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            });
    },

    persona: function (config) {

        const input = document.querySelector(config.input);
        const cargo = document.querySelector(config.cargo);
        const dependencia = document.querySelector(config.dependencia);

        if (!input) return;

        let resultados = [];
        let indiceSeleccionado = -1;

        const contenedor = document.createElement('div');
        contenedor.className = 'sugerencias-personas';
        input.parentElement.style.position = 'relative';
        input.parentElement.appendChild(contenedor);

        input.addEventListener('input', function () {

            const termino = this.value.trim();

            indiceSeleccionado = -1;

            if (termino.length < 2) {
                ocultar();
                return;
            }

            Sugerencias.buscarPersonas(termino)
                .then(data => {

                    resultados = data;

                    if (!resultados.length) {
                        ocultar();
                        return;
                    }

                    mostrar(resultados);
                })
                .catch(error => {
                    console.error('Error buscando personas:', error);
                    ocultar();
                });
        });

        input.addEventListener('keydown', function (event) {

            if (contenedor.style.display === 'none') return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                indiceSeleccionado = Math.min(
                    indiceSeleccionado + 1,
                    resultados.length - 1
                );
                actualizarSeleccion();
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                indiceSeleccionado = Math.max(
                    indiceSeleccionado - 1,
                    0
                );
                actualizarSeleccion();
            }

            if (event.key === 'Enter' && indiceSeleccionado >= 0) {
                event.preventDefault();
                seleccionar(resultados[indiceSeleccionado]);
            }

            if (event.key === 'Escape') {
                ocultar();
            }
        });

        document.addEventListener('click', function (event) {

            if (!input.parentElement.contains(event.target)) {
                ocultar();
            }
        });

        function mostrar(personas) {

            contenedor.innerHTML = '';

            personas.forEach((persona, index) => {

                const opcion = document.createElement('button');

                opcion.type = 'button';
                opcion.className = 'sugerencia-persona';
                opcion.dataset.index = index;

                opcion.innerHTML = `
                    <div class="sugerencia-persona-nombre">
                        ${escapeHtml(persona.nombre)}
                    </div>
                    <div class="sugerencia-persona-detalle">
                        ${escapeHtml(persona.cargo || '')}
                        ${persona.cargo && persona.dependencia ? ' · ' : ''}
                        ${escapeHtml(persona.dependencia || '')}
                    </div>
                `;

                opcion.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    seleccionar(persona);
                });

                contenedor.appendChild(opcion);
            });

            contenedor.style.display = 'block';
        }

        function actualizarSeleccion() {

            contenedor
                .querySelectorAll('.sugerencia-persona')
                .forEach((elemento, index) => {
                    elemento.classList.toggle(
                        'active',
                        index === indiceSeleccionado
                    );
                });
        }

        function seleccionar(persona) {

            input.value = persona.nombre;

            if (cargo) {
                cargo.value = persona.cargo || '';
            }

            if (dependencia) {
                dependencia.value = persona.dependencia || '';
            }

            ocultar();

            input.dispatchEvent(new Event('change', {
                bubbles: true
            }));
        }

        function ocultar() {
            contenedor.style.display = 'none';
            indiceSeleccionado = -1;
        }

        function escapeHtml(valor) {
            const div = document.createElement('div');
            div.textContent = valor;
            return div.innerHTML;
        }
    }

};


$(document).ready(function () {

    Sugerencias.persona({
        input: 'input[name="destinatario_nombre"]',
        cargo: 'input[name="destinatario_cargo"]',
        dependencia: '#destinatario_dependencia'
    });

    Sugerencias.persona({
        input: 'input[name="remitente_nombre"]',
        cargo: 'input[name="remitente_cargo"]',
        dependencia: '#remitente_dependencia'
    });

});