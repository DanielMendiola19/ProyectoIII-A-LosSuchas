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

    // ✅ Validar solo números en número de mesa
    numeroMesa.addEventListener('input', () => {
        numeroMesa.value = numeroMesa.value.replace(/\D/g, '');
    });

    // ✅ Validar número de mesa en tiempo real
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

    // ✅ Validar capacidad (no negativa y <= 6)
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

    // ✅ Validar capacidad también en el modal de edición
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

    // ✅ Enviar formulario con AJAX
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

    // ✅ Enviar formulario de edición
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

    // ✅ Abrir modal de edición (bloqueando el número de mesa)
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            const mesa = JSON.parse(btn.dataset.mesa);
            formEditar.action = `/mesas/${mesa.id}`;
            document.getElementById('edit_numero_mesa').value = mesa.numero_mesa;
            document.getElementById('edit_numero_mesa').setAttribute('readonly', true);
            document.getElementById('edit_capacidad').value = mesa.capacidad;
            document.getElementById('edit_estado').value = mesa.estado;
            modalEditar.classList.add('show');
        });
    });

    // Cerrar modal
    cerrarModal.addEventListener('click', () => modalEditar.classList.remove('show'));
    modalEditar.addEventListener('click', e => {
        if (e.target === modalEditar) modalEditar.classList.remove('show');
    });

    // ✅ Función para notificaciones
    function mostrarNotificacion(mensaje, tipo = 'info') {
        let notificacion = document.getElementById('notificacion');
        if (!notificacion) {
            notificacion = document.createElement('div');
            notificacion.id = 'notificacion';
            notificacion.className = `notificacion ${tipo}`;
            document.body.appendChild(notificacion);
        }
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion ${tipo}`;
        notificacion.style.display = 'block';
        setTimeout(() => (notificacion.style.display = 'none'), 3000);
    }
});
