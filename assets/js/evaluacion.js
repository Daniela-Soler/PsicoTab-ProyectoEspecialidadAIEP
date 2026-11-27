const ordenPruebas = ['phq9', 'gad7', 'asrs', 'whodas'];

function getPruebasSeleccionadas() {
    const checks = document.querySelectorAll('.check-instrumento:checked');
    return Array.from(checks).map(c => c.value);
}

function actualizarTabsVisibles() {
    const seleccionadas = getPruebasSeleccionadas();
    const contador = document.getElementById('contador-pruebas');
    if (contador) {
        contador.textContent = `${seleccionadas.length} seleccionada(s)`;
    }

    ordenPruebas.forEach(p => {
        const navItem = document.getElementById(`nav-item-${p}`);
        if (navItem) {
            navItem.style.display = seleccionadas.includes(p) ? 'block' : 'none';
        }
    });

    const btnIniciar = document.getElementById('btn-iniciar-pruebas');
    if (btnIniciar) {
        if (seleccionadas.length === 0) {
            btnIniciar.disabled = true;
            btnIniciar.textContent = 'Seleccione al menos 1 prueba';
        } else {
            btnIniciar.disabled = false;
            btnIniciar.innerHTML = `Iniciar Evaluación (${seleccionadas.length}) <i class="bi bi-arrow-right ms-1"></i>`;
        }
    }
}

function irATab(tabId) {
    const triggerEl = document.getElementById(tabId);
    if (triggerEl) {
        const tabInstance = new bootstrap.Tab(triggerEl);
        tabInstance.show();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function enviarFormulario() {
    const form = document.getElementById('form-evaluacion');
    const btns = document.querySelectorAll('.btn-finalizar');
    btns.forEach(b => {
        b.disabled = true;
        b.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Procesando...`;
    });
    if (form) {
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.check-instrumento').forEach(check => {
        check.addEventListener('change', actualizarTabsVisibles);
    });

    const btnIniciar = document.getElementById('btn-iniciar-pruebas');
    if (btnIniciar) {
        btnIniciar.addEventListener('click', () => {
            const seleccionadas = getPruebasSeleccionadas();
            if (seleccionadas.length > 0) {
                irATab(`${seleccionadas[0]}-tab`);
            }
        });
    }

    document.querySelectorAll('.btn-volver-datos').forEach(btn => {
        btn.addEventListener('click', () => irATab('paciente-tab'));
    });

    document.querySelectorAll('.btn-finalizar').forEach(btn => {
        btn.addEventListener('click', enviarFormulario);
    });

    document.querySelectorAll('.btn-siguiente').forEach(btn => {
        btn.addEventListener('click', () => {
            const current = btn.getAttribute('data-current');
            const seleccionadas = getPruebasSeleccionadas();
            const currentIndex = seleccionadas.indexOf(current);

            if (currentIndex !== -1 && currentIndex < seleccionadas.length - 1) {
                const siguiente = seleccionadas[currentIndex + 1];
                irATab(`${siguiente}-tab`);
            } else {
                enviarFormulario();
            }
        });
    });

    document.querySelectorAll('.btn-anterior').forEach(btn => {
        btn.addEventListener('click', () => {
            const current = btn.getAttribute('data-current');
            const seleccionadas = getPruebasSeleccionadas();
            const currentIndex = seleccionadas.indexOf(current);

            if (currentIndex > 0) {
                const anterior = seleccionadas[currentIndex - 1];
                irATab(`${anterior}-tab`);
            } else {
                irATab('paciente-tab');
            }
        });
    });

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

    function toggleTipoPaciente() {
        if (!radioAntiguo || !bloqueAntiguo) return;
        const esAntiguo = radioAntiguo.checked;
        bloqueAntiguo.classList.toggle('d-none', !esAntiguo);
        
        document.getElementById('paciente_rut').readOnly = esAntiguo;
        if (!esAntiguo) {
            document.getElementById('paciente_rut').value = '';
            document.getElementById('paciente_nombre').value = '';
            document.getElementById('paciente_nacimiento').value = '';
            document.getElementById('paciente_sexo').value = '';
        }
    }

    if (radioNuevo && radioAntiguo) {
        radioNuevo.addEventListener('change', toggleTipoPaciente);
        radioAntiguo.addEventListener('change', toggleTipoPaciente);
    }

    const btnCargarPaciente = document.getElementById('btn-cargar-paciente');
    if (btnCargarPaciente) {
        btnCargarPaciente.addEventListener('click', () => {
            const query = document.getElementById('buscar_paciente_bd').value.trim();
            if (!query) {
                alert('Ingrese un RUT o apellido para buscar.');
                return;
            }
            document.getElementById('paciente_rut').value = '12.345.678-K';
            document.getElementById('paciente_nombre').value = 'P. González R.';
            document.getElementById('paciente_nacimiento').value = '1994-05-14';
            document.getElementById('paciente_sexo').value = 'F';
            document.getElementById('paciente_motivo').value = 'atencion_primaria';
        });
    }

    actualizarTabsVisibles();
});