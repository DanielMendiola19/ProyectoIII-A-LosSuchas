document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalEditar');
    const spanCerrar = document.querySelector('.close');
    const editForm = document.getElementById('editForm');
    const notificacion = document.getElementById('notificacion');
    const productosBody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'info') {
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion ${tipo}`;
        notificacion.style.display = 'block';
        setTimeout(() => notificacion.style.display = 'none', 3000);
    }

    // Abrir modal de edición
    productosBody.addEventListener('click', e => {
        if (e.target.closest('.btn-editar')) {
            const btn = e.target.closest('.btn-editar');
            const row = btn.closest('tr');
            const id = btn.dataset.id;

            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = row.children[0].innerText;
            document.getElementById('editPrecio').value = parseFloat(row.children[1].innerText);
            document.getElementById('editStock').value = parseInt(row.children[2].innerText);

            const editCategoria = document.getElementById('editCategoria');
            Array.from(editCategoria.options).forEach(option => {
                option.selected = option.text === row.children[3].innerText;
            });

            modal.classList.add('show');
        }
    });

    // Cerrar modal
    spanCerrar.onclick = () => modal.classList.remove('show');
    window.onclick = e => { if (e.target === modal) modal.classList.remove('show'); };

    // Editar producto
    editForm.addEventListener('submit', async e => {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const formData = new FormData(editForm);

        try {
            const response = await fetch(`/productos/${id}`, {
                method: 'POST',
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

    // Eliminar producto (delegación)
    productosBody.addEventListener('click', async e => {
        if (e.target.closest('.btn-eliminar')) {
            e.preventDefault();
            const form = e.target.closest('form');

            if (confirm('¿Estás seguro de eliminar este producto?')) {
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'DELETE'
                        }
                    });

                    if (response.ok) {
                        mostrarNotificacion('Producto eliminado', 'error');
                        location.reload();
                    } else {
                        mostrarNotificacion('Error al eliminar', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    mostrarNotificacion('Error de conexión', 'error');
                }
            }
        }
    });
});
