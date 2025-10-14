document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalEditar');
    const spanCerrar = document.querySelector('.close');
    const editForm = document.getElementById('editForm');
    const notificacion = document.getElementById('notificacion');
    const productosBody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const editImagenInput = document.getElementById('editImagen');
    const editPreview = document.getElementById('editPreview');

    const inputImagen = document.getElementById('imagen');
    const labelImagen = document.querySelector('label.custom-file-label[for="imagen"]');

    // Variables para el modal de confirmación
    const modalConfirmacion = document.getElementById('modalConfirmacion');
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    const btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
    const confirmacionMensaje = document.getElementById('confirmacionMensaje');
    let formEliminarActual = null;

    inputImagen.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            labelImagen.textContent = this.files[0].name; // muestra el nombre del archivo
        } else {
            labelImagen.textContent = 'Seleccionar imagen'; // texto por defecto si no hay archivo
        }
    });

    // Mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'info') {
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion ${tipo}`;
        notificacion.style.display = 'block';
        setTimeout(() => notificacion.style.display = 'none', 3000);
    }

    // Abrir modal de edición (delegación)
    productosBody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-editar');
        if (btn) {
            const row = btn.closest('tr');
            const id = btn.dataset.id;

            // Obtener valores desde celdas con clases
            const nombre = row.querySelector('.td-nombre')?.innerText?.trim() ?? '';
            const precioText = row.querySelector('.td-precio')?.innerText ?? '';
            // quitar caracteres no numéricos para parsear
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

            // preview de imagen actual
            const imagenUrl = btn.dataset.imagen || '';
            if (editPreview) {
                editPreview.src = imagenUrl || '/img/defecto.png';
            }

            // limpiar input file del modal
            if (editImagenInput) editImagenInput.value = '';

            modal.classList.add('show');
        }
    });

    // vista previa al seleccionar nueva imagen en modal
    if (editImagenInput) {
        editImagenInput.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                editPreview.src = URL.createObjectURL(file);
            } else {
                // si no hay archivo seleccionado, mantener la imagen previa (no cambiar)
            }
        });
    }

    // Cerrar modal
    spanCerrar.onclick = () => modal.classList.remove('show');
    window.onclick = e => { if (e.target === modal) modal.classList.remove('show'); };

    // Editar producto (envío por fetch con FormData para incluir imagen)
    editForm.addEventListener('submit', async e => {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const formData = new FormData(editForm);

        try {
            const response = await fetch(`/productos/${id}`, {
                method: 'POST', // usamos override
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            });

            if (response.ok) {
                mostrarNotificacion('Producto actualizado exitosamente', 'exito');
                location.reload();
            } else {
                mostrarNotificacion('Error al actualizar producto', 'error');
            }
        } catch (err) {
            console.error(err);
            mostrarNotificacion('Error de conexión', 'error');
        }
    });

    // Eliminar producto con confirmación personalizada
    productosBody.addEventListener('click', async e => {
        const btnEliminar = e.target.closest('.btn-eliminar');
        if (btnEliminar) {
            e.preventDefault();
            formEliminarActual = btnEliminar.closest('form');
            const nombreProducto = btnEliminar.closest('tr').querySelector('.td-nombre').innerText;
            
            // Personalizar el mensaje
            confirmacionMensaje.innerHTML = `
                ¿Estás seguro de que deseas eliminar el producto <strong>"${nombreProducto}"</strong>? 
                <br><br>
                <small>Esta acción es reversible. Podrás recuperarlo desde la sección de productos eliminados.</small>
            `;
            
            // Mostrar modal de confirmación
            modalConfirmacion.classList.add('show');
        }
    });

    // Confirmar eliminación
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

    // Cancelar eliminación
    btnCancelarEliminar.addEventListener('click', () => {
        modalConfirmacion.classList.remove('show');
        formEliminarActual = null;
    });

    // Cerrar modal al hacer clic fuera
    modalConfirmacion.addEventListener('click', (e) => {
        if (e.target === modalConfirmacion) {
            modalConfirmacion.classList.remove('show');
            formEliminarActual = null;
        }
    });
});