/**
 * form-validation.js
 * - Control de completitud estricto según pruebas activas.
 * - Resaltado visual (.question-incomplete) de ítems omitidos.
 * - Contadores de progreso en tiempo real por cada instrumento.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-evaluacion');
    if (!form) return;

    // Configuración de cantidades de ítems por instrumento
    const totalItems = {
        phq9: 9,
        gad7: 7,
        asrs: 6,
        whodas: 12
    };

    // Inicializar listeners en los inputs de radio para actualizar progreso y limpiar alertas
    document.querySelectorAll('.tab-pane input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const card = e.target.closest('.question-card');
            if (card) {
                card.classList.remove('question-incomplete');
            }
            actualizarProgresoInstrumento(obtenerPrefijo(e.target.name));
        });
    });

    // Actualizar contadores iniciales
    ['phq9', 'gad7', 'asrs', 'whodas'].forEach(actualizarProgresoInstrumento);

    // Sobrescribir la función de envío global
    window.enviarFormulario = function() {
        if (!validarFormularioCompleto()) {
            return;
        }

        const btns = document.querySelectorAll('.btn-finalizar');
        btns.forEach(b => {
            b.disabled = true;
            b.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Procesando...`;
        });
        form.submit();
    };

    function obtenerPrefijo(inputName) {
        return inputName.split('_')[0];
    }

    function actualizarProgresoInstrumento(prefix) {
        if (!totalItems[prefix]) return;

        const max = totalItems[prefix];
        let contestadas = 0;

        for (let i = 1; i <= max; i++) {
            const checked = form.querySelector(`input[name="${prefix}_${i}"]:checked`);
            if (checked) contestadas++;
        }

        const badgeProgreso = document.getElementById(`progreso-${prefix}`);
        if (badgeProgreso) {
            badgeProgreso.textContent = `${contestadas}/${max} respondidas`;
            if (contestadas === max) {
                badgeProgreso.className = 'badge bg-success-subtle text-success';
            } else {
                badgeProgreso.className = 'badge bg-secondary-subtle text-dark';
            }
        }
    }

    function validarFormularioCompleto() {
        // 1. Validar datos del paciente
        const rutInput = document.getElementById('paciente_rut');
        const nombreInput = document.getElementById('paciente_nombre');
        const nacInput = document.getElementById('paciente_nacimiento');
        const sexoInput = document.getElementById('paciente_sexo');

        if (!rutInput.value || !nombreInput.value || !nacInput.value || !sexoInput.value) {
            alert('Por favor complete la ficha de identificación del paciente antes de procesar.');
            irATab('paciente-tab');
            return false;
        }

        // 2. Validar instrumentos seleccionados
        const pruebasActivas = window.getPruebasSeleccionadas ? window.getPruebasSeleccionadas() : [];
        if (pruebasActivas.length === 0) {
            alert('Debe seleccionar al menos un instrumento para evaluar.');
            irATab('paciente-tab');
            return false;
        }

        // 3. Validar que cada pregunta activa esté contestada
        for (const prueba of pruebasActivas) {
            const max = totalItems[prueba];
            let primerFaltante = null;

            for (let i = 1; i <= max; i++) {
                const name = `${prueba}_${i}`;
                const respondida = form.querySelector(`input[name="${name}"]:checked`);
                const card = document.getElementById(`q-${prueba}-${i}`) || form.querySelector(`input[name="${name}"]`).closest('.question-card');

                if (!respondida) {
                    if (card) card.classList.add('question-incomplete');
                    if (!primerFaltante) primerFaltante = card;
                } else {
                    if (card) card.classList.remove('question-incomplete');
                }
            }

            if (primerFaltante) {
                irATab(`${prueba}-tab`);
                primerFaltante.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert(`Faltan reactivos por responder en la prueba seleccionada. Se han resaltado en rojo.`);
                return false;
            }
        }

        return true;
    }
});