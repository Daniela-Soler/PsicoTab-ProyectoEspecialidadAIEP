/**
 * form-validation.js
 * - Control de completitud estricto según pruebas activas.
 * - Resaltado visual (.question-incomplete) de ítems omitidos.
 * - Contadores de progreso en tiempo real por cada instrumento.
 * - Envío vía fetch + JSON a evaluacion_guardar.php.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-evaluacion');
    if (!form) return;
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        window.enviarFormulario();
    });

    const totalItems = {
        phq9: 9,
        gad7: 7,
        asrs: 6,
        whodas: 12
    };

    document.querySelectorAll('.tab-pane input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const card = e.target.closest('.question-card');
            if (card) {
                card.classList.remove('question-incomplete');
            }
            actualizarProgresoInstrumento(obtenerPrefijo(e.target.name));
        });
    });

    ['phq9', 'gad7', 'asrs', 'whodas'].forEach(actualizarProgresoInstrumento);

    window.enviarFormulario = async function() {
        if (!validarFormularioCompleto()) {
            return;
        }

        const btns = document.querySelectorAll('.btn-finalizar');
        btns.forEach(b => {
            b.disabled = true;
            b.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Procesando...`;
        });

        const idPacienteInput = document.getElementById('paciente_id');
        const payload = {
            paciente: {
                id_paciente: idPacienteInput && idPacienteInput.value ? parseInt(idPacienteInput.value, 10) : null,
                rut: document.getElementById('paciente_rut').value.trim(),
                nombre: document.getElementById('paciente_nombre').value.trim(),
                fecha_nacimiento: document.getElementById('paciente_nacimiento').value,
                sexo: document.getElementById('paciente_sexo').value
            },
            evaluacion: {
                contexto: document.getElementById('paciente_motivo').value,
                observaciones: document.getElementById('observaciones').value.trim()
            },
            tests: {}
        };

        const pruebasActivas = window.getPruebasSeleccionadas ? window.getPruebasSeleccionadas() : [];
        for (const prueba of pruebasActivas) {
            const max = totalItems[prueba];
            const respuestas = [];
            for (let i = 1; i <= max; i++) {
                const checked = form.querySelector(`input[name="${prueba}_${i}"]:checked`);
                respuestas.push(parseInt(checked.value, 10));
            }
            payload.tests[prueba] = respuestas;
        }

        try {
            const response = await fetch('../php/evaluacion_guardar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'No se pudo guardar la evaluación.');
            }

            window.location.href = `resultados.php?id_evaluacion=${encodeURIComponent(data.id_evaluacion)}`;

        } catch (error) {
            alert('Error: ' + error.message);
            btns.forEach(b => {
                b.disabled = false;
                b.innerHTML = `<i class="bi bi-cpu-fill me-2"></i> Procesar Baremación y Reporte`;
            });
        }
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
        const rutInput = document.getElementById('paciente_rut');
        const nombreInput = document.getElementById('paciente_nombre');
        const nacInput = document.getElementById('paciente_nacimiento');
        const sexoInput = document.getElementById('paciente_sexo');

        if (!rutInput.value || !nombreInput.value || !nacInput.value || !sexoInput.value) {
            alert('Por favor complete la ficha de identificación del paciente antes de procesar.');
            window.irATab('paciente-tab');
            return false;
        }

        const pruebasActivas = window.getPruebasSeleccionadas ? window.getPruebasSeleccionadas() : [];
        if (pruebasActivas.length === 0) {
            alert('Debe seleccionar al menos un instrumento para evaluar.');
            window.irATab('paciente-tab');
            return false;
        }

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
                window.irATab(`${prueba}-tab`);
                primerFaltante.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert(`Faltan reactivos por responder en la prueba seleccionada. Se han resaltado en rojo.`);
                return false;
            }
        }

        return true;
    }
});