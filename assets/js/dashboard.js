document.addEventListener('DOMContentLoaded', () => {
    const inputFiltro = document.getElementById('filtro-registros');
    
    if (inputFiltro) {
        inputFiltro.addEventListener('keyup', function() {
            const termino = this.value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tabla-registros tbody tr');

            filas.forEach(fila => {
                const rut = fila.querySelector('.col-rut').textContent.toLowerCase();
                const nombre = fila.querySelector('.col-nombre').textContent.toLowerCase();
                fila.style.display = (rut.includes(termino) || nombre.includes(termino)) ? '' : 'none';
            });
        });
    }
});