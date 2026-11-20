/**
 * evaluacion.js
 * - Navegación dinámica de pestañas según instrumentos seleccionados.
 * - Alerta clínica modal para Ítem 9 del PHQ-9.
 * - Alternancia Paciente Nuevo / Antiguo y búsqueda real en BD.
 */

const ordenPruebas = ['phq9', 'gad7', 'asrs', 'whodas'];

// Funciones globales (usadas por el HTML y por form-validation.js)
window.getPruebasSeleccionadas = function () {
    const checks = document.querySelectorAll('.check-instrumento:checked');
    return Array.from(checks).map(c => c.value);
};

window.irATab = function (tabId) {
    const triggerEl = document.getElementById(tabId);
    if (triggerEl) {
        const tabInstance = new bootstrap.Tab(triggerEl);
        tabInstance.show();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    function actualizarTabsVisibles() {
        const seleccionadas = window.getPruebasSeleccionadas();
        document.getElementById('contador-pruebas').textContent = `${seleccionadas.length} seleccionada(s)`;

        ordenPruebas.forEach(p => {
            const navItem = document.getElementById(`nav-item-${p}`);
            if (navItem) {
                navItem.style.display = seleccionadas.includes(p) ? 'block' : 'none';
            }
        });

        const btnIniciar = document.getElementById('btn-iniciar-pruebas');
        if (seleccionadas.length === 0) {
            btnIniciar.disabled = true;
            btnIniciar.textContent = 'Seleccione al menos 1 prueba';
        } else {
            btnIniciar.disabled = false;
            btnIniciar.innerHTML = `Iniciar Evaluación (${seleccionadas.length}) <i class="bi bi-arrow-right ms-1"></i>`;
        }
    }

    document.querySelectorAll('.check-instrumento').forEach(check => {
        check.addEventListener('change', actualizarTabsVisibles);
    });

    document.getElementById('btn-iniciar-pruebas').addEventListener('click', () => {
        const seleccionadas = window.getPruebasSeleccionadas();
        if (seleccionadas.length > 0) {
            window.irATab(`${seleccionadas[0]}-tab`);
        }
    });

    document.querySelectorAll('.btn-siguiente').forEach(btn => {
        btn.addEventListener('click', () => {
            const current = btn.getAttribute('data-current');
            const seleccionadas = window.getPruebasSeleccionadas();
            const currentIndex = seleccionadas.indexOf(current);

            if (currentIndex !== -1 && currentIndex < seleccionadas.length - 1) {
                const siguiente = seleccionadas[currentIndex + 1];
                window.irATab(`${siguiente}-tab`);
            } else if (typeof window.enviarFormulario === 'function') {
                window.enviarFormulario();
            }
        });
    });

    document.querySelectorAll('.btn-anterior').forEach(btn => {
        btn.addEventListener('click', () => {
            const current = btn.getAttribute('data-current');
            const seleccionadas = window.getPruebasSeleccionadas();
            const currentIndex = seleccionadas.indexOf(current);

            if (currentIndex > 0) {
                const anterior = seleccionadas[currentIndex - 1];
                window.irATab(`${anterior}-tab`);
            } else {
                window.irATab('paciente-tab');
            }
        });
    });

    // Modal de alerta clínica para Ítem 9 PHQ-9
    document.querySelectorAll('.item-critico').forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (parseInt(e.target.value, 10) > 0) {
                const alertModal = new bootstrap.Modal(document.getElementById('modalAlertaClinica'));
                alertModal.show();
            }
        });
    });

    // Alternar bloque de búsqueda de paciente antiguo
    const radioNuevo = document.getElementById('tipo_nuevo');
    const radioAntiguo = document.getElementById('tipo_antiguo');
    const bloqueAntiguo = document.getElementById('bloque-buscar-antiguo');
    const contenedorResultados = document.getElementById('resultados-busqueda-paciente');

    function toggleTipoPaciente() {
        const esAntiguo = radioAntiguo.checked;
        bloqueAntiguo.classList.toggle('d-none', !esAntiguo);
        document.getElementById('paciente_rut').readOnly = esAntiguo;

        if (!esAntiguo) {
            document.getElementById('paciente_id').value = '';
            document.getElementById('paciente_rut').value = '';
            document.getElementById('paciente_nombre').value = '';
            document.getElementById('paciente_nacimiento').value = '';
            document.getElementById('paciente_sexo').value = '';
            if (contenedorResultados) contenedorResultados.classList.add('d-none');
        }
    }

    radioNuevo.addEventListener('change', toggleTipoPaciente);
    radioAntiguo.addEventListener('change', toggleTipoPaciente);

    // Búsqueda real de paciente antiguo en BD
    document.getElementById('btn-cargar-paciente').addEventListener('click', async () => {
        const query = document.getElementById('buscar_paciente_bd').value.trim();

        if (!query) {
            alert('Ingrese un RUT o apellido para buscar.');
            return;
        }

        try {
            const res = await fetch(`../php/paciente_buscar.php?busqueda=${encodeURIComponent(query)}`);
            if (res.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            const data = await res.json();
            const pacientes = data.datos || [];

            if (!Array.isArray(pacientes) || pacientes.length === 0) {
                contenedorResultados.innerHTML = `<div class="list-group-item text-muted small">No se encontraron pacientes registrados con "${query}".</div>`;
                contenedorResultados.classList.remove('d-none');
                return;
            }

            if (pacientes.length === 1) {
                rellenarDatosPaciente(pacientes[0]);
                contenedorResultados.classList.add('d-none');
            } else {
                contenedorResultados.innerHTML = '';
                pacientes.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action small d-flex justify-content-between align-items-center';
                    btn.innerHTML = `<span><strong>${p.Nombre}</strong></span> <code>${p.RUT}</code>`;
                    btn.addEventListener('click', () => {
                        rellenarDatosPaciente(p);
                        contenedorResultados.classList.add('d-none');
                    });
                    contenedorResultados.appendChild(btn);
                });
                contenedorResultados.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Error al buscar paciente:', error);
            alert('Error al conectar con el servidor para buscar el paciente.');
        }
    });

    function rellenarDatosPaciente(p) {
        document.getElementById('paciente_id').value = p.ID_Paciente ?? '';
        document.getElementById('paciente_rut').value = p.RUT ?? '';
        document.getElementById('paciente_nombre').value = p.Nombre ?? '';
        document.getElementById('paciente_nacimiento').value = p.Fecha_Nacimiento ?? '';
        document.getElementById('paciente_sexo').value = p.Sexo ?? '';
    }

    actualizarTabsVisibles();
});