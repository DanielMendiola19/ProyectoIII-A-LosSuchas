document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalEditar');
    const spanCerrar = document.querySelector('.close');
    const editForm = document.getElementById('editForm');
    const notificacion = document.getElementById('notificacion');
    const productosBody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const inputImagen = document.getElementById('imagen');
    const labelImagen = document.querySelector('label.custom-file-label[for="imagen"]');

    const editImagenInput = document.getElementById('editImagen');
    const editPreview = document.getElementById('editPreview');

    const modalConfirmacion = document.getElementById('modalConfirmacion');
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    const btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
    const confirmacionMensaje = document.getElementById('confirmacionMensaje');
    let formEliminarActual = null;

    // 🔹 Crear elementos para mensajes de error
    function crearMensajesError() {
        // Para formulario de nuevo producto
        const precioInput = document.getElementById('precio');
        const stockInput = document.getElementById('stock');
        
        if (precioInput && !precioInput.nextElementSibling?.classList?.contains('mensaje-error')) {
            const precioError = document.createElement('div');
            precioError.className = 'mensaje-error';
            precioInput.parentNode.insertBefore(precioError, precioInput.nextSibling);
        }
        
        if (stockInput && !stockInput.nextElementSibling?.classList?.contains('mensaje-error')) {
            const stockError = document.createElement('div');
            stockError.className = 'mensaje-error';
            stockInput.parentNode.insertBefore(stockError, stockInput.nextSibling);
        }

        // Para formulario de edición
        const editPrecioInput = document.getElementById('editPrecio');
        const editStockInput = document.getElementById('editStock');
        
        if (editPrecioInput && !editPrecioInput.nextElementSibling?.classList?.contains('mensaje-error')) {
            const editPrecioError = document.createElement('div');
            editPrecioError.className = 'mensaje-error';
            editPrecioInput.parentNode.insertBefore(editPrecioError, editPrecioInput.nextSibling);
        }
        
        if (editStockInput && !editStockInput.nextElementSibling?.classList?.contains('mensaje-error')) {
            const editStockError = document.createElement('div');
            editStockError.className = 'mensaje-error';
            editStockInput.parentNode.insertBefore(editStockError, editStockInput.nextSibling);
        }
    }

    // 🔹 Mostrar mensaje de error debajo del campo
    function mostrarError(input, mensaje) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('mensaje-error')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'mensaje-error';
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        input.classList.add('error');
    }

    // 🔹 Limpiar mensaje de error
    function limpiarError(input) {
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('mensaje-error')) {
            errorDiv.style.display = 'none';
            input.classList.remove('error');
        }
    }

    // 🔹 Bloquear caracteres no permitidos (incluyendo 'e', '+', '-')
    function bloquearCaracteresInvalidos(input, esEntero = false) {
        input.addEventListener('keydown', function(e) {
            // Permitir teclas de control
            if (e.ctrlKey || e.altKey || e.metaKey) return;
            
            // Permitir teclas de navegación
            if ([8, 9, 13, 27, 37, 38, 39, 40, 46].includes(e.keyCode)) return;
            
            // Permitir punto decimal solo si no es entero
            if (e.key === '.' && !esEntero) return;
            
            // Permitir coma decimal solo si no es entero
            if (e.key === ',' && !esEntero) return;
            
            // Bloquear letras incluyendo 'e', 'E', '+', '-'
            if (/[a-zA-ZeE+\-]/.test(e.key)) {
                e.preventDefault();
                mostrarError(input, 'No se permite ingresar letras ni caracteres especiales');
                return;
            }
            
            // Permitir solo números
            if (!/\d/.test(e.key)) {
                e.preventDefault();
                return;
            }
        });
        
        // Validar también al pegar contenido
        input.addEventListener('paste', function(e) {
            const pastedData = e.clipboardData.getData('text');
            if (/[a-zA-ZeE+\-]/.test(pastedData)) {
                e.preventDefault();
                mostrarError(input, 'No se permite pegar letras ni caracteres especiales');
            }
        });
    }

    // 🔹 Validaciones de campos numéricos en tiempo real
    function configurarValidacionesEnTiempoReal() {
        const precioInput = document.getElementById('precio');
        const stockInput = document.getElementById('stock');
        const editPrecioInput = document.getElementById('editPrecio');
        const editStockInput = document.getElementById('editStock');

        // Configurar bloqueo de caracteres
        if (precioInput) {
            bloquearCaracteresInvalidos(precioInput, false);
            precioInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 1, 100, false, 'precio');
            });
        }

        if (stockInput) {
            bloquearCaracteresInvalidos(stockInput, true);
            stockInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 0, 50, true, 'stock');
            });
        }

        if (editPrecioInput) {
            bloquearCaracteresInvalidos(editPrecioInput, false);
            editPrecioInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 1, 100, false, 'precio');
            });
        }

        if (editStockInput) {
            bloquearCaracteresInvalidos(editStockInput, true);
            editStockInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 0, 50, true, 'stock');
            });
        }
    }

    // 🔹 Validación en tiempo real para campos numéricos
    function validarNumeroEnTiempoReal(input, min, max, esEntero = false, tipo = '') {
        let valor = input.value.trim();
        
        // Si está vacío, limpiar error
        if (valor === '') {
            limpiarError(input);
            return true;
        }

        // No permitir múltiples puntos decimales
        if ((valor.match(/\./g) || []).length > 1 || (valor.match(/,/g) || []).length > 1) {
            mostrarError(input, 'Solo se permite un punto o coma decimal');
            return false;
        }

        // Validar que no empiece con punto o coma
        if (/^[.,]/.test(valor)) {
            mostrarError(input, 'El número no puede empezar con punto o coma');
            return false;
        }

        let numero = parseFloat(valor.replace(',', '.'));
        
        // Si no es un número válido
        if (isNaN(numero)) {
            mostrarError(input, 'Por favor ingresa un número válido');
            return false;
        }

        // Validar si debe ser entero
        if (esEntero && !Number.isInteger(numero)) {
            mostrarError(input, 'Debe ser un número entero. No se permiten decimales');
            return false;
        }

        // Validar rango mínimo y máximo
        if (numero < min) {
            let mensaje = `El ${tipo} no puede ser menor a ${min}`;
            if (tipo === 'precio') {
                mensaje = `El precio no puede ser menor a ${min} Bs`;
            } else if (tipo === 'stock') {
                mensaje = `El stock no puede ser menor a ${min} unidades`;
            }
            mostrarError(input, mensaje);
            return false;
        }

        if (numero > max) {
            let mensaje = `El ${tipo} no puede ser mayor a ${max}`;
            if (tipo === 'precio') {
                mensaje = `El precio no puede ser mayor a ${max} Bs`;
            } else if (tipo === 'stock') {
                mensaje = `El stock no puede ser mayor a ${max} unidades`;
            }
            mostrarError(input, mensaje);
            return false;
        }

        // Si pasa todas las validaciones, limpiar error
        limpiarError(input);
        return true;
    }

    // 🔹 Validación de imagen (solo formatos válidos)
    function validarImagen(input) {
        const file = input.files[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!validTypes.includes(file.type)) {
                mostrarNotificacion('Solo se permiten archivos de imagen (JPG, PNG, GIF, WEBP).', 'error');
                input.value = ''; // limpia
                return false;
            }
            
            if (file.size > maxSize) {
                mostrarNotificacion('La imagen no puede ser mayor a 5MB.', 'error');
                input.value = '';
                return false;
            }
        }
        return true;
    }

    // Actualizar nombre de archivo en label
    inputImagen.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            if (validarImagen(this)) {
                labelImagen.textContent = this.files[0].name;
            } else {
                labelImagen.textContent = 'Seleccionar imagen';
            }
        } else {
            labelImagen.textContent = 'Seleccionar imagen';
        }
    });

    // Vista previa imagen del modal
    if (editImagenInput) {
        editImagenInput.addEventListener('change', e => {
            if (validarImagen(e.target)) {
                const file = e.target.files[0];
                if (file) {
                    editPreview.src = URL.createObjectURL(file);
                }
            } else {
                e.target.value = '';
                editPreview.src = '/img/defecto.png';
            }
        });
    }

    // Mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'info') {
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion ${tipo}`;
        notificacion.style.display = 'block';
        setTimeout(() => notificacion.style.display = 'none', 3000);
    }

    // Abrir modal de edición
    productosBody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-editar');
        if (btn) {
            const row = btn.closest('tr');
            const id = btn.dataset.id;
            const nombre = row.querySelector('.td-nombre')?.innerText?.trim() ?? '';
            const precioText = row.querySelector('.td-precio')?.innerText ?? '';
            const precio = parseFloat(precioText.replace(/[^\d.,-]/g, '').replace(',', '.')) || 0;
            const stock = parseInt(row.querySelector('.td-stock')?.innerText ?? '0', 10) || 0;
            const categoriaId = btn.dataset.categoria ?? '';

            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editPrecio').value = precio;
            document.getElementById('editStock').value = stock;

            const editCategoria = document.getElementById('editCategoria');
            Array.from(editCategoria.options).forEach(option => {
                option.selected = option.value === categoriaId.toString();
            });

            const imagenUrl = btn.dataset.imagen || '';
            editPreview.src = imagenUrl || '/img/defecto.png';
            if (editImagenInput) editImagenInput.value = '';

            // Limpiar errores al abrir modal
            limpiarError(document.getElementById('editPrecio'));
            limpiarError(document.getElementById('editStock'));

            modal.classList.add('show');
        }
    });

    // Cerrar modal
    spanCerrar.onclick = () => modal.classList.remove('show');
    window.onclick = e => { if (e.target === modal) modal.classList.remove('show'); };

    // ✅ Validar antes de enviar edición
    editForm.addEventListener('submit', async e => {
        e.preventDefault();

        const editPrecioInput = document.getElementById('editPrecio');
        const editStockInput = document.getElementById('editStock');
        
        const precioValido = validarNumeroEnTiempoReal(editPrecioInput, 1, 100, false, 'precio');
        const stockValido = validarNumeroEnTiempoReal(editStockInput, 0, 50, true, 'stock');
        const imagenValida = validarImagen(editImagenInput);

        if (!precioValido || !stockValido || !imagenValida) {
            mostrarNotificacion('Por favor corrige los errores en el formulario.', 'error');
            return;
        }

        const id = document.getElementById('editId').value;
        const formData = new FormData(editForm);

        try {
            const response = await fetch(`/productos/${id}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            });

            if (response.ok) {
                mostrarNotificacion('Producto actualizado exitosamente', 'exito');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarNotificacion('Error al actualizar producto', 'error');
            }
        } catch (err) {
            console.error(err);
            mostrarNotificacion('Error de conexión', 'error');
        }
    });

    // ✅ Validar antes de agregar nuevo producto
    const formNuevo = document.getElementById('productForm');
    if (formNuevo) {
        formNuevo.addEventListener('submit', e => {
            const precioInput = document.getElementById('precio');
            const stockInput = document.getElementById('stock');
            
            const precioValido = validarNumeroEnTiempoReal(precioInput, 1, 100, false, 'precio');
            const stockValido = validarNumeroEnTiempoReal(stockInput, 0, 50, true, 'stock');
            const imagenValida = validarImagen(inputImagen);

            if (!precioValido || !stockValido || !imagenValida) {
                e.preventDefault();
                mostrarNotificacion('Por favor corrige los errores en el formulario.', 'error');
            }
        });
    }

    // Eliminación con confirmación
    productosBody.addEventListener('click', async e => {
        const btnEliminar = e.target.closest('.btn-eliminar');
        if (btnEliminar) {
            e.preventDefault();
            formEliminarActual = btnEliminar.closest('form');
            const nombreProducto = btnEliminar.closest('tr').querySelector('.td-nombre').innerText;

            confirmacionMensaje.innerHTML = `
                ¿Estás seguro de que deseas eliminar el producto <strong>"${nombreProducto}"</strong>? 
                <br><br>
                <small>Esta acción es reversible. Podrás recuperarlo desde la sección de productos eliminados.</small>
            `;
            modalConfirmacion.classList.add('show');
        }
    });

    btnConfirmarEliminar.addEventListener('click', async () => {
        if (formEliminarActual) {
            try {
                const response = await fetch(formEliminarActual.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                });

                if (response.ok) {
                    mostrarNotificacion('Producto eliminado correctamente', 'exito');
                    modalConfirmacion.classList.remove('show');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion('Error al eliminar el producto', 'error');
                    modalConfirmacion.classList.remove('show');
                }
            } catch (err) {
                console.error(err);
                mostrarNotificacion('Error de conexión', 'error');
                modalConfirmacion.classList.remove('show');
            }
        }
    });

    btnCancelarEliminar.addEventListener('click', () => {
        modalConfirmacion.classList.remove('show');
        formEliminarActual = null;
    });

    modalConfirmacion.addEventListener('click', (e) => {
        if (e.target === modalConfirmacion) {
            modalConfirmacion.classList.remove('show');
            formEliminarActual = null;
        }
    });

    // Inicializar validaciones en tiempo real
    crearMensajesError();
    configurarValidacionesEnTiempoReal();
});