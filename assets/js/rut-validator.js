/**
 * rut-validator.js
 * Formateo dinámico y validación de RUT chileno con algoritmo Módulo 11.
 * Integrado con clases visuales de Bootstrap 5 (.is-valid / .is-invalid).
 */

document.addEventListener('DOMContentLoaded', () => {
    const rutInput = document.getElementById('paciente_rut');
    const rutFeedback = document.getElementById('rut-feedback');

    if (!rutInput) return;

    // Formateo automático mientras el usuario escribe
    rutInput.addEventListener('input', (e) => {
        let valor = e.target.value.replace(/[^0-9kK]/g, '').toUpperCase();
        
        if (valor.length === 0) {
            e.target.value = '';
            limpiarEstado(rutInput, rutFeedback);
            return;
        }

        // Limitar a máximo 9 caracteres brutos (ej: 12345678K)
        if (valor.length > 9) {
            valor = valor.slice(0, 9);
        }

        const dv = valor.slice(-1);
        const cuerpo = valor.slice(0, -1);

        // Aplicar formato con puntos y guion
        if (cuerpo.length > 0) {
            e.target.value = formatearCuerpo(cuerpo) + '-' + dv;
        } else {
            e.target.value = dv;
        }
    });

    // Validación matemática al perder el foco (blur) o al enviar
    rutInput.addEventListener('blur', () => {
        const valor = rutInput.value.trim();
        if (valor === '') {
            limpiarEstado(rutInput, rutFeedback);
            return;
        }

        if (validarRut(valor)) {
            marcarValido(rutInput, rutFeedback);
        } else {
            marcarInvalido(rutInput, rutFeedback, 'El RUT ingresado no es válido.');
        }
    });
});

/**
 * Inserta los separadores de miles con puntos en el cuerpo del RUT.
 */
function formatearCuerpo(cuerpo) {
    return cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Algoritmo Módulo 11 para comprobar el dígito verificador.
 */
function validarRut(rutCompleto) {
    // Limpiar puntos y guion
    const valorLimpio = rutCompleto.replace(/[^0-9kK]/g, '').toUpperCase();
    if (valorLimpio.length < 8) return false;

    const cuerpo = valorLimpio.slice(0, -1);
    const dvIngresado = valorLimpio.slice(-1);

    // Cálculo Módulo 11
    let suma = 0;
    let multiplicador = 2;

    for (let i = cuerpo.length - 1; i >= 0; i--) {
        suma += parseInt(cuerpo.charAt(i), 10) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }

    const residuo = 11 - (suma % 11);
    let dvEsperado = '';

    if (residuo === 11) dvEsperado = '0';
    else if (residuo === 10) dvEsperado = 'K';
    else dvEsperado = residuo.toString();

    return dvIngresado === dvEsperado;
}

function marcarValido(input, feedback) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    if (feedback) {
        feedback.textContent = '';
        feedback.className = 'valid-feedback';
    }
}

function marcarInvalido(input, feedback, mensaje) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    if (feedback) {
        feedback.textContent = mensaje;
        feedback.className = 'invalid-feedback';
    }
}

function limpiarEstado(input, feedback) {
    input.classList.remove('is-valid', 'is-invalid');
    if (feedback) feedback.textContent = '';
}