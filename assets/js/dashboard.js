// Función auxiliar para prevenir XSS al insertar texto en el DOM
function escaparHTML(texto) {
    if (texto === null || texto === undefined) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// 1. Cargar métricas de las tarjetas
async function cargarEstadisticas() {
    try {
        const res = await fetch('../php/procesar_dashboard_estadisticas.php');
        if (res.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const data = await res.json();
        document.getElementById('stat-total-evaluaciones').textContent = data.TotalEvaluaciones ?? 0;
        document.getElementById('stat-alertas-criticas').textContent = data.AlertasCriticas ?? 0;
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

// 2. Cargar registros de la tabla (con filtro opcional)
async function cargarRegistros(busqueda = '') {
    const tbody = document.getElementById('tbody-registros');
    try {
        const url = `../php/procesar_dashboard_registros.php?busqueda=${encodeURIComponent(busqueda)}`;
        const res = await fetch(url);
        if (res.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const registros = await res.json();

        if (!Array.isArray(registros) || registros.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No se encontraron evaluaciones registradas.
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = registros.map(reg => {
            const idEval   = reg.ID_Evaluacion;
            const rut      = reg.Identificador ?? '-';
            const nombre   = reg.Paciente ?? '-';
            const fecha    = reg.Fecha ?? '-';
            const baterias = reg.BateriasAplicadas || 'Sin detalle';
            const alerta   = reg.AlertaPHQ9 ?? 'N/A';

            let badgeAlerta = `<span class="badge bg-secondary-subtle text-muted">N/A</span>`;
            if (alerta === 'Crítica') {
                badgeAlerta = `<span class="badge bg-danger">Crítica</span>`;
            } else if (alerta === 'Negativa') {
                badgeAlerta = `<span class="badge bg-success-subtle text-success">Negativa</span>`;
            }

            return `
                <tr>
                    <td class="col-rut"><code>${escaparHTML(rut)}</code></td>
                    <td class="col-nombre">${escaparHTML(nombre)}</td>
                    <td>${escaparHTML(fecha)}</td>
                    <td><span class="badge bg-secondary-subtle text-dark">${escaparHTML(baterias)}</span></td>
                    <td>${badgeAlerta}</td>
                    <td class="text-end">
                        <a href="resultados.php?id_evaluacion=${encodeURIComponent(idEval)}" class="btn btn-sm btn-outline-primary">
                            Ver Informe
                        </a>
                    </td>
                </tr>`;
        }).join('');

    } catch (error) {
        console.error('Error al cargar registros:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    Error al cargar los registros desde el servidor.
                </td>
            </tr>`;
    }
}

// 3. Ejecutar carga inicial y configurar buscador con debounce (300ms)
document.addEventListener('DOMContentLoaded', () => {
    cargarEstadisticas();
    cargarRegistros();

    let timeoutBusqueda;
    document.getElementById('filtro-registros').addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        const termino = this.value.trim();
        timeoutBusqueda = setTimeout(() => {
            cargarRegistros(termino);
        }, 300);
    });
});