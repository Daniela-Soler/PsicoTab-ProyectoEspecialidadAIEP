/**
 * resultados.js
 * - Carga los datos reales del informe desde php/informe_obtener.php según ?id_evaluacion=...
 * - Muestra solo las tarjetas de los instrumentos aplicados.
 * - Permite actualizar las observaciones clínicas vía fetch hacia php/informe_actualizar.php.
 */

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const idEvaluacion = params.get('id_evaluacion');

    if (!idEvaluacion) {
        mostrarErrorInforme('No se especificó un ID de evaluación válido en la URL.');
        return;
    }

    cargarInforme(idEvaluacion);
    configurarFormularioObservaciones(idEvaluacion);
});

function mostrarErrorInforme(mensaje) {
    const alerta = document.getElementById('alerta-error-informe');
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');
}

function obtenerEstilosSemaforo(nivel) {
    const texto = (nivel || '').toLowerCase();
    if (texto.includes('grave') || texto.includes('severa') || texto.includes('extrema') || texto.includes('positivo')) {
        return { badge: 'semaforo-severo', barra: 'bg-danger' };
    }
    if (texto.includes('moderada')) {
        return { badge: 'semaforo-moderado', barra: 'bg-warning' };
    }
    return { badge: 'semaforo-leve', barra: 'bg-info' };
}

async function cargarInforme(idEvaluacion) {
    try {
        const res = await fetch(`../php/informe_obtener.php?id_evaluacion=${encodeURIComponent(idEvaluacion)}`);
        if (res.status === 401) {
            window.location.href = 'login.php';
            return;
        }

        const data = await res.json();
        if (!res.ok || !data.success || !data.datos) {
            throw new Error(data.error || 'No se pudo cargar el informe.');
        }

        const d = data.datos;

        // 1. Datos del paciente y evaluación
        document.getElementById('res-rut').textContent = d.RUT ?? '-';
        document.getElementById('res-nombre').textContent = d.Nombre_Paciente ?? '-';
        document.getElementById('res-fecha').textContent = d.Fecha_hora ?? '-';
        document.getElementById('res-contexto').textContent = d.ContextoInstitucional ?? '-';
        document.getElementById('texto-observaciones').value = d.Observaciones ?? '';

        // 2. Alerta crítica Ítem 9 PHQ-9
        if (d.AlertaAutolesiva == 1 || d.AlertaAutolesiva === true) {
            document.getElementById('banner-alerta-critica').classList.remove('d-none');
        }

        // 3. Tarjeta PHQ-9
        if (d.PuntajeTotal_PHQ9 !== null && d.PuntajeTotal_PHQ9 !== undefined) {
            const pts = Number(d.PuntajeTotal_PHQ9);
            const pct = Math.min(100, Math.round((pts / 27) * 100));
            const estilos = obtenerEstilosSemaforo(d.Nivel_PHQ9);

            document.getElementById('phq9-puntaje').textContent = pts;
            const badge = document.getElementById('phq9-nivel');
            badge.textContent = d.Nivel_PHQ9 ?? '-';
            badge.className = `badge-severidad ${estilos.badge} d-inline-block`;

            const barra = document.getElementById('phq9-barra');
            barra.className = `progress-bar ${estilos.barra}`;
            barra.style.width = `${pct}%`;

            document.getElementById('card-phq9').classList.remove('d-none');
        }

        // 4. Tarjeta GAD-7
        if (d.PuntajeTotal_GAD7 !== null && d.PuntajeTotal_GAD7 !== undefined) {
            const pts = Number(d.PuntajeTotal_GAD7);
            const pct = Math.min(100, Math.round((pts / 21) * 100));
            const estilos = obtenerEstilosSemaforo(d.Nivel_GAD7);

            document.getElementById('gad7-puntaje').textContent = pts;
            const badge = document.getElementById('gad7-nivel');
            badge.textContent = d.Nivel_GAD7 ?? '-';
            badge.className = `badge-severidad ${estilos.badge} d-inline-block`;

            const barra = document.getElementById('gad7-barra');
            barra.className = `progress-bar ${estilos.barra}`;
            barra.style.width = `${pct}%`;

            document.getElementById('card-gad7').classList.remove('d-none');
        }

        // 5. Tarjeta ASRS v1.1
        if (d.Puntaje_ASRS !== null && d.Puntaje_ASRS !== undefined) {
            const pts = Number(d.Puntaje_ASRS);
            const pct = Math.min(100, Math.round((pts / 6) * 100));
            const estilos = obtenerEstilosSemaforo(d.Resultado_ASRS);

            document.getElementById('asrs-puntaje').textContent = pts;
            const badge = document.getElementById('asrs-nivel');
            badge.textContent = d.Resultado_ASRS === 'Positivo' ? 'Sospecha Positiva' : (d.Resultado_ASRS ?? '-');
            badge.className = `badge-severidad ${estilos.badge} d-inline-block`;

            const barra = document.getElementById('asrs-barra');
            barra.className = `progress-bar ${estilos.barra}`;
            barra.style.width = `${pct}%`;

            document.getElementById('card-asrs').classList.remove('d-none');
        }

        // 6. Tarjeta WHODAS 2.0
        if (d.Porcentaje_WHODAS !== null && d.Porcentaje_WHODAS !== undefined) {
            const pct = Math.min(100, Math.round(Number(d.Porcentaje_WHODAS)));
            const estilos = obtenerEstilosSemaforo(d.Nivel_WHODAS);

            document.getElementById('whodas-puntaje').textContent = `${pct}%`;
            const badge = document.getElementById('whodas-nivel');
            badge.textContent = d.Nivel_WHODAS ?? '-';
            badge.className = `badge-severidad ${estilos.badge} d-inline-block`;

            const barra = document.getElementById('whodas-barra');
            barra.className = `progress-bar ${estilos.barra}`;
            barra.style.width = `${pct}%`;

            document.getElementById('card-whodas').classList.remove('d-none');
        }

    } catch (error) {
        console.error('Error al cargar informe:', error);
        mostrarErrorInforme('Error: ' + error.message);
    }
}

function configurarFormularioObservaciones(idEvaluacion) {
    const form = document.getElementById('form-observaciones');
    const btn = document.getElementById('btn-guardar-obs');
    const alerta = document.getElementById('alerta-observacion');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Guardando...`;

        const formData = new FormData();
        formData.append('id_evaluacion', idEvaluacion);
        formData.append('observaciones', document.getElementById('texto-observaciones').value.trim());

        try {
            const res = await fetch('../php/informe_actualizar.php', {
                method: 'POST',
                body: formData
            });

            if (res.status === 401) {
                window.location.href = 'login.php';
                return;
            }

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.error || 'No se pudieron actualizar las observaciones.');
            }

            alerta.className = 'alert alert-success py-2 px-3 small';
            alerta.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${data.mensaje || 'Observaciones guardadas correctamente.'}`;
            alerta.classList.remove('d-none');

        } catch (error) {
            alerta.className = 'alert alert-danger py-2 px-3 small';
            alerta.textContent = 'Error: ' + error.message;
            alerta.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-save me-1"></i> Guardar / Actualizar Observaciones`;
        }
    });
}