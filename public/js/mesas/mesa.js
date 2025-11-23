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

    // ✅ Botón de mantenimiento corregido - CON ESTILO PERSONALIZADO
    document.querySelectorAll('.btn-mantenimiento').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const mesaElement = document.querySelector(`.mesa-item[data-id="${id}"]`);
            const estadoActual = mesaElement?.classList.contains('mantenimiento');
            
            const accion = estadoActual ? 'quitar del mantenimiento' : 'poner en mantenimiento';
            
            // Usar nuestra confirmación personalizada en lugar de confirm() nativo
            const confirmado = await mostrarConfirmacion(
                `¿Estás seguro de que deseas ${accion} esta mesa?`, 
                'mantenimiento'
            );
            
            if (!confirmado) return;

            try {
                const res = await fetch(`/mesas/mantenimiento/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await res.json();
                
                if (res.ok && data.success) {
                    mostrarNotificacion(data.message, 'mantenimiento');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    mostrarNotificacion(data.message || 'Error al actualizar la mesa', 'error');
                }
            } catch (err) {
                console.error('Error completo:', err);
                mostrarNotificacion('Error de conexión: ' + err.message, 'error');
            }
        });
    });

    // ✅ Función personalizada para confirmaciones con estilo
    function mostrarConfirmacion(mensaje, tipo = 'general') {
        return new Promise((resolve) => {
            // Crear overlay
            const overlay = document.createElement('div');
            overlay.className = 'custom-alert-overlay';
            overlay.style.display = 'flex';

            // Crear caja de confirmación
            const alertBox = document.createElement('div');
            alertBox.className = `custom-alert-box ${tipo === 'mantenimiento' ? 'custom-alert-mantenimiento' : ''}`;

            // Contenido HTML
            alertBox.innerHTML = `
                <div class="custom-alert-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmación
                </div>
                <div class="custom-alert-message">${mensaje}</div>
                <div class="custom-alert-buttons">
                    <button class="custom-alert-btn custom-alert-confirm" id="customConfirmYes">
                        <i class="fas fa-check"></i> Aceptar
                    </button>
                    <button class="custom-alert-btn custom-alert-cancel" id="customConfirmNo">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            `;

            overlay.appendChild(alertBox);
            document.body.appendChild(overlay);

            // Event listeners
            document.getElementById('customConfirmYes').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(true);
            });

            document.getElementById('customConfirmNo').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(false);
            });

            // Cerrar al hacer clic fuera
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    document.body.removeChild(overlay);
                    resolve(false);
                }
            });

            // Cerrar con ESC
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', handleEsc);
                    document.body.removeChild(overlay);
                    resolve(false);
                }
            };
            document.addEventListener('keydown', handleEsc);
        });
    }

    // ✅ Validar solo números en número de mesa - MEJORADA
    numeroMesa.addEventListener('input', (e) => {
        // Remover cualquier caracter que no sea número
        let valor = e.target.value.replace(/[^\d]/g, '');
        
        // Remover ceros a la izquierda
        valor = valor.replace(/^0+/, '');
        
        // Limitar a 3 dígitos máximo
        if (valor.length > 3) {
            valor = valor.slice(0, 3);
        }
        
        e.target.value = valor;
        
        // Si el campo está vacío después de la limpieza, mostrar error
        if (valor === '') {
            errorNumero.textContent = 'El número de mesa es requerido';
        } else {
            errorNumero.textContent = '';
        }
    });

    // ✅ Validar también en el evento keydown para prevenir entrada de caracteres no numéricos
    numeroMesa.addEventListener('keydown', (e) => {
        // Permitir teclas de control (backspace, delete, tab, etc.)
        if (
            e.key === 'Backspace' || 
            e.key === 'Delete' || 
            e.key === 'Tab' || 
            e.key === 'ArrowLeft' || 
            e.key === 'ArrowRight' ||
            e.key === 'Home' ||
            e.key === 'End' ||
            (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x'))
        ) {
            return;
        }
        
        // Permitir solo números
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            mostrarNotificacion('Solo se permiten números en el campo de número de mesa', 'error');
        }
    });

    // ✅ Validar también en el evento paste para limpiar contenido pegado
    numeroMesa.addEventListener('paste', (e) => {
        e.preventDefault();
        const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
        const soloNumeros = textoPegado.replace(/[^\d]/g, '');
        document.execCommand('insertText', false, soloNumeros);
    });

    // ✅ Validar número de mesa duplicado en tiempo real - MEJORADA
    numeroMesa.addEventListener('keyup', async () => {
        const numero = numeroMesa.value.trim();
        
        if (numero === '') {
            errorNumero.textContent = 'El número de mesa es requerido';
            return;
        }
        
        // Validar que sea un número válido (no 0)
        if (numero === '0') {
            errorNumero.textContent = 'El número de mesa no puede ser 0';
            return;
        }
        
        // Validar que no sea demasiado grande
        if (parseInt(numero) > 999) {
            errorNumero.textContent = 'El número de mesa no puede ser mayor a 999';
            return;
        }
        
        try {
            const res = await fetch(`/mesas/verificar/${numero}`);
            const data = await res.json();
            errorNumero.textContent = data.existe ? 'Este número de mesa ya existe' : '';
        } catch (error) {
            console.error('Error al verificar número:', error);
            errorNumero.textContent = 'Error al verificar disponibilidad';
        }
    });

    // ✅ Validar capacidad (mínimo 2, máximo 6) - MEJORADA
    capacidad.addEventListener('input', (e) => {
        // Remover cualquier caracter que no sea número
        let valor = e.target.value.replace(/[^\d]/g, '');
        
        // Si está vacío, mostrar error
        if (valor === '') {
            errorCapacidad.textContent = 'La capacidad es requerida';
            e.target.value = '';
            return;
        }
        
        const numValor = parseInt(valor);
        
        if (numValor < 2) {
            errorCapacidad.textContent = 'La capacidad mínima es 2';
            e.target.value = 2;
        } else if (numValor > 6) {
            errorCapacidad.textContent = 'La capacidad máxima es 6';
            e.target.value = 6;
        } else {
            errorCapacidad.textContent = '';
            e.target.value = numValor;
        }
    });

    capacidad.addEventListener('keydown', (e) => {
        // Permitir teclas de control
        if (
            e.key === 'Backspace' || 
            e.key === 'Delete' || 
            e.key === 'Tab' || 
            e.key === 'ArrowLeft' || 
            e.key === 'ArrowRight' ||
            e.key === 'Home' ||
            e.key === 'End' ||
            (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x'))
        ) {
            return;
        }
        
        // Permitir solo números
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            mostrarNotificacion('Solo se permiten números en el campo de capacidad', 'error');
        }
    });

    // ✅ Validar capacidad en edición - MEJORADA
    document.getElementById('edit_capacidad').addEventListener('input', (e) => {
        // Remover cualquier caracter que no sea número
        let valor = e.target.value.replace(/[^\d]/g, '');
        
        // Si está vacío, mostrar error
        if (valor === '') {
            errorEditarCapacidad.textContent = 'La capacidad es requerida';
            e.target.value = '';
            return;
        }
        
        const numValor = parseInt(valor);
        
        if (numValor < 2) {
            errorEditarCapacidad.textContent = 'La capacidad mínima es 2';
            e.target.value = 2;
        } else if (numValor > 6) {
            errorEditarCapacidad.textContent = 'La capacidad máxima es 6';
            e.target.value = 6;
        } else {
            errorEditarCapacidad.textContent = '';
            e.target.value = numValor;
        }
    });

    document.getElementById('edit_capacidad').addEventListener('keydown', (e) => {
        // Permitir teclas de control
        if (
            e.key === 'Backspace' || 
            e.key === 'Delete' || 
            e.key === 'Tab' || 
            e.key === 'ArrowLeft' || 
            e.key === 'ArrowRight' ||
            e.key === 'Home' ||
            e.key === 'End' ||
            (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x'))
        ) {
            return;
        }
        
        // Permitir solo números
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            mostrarNotificacion('Solo se permiten números en el campo de capacidad', 'error');
        }
    });

    // ✅ Validar solo números en número de mesa (EDITAR) - MEJORADA
    document.getElementById('edit_numero_mesa').addEventListener('input', (e) => {
        // Remover cualquier caracter que no sea número
        let valor = e.target.value.replace(/[^\d]/g, '');
        
        // Remover ceros a la izquierda
        valor = valor.replace(/^0+/, '');
        
        // Limitar a 3 dígitos máximo
        if (valor.length > 3) {
            valor = valor.slice(0, 3);
        }
        
        e.target.value = valor;
    });

    document.getElementById('edit_numero_mesa').addEventListener('keydown', (e) => {
        // Permitir teclas de control (backspace, delete, tab, etc.)
        if (
            e.key === 'Backspace' || 
            e.key === 'Delete' || 
            e.key === 'Tab' || 
            e.key === 'ArrowLeft' || 
            e.key === 'ArrowRight' ||
            e.key === 'Home' ||
            e.key === 'End' ||
            (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x'))
        ) {
            return;
        }
        
        // Permitir solo números
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            mostrarNotificacion('Solo se permiten números en el campo de número de mesa', 'error');
        }
    });

    document.getElementById('edit_numero_mesa').addEventListener('paste', (e) => {
        e.preventDefault();
        const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
        const soloNumeros = textoPegado.replace(/[^\d]/g, '');
        document.execCommand('insertText', false, soloNumeros);
    });

    // ✅ Crear mesa con AJAX - MEJORADA
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validaciones finales antes de enviar
        const numero = numeroMesa.value.trim();
        const capacidadVal = capacidad.value.trim();
        
        if (numero === '' || numero === '0') {
            mostrarNotificacion('El número de mesa es requerido y debe ser mayor a 0', 'error');
            numeroMesa.focus();
            return;
        }
        
        if (capacidadVal === '' || parseInt(capacidadVal) < 2 || parseInt(capacidadVal) > 6) {
            mostrarNotificacion('La capacidad debe estar entre 2 y 6', 'error');
            capacidad.focus();
            return;
        }
        
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
                    mostrarNotificacion('Posiciones guardadas correctamente', 'exito');
                } else {
                    mostrarNotificacion('Error al guardar las posiciones', 'error');
                }   
            } catch (err) {
                console.error('Error:', err);
                mostrarNotificacion('Error de conexión al guardar', 'error');
            }
        });
    }
});