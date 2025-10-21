document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formMesa');
    const numeroMesa = document.getElementById('numero_mesa');
    const errorNumero = document.getElementById('errorNumero');
    const capacidad = document.getElementById('capacidad');
    const errorCapacidad = document.getElementById('errorCapacidad');
    const modalEditar = document.getElementById('modalEditar');
    const formEditar = document.getElementById('formEditar');
    const cerrarModal = document.getElementById('cerrarModal');
    const errorEditarCapacidad = document.getElementById('errorEditarCapacidad');
    const areaMesas = document.getElementById('area-mesas');

    // ✅ Validar solo números en número de mesa
    numeroMesa.addEventListener('input', () => {
        numeroMesa.value = numeroMesa.value.replace(/\D/g, '');
    });

    // ✅ Validar número de mesa duplicado en tiempo real
    numeroMesa.addEventListener('keyup', async () => {
        const numero = numeroMesa.value.trim();
        if (numero === '') {
            errorNumero.textContent = '';
            return;
        }
        const res = await fetch(`/mesas/verificar/${numero}`);
        const data = await res.json();
        errorNumero.textContent = data.existe ? '⚠️ Este número de mesa ya existe' : '';
    });

    // ✅ Validar capacidad (mínimo 2, máximo 6)
    capacidad.addEventListener('input', () => {
        const valor = parseInt(capacidad.value);
        if (valor < 2) {
            errorCapacidad.textContent = '⚠️ La capacidad mínima es 2';
            capacidad.value = 2;
        } else if (valor > 6) {
            errorCapacidad.textContent = '⚠️ La capacidad máxima es 6';
            capacidad.value = 6;
        } else {
            errorCapacidad.textContent = '';
        }
    });

    // ✅ Validar capacidad en edición
    document.getElementById('edit_capacidad').addEventListener('input', (e) => {
        const valor = parseInt(e.target.value);
        if (valor < 2) {
            errorEditarCapacidad.textContent = '⚠️ La capacidad mínima es 2';
            e.target.value = 2;
        } else if (valor > 6) {
            errorEditarCapacidad.textContent = '⚠️ La capacidad máxima es 6';
            e.target.value = 6;
        } else {
            errorEditarCapacidad.textContent = '';
        }
    });

    // ✅ Crear mesa con AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (errorNumero.textContent !== '') {
            mostrarNotificacion('Corrige los errores antes de continuar', 'error');
            return;
        }
        const formData = new FormData(form);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            if (response.ok) {
                mostrarNotificacion('Mesa creada correctamente', 'exito');
                form.reset();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                mostrarNotificacion('Error al crear la mesa', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    });

    // ✅ Editar mesa con AJAX
    formEditar.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formEditar);
        try {
            const response = await fetch(formEditar.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT'
                }
            });
            if (response.ok) {
                mostrarNotificacion('Mesa actualizada correctamente', 'exito');
                modalEditar.classList.remove('show');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                mostrarNotificacion('Error al actualizar la mesa', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión', 'error');
        }
    });

    // ✅ Abrir modal de edición
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            const mesa = JSON.parse(btn.dataset.mesa);
            formEditar.action = `/mesas/${mesa.id}`;
            document.getElementById('edit_numero_mesa').value = mesa.numero_mesa;
            document.getElementById('edit_capacidad').value = mesa.capacidad;
            document.getElementById('edit_estado').value = mesa.estado;
            modalEditar.classList.add('show');
        });
    });

    // ✅ Cerrar modal
    cerrarModal.addEventListener('click', () => modalEditar.classList.remove('show'));
    modalEditar.addEventListener('click', e => {
        if (e.target === modalEditar) modalEditar.classList.remove('show');
    });

    // ✅ Mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'info') {
        let notificacion = document.getElementById('notificacion');
        if (!notificacion) {
            notificacion = document.createElement('div');
            notificacion.id = 'notificacion';
            document.body.appendChild(notificacion);
        }
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion ${tipo}`;
        notificacion.style.display = 'block';
        setTimeout(() => (notificacion.style.display = 'none'), 3000);
    }

    // ✅ Drag & Drop de mesas (sin guardar automático)
    let dragged = null, offsetX = 0, offsetY = 0;
    areaMesas.querySelectorAll('.mesa-item').forEach(mesa => {
        // Mostrar número arriba
        const numLabel = document.createElement('div');
        numLabel.className = 'mesa-numero';
        numLabel.style.position = 'absolute';
        numLabel.style.top = '-22px';
        numLabel.style.left = '50%';
        numLabel.style.transform = 'translateX(-50%)';
        numLabel.style.fontWeight = 'bold';
        numLabel.style.color = '#fff';
        mesa.appendChild(numLabel);

        mesa.addEventListener('mousedown', e => {
            dragged = mesa;
            offsetX = e.offsetX;
            offsetY = e.offsetY;
            mesa.style.zIndex = 1000;
        });
    });

    document.addEventListener('mousemove', e => {
        if (!dragged) return;
        const rect = areaMesas.getBoundingClientRect();
        let x = e.clientX - rect.left - offsetX;
        let y = e.clientY - rect.top - offsetY;
        x = Math.max(0, Math.min(x, areaMesas.offsetWidth - dragged.offsetWidth));
        y = Math.max(0, Math.min(y, areaMesas.offsetHeight - dragged.offsetHeight));
        dragged.style.left = x + 'px';
        dragged.style.top = y + 'px';
    });

    document.addEventListener('mouseup', () => {
        if (dragged) {
            dragged.style.zIndex = '';
            dragged = null;
        }
    });

        // ✅ Botón para guardar todas las posiciones
    const btnGuardar = document.getElementById('guardar-posiciones');
    if (btnGuardar) {
        btnGuardar.addEventListener('click', async () => {
            const mesas = Array.from(areaMesas.querySelectorAll('.mesa-item')).map(mesa => ({
                id: mesa.dataset.id,
                pos_x: parseInt(mesa.style.left) || 0,
                pos_y: parseInt(mesa.style.top) || 0
            }));

            try {
                const response = await fetch('/mesas/guardar-posiciones', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ mesas })
                });

                if (response.ok) {
                    mostrarNotificacion('✅ Posiciones guardadas correctamente', 'exito');
                } else {
                    mostrarNotificacion('❌ Error al guardar las posiciones', 'error');
                }
            } catch (err) {
                console.error('Error:', err);
                mostrarNotificacion('⚠️ Error de conexión al guardar', 'error');
            }
        });
    }
   
});
