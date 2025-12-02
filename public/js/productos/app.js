document.addEventListener('DOMContentLoaded', () => {
    // 🔹 Obtener elementos de forma segura con verificaciones
    const modal = document.getElementById('modalEditar');
    const spanCerrar = document.querySelector('.close');
    const editForm = document.getElementById('editForm');
    const notificacion = document.getElementById('notificacion');
    const productosBody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const inputImagen = document.getElementById('imagen');
    const labelImagen = document.querySelector('label.custom-file-label[for="imagen"]');

    const editImagenInput = document.getElementById('editImagen');
    const editPreview = document.getElementById('editPreview');

    const modalConfirmacion = document.getElementById('modalConfirmacion');
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    const btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
    const confirmacionMensaje = document.getElementById('confirmacionMensaje');

    // 🔹 Obtener elementos de forma segura
    const btnGuardarCambios = editForm ? editForm.querySelector('button[type="submit"]') : null;
    const formNuevo = document.getElementById('productForm');
    const precioInput = document.getElementById('precio');
    const stockInput = document.getElementById('stock');
    const nombreInput = document.getElementById('nombre');
    const editNombreInput = document.getElementById('editNombre');

    let formEliminarActual = null;

    // 🔹 Función para mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'info') {
        if (notificacion) {
            notificacion.textContent = mensaje;
            notificacion.className = `notificacion ${tipo}`;
            notificacion.style.display = 'block';
            setTimeout(() => notificacion.style.display = 'none', 3000);
        }
    }

    async function validarNombreYActualizarBoton() {
        if (!editNombreInput || !btnGuardarCambios) return;
        
        const id = document.getElementById('editId')?.value || null;
        const nombreValido = await verificarNombreUnico(editNombreInput, id);
        btnGuardarCambios.disabled = !nombreValido;
    }

    // 🔹 Verificación de nombre único en tiempo real
    async function verificarNombreUnico(input, id = null) {
        if (!input) return true;
        
        const nombre = input.value.trim();
        if (!nombre) {
            limpiarError(input);
            return true;
        }

        try {
            const url = id
                ? `/productos/verificar-nombre?nombre=${encodeURIComponent(nombre)}&id=${id}`
                : `/productos/verificar-nombre?nombre=${encodeURIComponent(nombre)}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.existe) {
                mostrarError(input, 'Ya existe un producto con un nombre similar.');
                return false;
            } else {
                limpiarError(input);
                return true;
            }
        } catch (err) {
            console.error(err);
            mostrarError(input, 'Error verificando nombre en el servidor.');
            return false;
        }
    }

    // 🔹 Escucha en tiempo real para los campos de nombre
    if (nombreInput) {
        nombreInput.addEventListener('input', async function () {
            await verificarNombreUnico(this);
        });
    }

    if (editNombreInput) {
        editNombreInput.addEventListener('input', async function () {
            const id = document.getElementById('editId')?.value || null;
            await verificarNombreUnico(this, id);
            validarNombreYActualizarBoton();
        });
    }

    // 🔹 Modificar envío de formularios para incluir esta validación
    if (formNuevo) {
        formNuevo.addEventListener('submit', async e => {
            e.preventDefault();
            
            const nombreValido = await verificarNombreUnico(nombreInput);
            const precioValido = validarNumeroEnTiempoReal(precioInput, 1, 100, false, 'precio');
            const stockValido = validarNumeroEnTiempoReal(stockInput, 0, 50, true, 'stock');
            const imagenValida = validarImagen(inputImagen);

            if (!nombreValido || !precioValido || !stockValido || !imagenValida) {
                mostrarNotificacion('Por favor corrige los errores en el formulario.', 'error');
                return;
            }

            formNuevo.submit();
        });
    }

    // 🔹 Validar antes de enviar edición (solo si editForm existe)
    if (editForm) {
        editForm.addEventListener('submit', async e => {
            e.preventDefault();

            const id = document.getElementById('editId')?.value;
            const editPrecioInput = document.getElementById('editPrecio');
            const editStockInput = document.getElementById('editStock');

            if (!id) {
                mostrarNotificacion('Error: ID de producto no encontrado.', 'error');
                return;
            }

            const nombreValido = await verificarNombreUnico(editNombreInput, id);
            const precioValido = validarNumeroEnTiempoReal(editPrecioInput, 1, 100, false, 'precio');
            const stockValido = validarNumeroEnTiempoReal(editStockInput, 0, 50, true, 'stock');
            const imagenValida = validarImagen(editImagenInput);

            if (!nombreValido || !precioValido || !stockValido || !imagenValida) {
                mostrarNotificacion('Por favor corrige los errores en el formulario.', 'error');
                return;
            }

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
    }

    // 🔹 Crear elementos para mensajes de error
    function crearMensajesError() {
        // Para formulario de nuevo producto
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
        if (!input) return;
        
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
        if (!input) return;
        
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('mensaje-error')) {
            errorDiv.style.display = 'none';
            input.classList.remove('error');
        }
    }

    // 🔹 MEJORADO: Bloquear caracteres no permitidos y validar centavos
    function bloquearCaracteresInvalidos(input, esEntero = false, esPrecio = false) {
        input.addEventListener('keydown', function(e) {
            // Permitir teclas de control
            if (e.ctrlKey || e.altKey || e.metaKey) return;
            
            // Permitir teclas de navegación
            if ([8, 9, 13, 27, 37, 38, 39, 40, 46].includes(e.keyCode)) return;
            
            // Para precios: permitir punto decimal
            if (esPrecio && e.key === '.') {
                // Verificar que no haya ya un punto
                if (this.value.includes('.')) {
                    e.preventDefault();
                    mostrarError(input, 'Solo se permite un punto decimal');
                    return;
                }
                return;
            }
            
            // Para stock (entero): no permitir punto
            if (esEntero && (e.key === '.' || e.key === ',')) {
                e.preventDefault();
                mostrarError(input, 'No se permiten decimales en el stock');
                return;
            }
            
            // Bloquear letras incluyendo 'e', 'E', '+', '-', '*', '/'
            if (/[a-zA-ZeE+\-*/]/.test(e.key)) {
                e.preventDefault();
                mostrarError(input, 'No se permite ingresar letras ni caracteres especiales');
                return;
            }
            
            // Permitir solo números
            if (!/\d/.test(e.key)) {
                e.preventDefault();
                return;
            }

            // 🔹 VALIDACIÓN ESPECIAL PARA PRECIOS: Solo permitir 2 decimales
            if (esPrecio && this.value.includes('.')) {
                const partes = this.value.split('.');
                if (partes[1] && partes[1].length >= 2) {
                    e.preventDefault();
                    mostrarError(input, 'Solo se permiten 2 decimales en el precio');
                    return;
                }
            }
        });
        
        // Validar también al pegar contenido
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            
            // Validar caracteres especiales
            if (/[a-zA-ZeE+\-*/]/.test(pastedData)) {
                mostrarError(input, 'No se permite pegar letras ni caracteres especiales');
                return;
            }
            
            // Para precios: validar formato de decimales
            if (esPrecio) {
                const partes = pastedData.split('.');
                if (partes.length > 2) {
                    mostrarError(input, 'Formato inválido: solo un punto decimal permitido');
                    return;
                }
                if (partes[1] && partes[1].length > 2) {
                    mostrarError(input, 'Solo se permiten 2 decimales en el precio');
                    return;
                }
            }
            
            // Para stock: no permitir decimales
            if (esEntero && pastedData.includes('.')) {
                mostrarError(input, 'No se permiten decimales en el stock');
                return;
            }
            
            // Si pasa todas las validaciones, insertar el texto limpio
            const textoLimpio = pastedData.replace(/[^\d.]/g, '');
            document.execCommand('insertText', false, textoLimpio);
        });

        // 🔹 VALIDACIÓN EN TIEMPO REAL PARA PRECIOS: Forzar 2 decimales
        if (esPrecio) {
            input.addEventListener('input', function() {
                if (this.value.includes('.')) {
                    const partes = this.value.split('.');
                    if (partes[1] && partes[1].length > 2) {
                        // Cortar a 2 decimales
                        this.value = partes[0] + '.' + partes[1].substring(0, 2);
                        mostrarError(input, 'Se han recortado los decimales a 2 lugares');
                        setTimeout(() => limpiarError(input), 2000);
                    }
                }
            });
        }
    }

    // 🔹 Validaciones de campos numéricos en tiempo real
    function configurarValidacionesEnTiempoReal() {
        const editPrecioInput = document.getElementById('editPrecio');
        const editStockInput = document.getElementById('editStock');

        // Configurar bloqueo de caracteres para formulario NUEVO
        if (precioInput) {
            bloquearCaracteresInvalidos(precioInput, false, true); // esPrecio = true
            precioInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 1, 100, false, 'precio');
            });
        }

        if (stockInput) {
            bloquearCaracteresInvalidos(stockInput, true, false); // esEntero = true
            stockInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 0, 50, true, 'stock');
            });
        }

        // Configurar bloqueo de caracteres para formulario EDICIÓN
        if (editPrecioInput) {
            bloquearCaracteresInvalidos(editPrecioInput, false, true); // esPrecio = true
            editPrecioInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 1, 100, false, 'precio');
            });
        }

        if (editStockInput) {
            bloquearCaracteresInvalidos(editStockInput, true, false); // esEntero = true
            editStockInput.addEventListener('input', function() {
                validarNumeroEnTiempoReal(this, 0, 50, true, 'stock');
            });
        }
    }

    // 🔹 MEJORADO: Validación en tiempo real para campos numéricos con validación de centavos
    function validarNumeroEnTiempoReal(input, min, max, esEntero = false, tipo = '') {
        if (!input) return false;
        
        let valor = input.value.trim();
        
        // Si está vacío, limpiar error
        if (valor === '') {
            limpiarError(input);
            return true;
        }

        // No permitir múltiples puntos decimales
        if ((valor.match(/\./g) || []).length > 1) {
            mostrarError(input, 'Solo se permite un punto decimal');
            return false;
        }

        // Validar que no empiece con punto
        if (/^\./.test(valor)) {
            mostrarError(input, 'El número no puede empezar con punto');
            return false;
        }

        // Para precios: validar que los decimales sean múltiplos de 10 (solo .00, .10, .20, etc.)
        if (tipo === 'precio' && valor.includes('.')) {
            const partes = valor.split('.');
            if (partes[1]) {
                // Convertir a número para verificar si es múltiplo de 10
                const decimal = parseInt(partes[1]);
                if (decimal % 10 !== 0 && partes[1].length === 2) {
                    mostrarError(input, 'Los centavos deben ser múltiplos de 10 (ej: 4.20, 4.50, 4.00)');
                    return false;
                }
                
                // Validar que no tenga más de 2 decimales
                if (partes[1].length > 2) {
                    mostrarError(input, 'Solo se permiten 2 decimales en el precio');
                    return false;
                }
            }
        }

        let numero = parseFloat(valor);
        
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
        if (!input) return true;
        
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
    if (inputImagen) {
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
    }

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
                if (editPreview) editPreview.src = '/img/defecto.png';
            }
        });
    }

    // Abrir modal de edición
    if (productosBody) {
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
                if (editCategoria) {
                    Array.from(editCategoria.options).forEach(option => {
                        option.selected = option.value === categoriaId.toString();
                    });
                }

                const imagenUrl = btn.dataset.imagen || '';
                if (editPreview) editPreview.src = imagenUrl || '/img/defecto.png';
                if (editImagenInput) editImagenInput.value = '';

                // Limpiar errores al abrir modal
                limpiarError(document.getElementById('editPrecio'));
                limpiarError(document.getElementById('editStock'));

                if (modal) modal.classList.add('show');
                validarNombreYActualizarBoton();
            }
        });
    }

    // Cerrar modal
    if (spanCerrar) {
        spanCerrar.onclick = () => {
            if (modal) modal.classList.remove('show');
        };
    }
    
    if (modal) {
        window.onclick = e => { 
            if (e.target === modal) modal.classList.remove('show'); 
        };
    }

    // ✅ Validar antes de agregar nuevo producto
    if (formNuevo) {
        formNuevo.addEventListener('submit', e => {
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
    if (productosBody) {
        productosBody.addEventListener('click', async e => {
            const btnEliminar = e.target.closest('.btn-eliminar');
            if (btnEliminar) {
                e.preventDefault();
                formEliminarActual = btnEliminar.closest('form');
                const nombreProducto = btnEliminar.closest('tr').querySelector('.td-nombre')?.innerText;

                if (confirmacionMensaje) {
                    confirmacionMensaje.innerHTML = `
                        ¿Estás seguro de que deseas eliminar el producto <strong>"${nombreProducto}"</strong>? 
                        <br><br>
                        <small>Esta acción es reversible. Podrás recuperarlo desde la sección de productos eliminados.</small>
                    `;
                }
                if (modalConfirmacion) modalConfirmacion.classList.add('show');
            }
        });
    }

    if (btnConfirmarEliminar) {
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
                        if (modalConfirmacion) modalConfirmacion.classList.remove('show');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        mostrarNotificacion('Error al eliminar el producto', 'error');
                        if (modalConfirmacion) modalConfirmacion.classList.remove('show');
                    }
                } catch (err) {
                    console.error(err);
                    mostrarNotificacion('Error de conexión', 'error');
                    if (modalConfirmacion) modalConfirmacion.classList.remove('show');
                }
            }
        });
    }

    if (btnCancelarEliminar) {
        btnCancelarEliminar.addEventListener('click', () => {
            if (modalConfirmacion) modalConfirmacion.classList.remove('show');
            formEliminarActual = null;
        });
    }

    if (modalConfirmacion) {
        modalConfirmacion.addEventListener('click', (e) => {
            if (e.target === modalConfirmacion) {
                modalConfirmacion.classList.remove('show');
                formEliminarActual = null;
            }
        });
    }

    // Inicializar validaciones en tiempo real
    crearMensajesError();
    configurarValidacionesEnTiempoReal();
});