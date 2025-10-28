document.addEventListener('DOMContentLoaded', () => {
    const carrito = [];
    const btnCarrito = document.getElementById('btnCarrito');
    const modalCarrito = document.getElementById('modalCarrito');
    const modalMesa = document.getElementById('modalMesa'); // Cambiado de modalMesas
    const modalPago = document.getElementById('modalPago');
    const listaCarrito = document.getElementById('listaCarrito');
    const contadorCarrito = document.getElementById('contadorCarrito');
    const totalSpan = document.getElementById('total');
    const btnCerrarCarrito = document.getElementById('btnCerrarCarrito');
    const btnSiguiente = document.getElementById('btnSiguiente'); // Cambiado de btnSiguienteCarrito
    const btnMesaSiguiente = document.getElementById('btnMesaSiguiente'); // Cambiado de btnSiguienteMesas
    const btnCancelarMesa = document.getElementById('btnCancelarMesa'); // Cambiado de btnCerrarMesas
    const btnCancelarPago = document.getElementById('btnCancelarPago');
    const formPedido = document.getElementById('formPedido');

    let mesaSeleccionada = null;

    // --- AGREGAR PRODUCTO AL CARRITO ---
    document.addEventListener('click', (e) => {
        const boton = e.target.closest('.btn-agregar');
        const card = e.target.closest('.product-card');
        
        if (!card) return;

        if (boton) {
            agregarAlCarrito(card, boton);
        }
    });

    function agregarAlCarrito(card, boton = null) {
        const id = card.dataset.id;
        const nombre = card.dataset.nombre;
        const precio = parseFloat(card.dataset.precio);
        const imagen = card.dataset.imagen;
        const stock = parseInt(card.dataset.stock);

        const existente = carrito.find(p => p.id == id);
        if (existente) {
            if (existente.cantidad >= stock) {
                mostrarNotificacion(`⚠️ Stock insuficiente para ${nombre}`, 'error');
                return;
            }
            existente.cantidad++;
        } else {
            if (stock <= 0) {
                mostrarNotificacion(`⚠️ ${nombre} sin stock`, 'error');
                return;
            }
            carrito.push({ id, nombre, precio, imagen, cantidad: 1, stock });
        }

        actualizarCarrito();
        mostrarNotificacion(`✅ ${nombre} agregado al carrito`, 'exito');

        if (boton) {
            boton.innerHTML = '<i class="fas fa-check"></i> Agregado';
            boton.style.background = 'var(--verde-exito)';
            setTimeout(() => {
                boton.innerHTML = '<i class="fas fa-cart-plus"></i> Agregar';
                boton.style.background = '';
            }, 1000);
        }
    }

    // --- ACTUALIZAR CARRITO ---
        // --- ACTUALIZAR CARRITO ---
    function actualizarCarrito() {
        listaCarrito.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            listaCarrito.innerHTML = `
                <div class="carrito-vacio">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Tu carrito está vacío</p>
                </div>`;
            
            // 🔒 Desactivar botón siguiente cuando el carrito está vacío
            btnSiguiente.disabled = true;
            btnSiguiente.classList.add('disabled');
        } else {
            carrito.forEach((p, i) => {
                total += p.precio * p.cantidad;
                const item = document.createElement('div');
                item.classList.add('carrito-item');
                item.innerHTML = `
                    <div class="carrito-producto">
                        <img src="${p.imagen}" alt="${p.nombre}" class="img-carrito">
                        <div class="info-carrito">
                            <span class="nombre">${p.nombre}</span>
                            <div class="cantidad-controls">
                                <button class="cantidad-btn" data-index="${i}" data-action="menos">-</button>
                                <span class="cantidad">${p.cantidad}</span>
                                <button class="cantidad-btn" data-index="${i}" data-action="mas">+</button>
                            </div>
                            <div class="precio-info">
                                <span>Bs ${p.precio.toFixed(2)} c/u</span>
                                <strong>Bs ${(p.precio * p.cantidad).toFixed(2)}</strong>
                            </div>
                        </div>
                        <button class="btn-eliminar-item" data-index="${i}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`;
                listaCarrito.appendChild(item);
            });

            // ✅ Activar botón siguiente solo si hay productos
            btnSiguiente.disabled = false;
            btnSiguiente.classList.remove('disabled');
        }

        totalSpan.textContent = total.toFixed(2);
        contadorCarrito.textContent = carrito.reduce((a, b) => a + b.cantidad, 0);
    }


    // --- CONTROL DE CANTIDAD Y ELIMINAR ---
    listaCarrito.addEventListener('click', (e) => {
        const btn = e.target.closest('.cantidad-btn');
        const eliminar = e.target.closest('.btn-eliminar-item');

        if (btn) {
            const index = btn.dataset.index;
            const action = btn.dataset.action;

            if (action === 'mas' && carrito[index].cantidad < carrito[index].stock) {
                carrito[index].cantidad++;
            } else if (action === 'menos') {
                carrito[index].cantidad--;
                if (carrito[index].cantidad <= 0) carrito.splice(index, 1);
            } else if (carrito[index].cantidad >= carrito[index].stock) {
                mostrarNotificacion(`⚠️ Stock insuficiente para ${carrito[index].nombre}`, 'error');
            }

            actualizarCarrito();
        }

        if (eliminar) {
            const index = eliminar.dataset.index;
            carrito.splice(index, 1);
            actualizarCarrito();
        }
    });

    // --- MODALES ---
    btnCarrito.addEventListener('click', () => {
        if (carrito.length === 0) {
            mostrarNotificacion('🛒 Carrito vacío', 'info');
            return;
        }
        abrirModal(modalCarrito);
    });

    btnCerrarCarrito.addEventListener('click', () => cerrarModal(modalCarrito));

    btnSiguiente.addEventListener('click', () => {
        cerrarModal(modalCarrito);
        abrirModal(modalMesa);
    });

    btnCancelarMesa.addEventListener('click', () => cerrarModal(modalMesa));

    btnMesaSiguiente.addEventListener('click', () => {
        if (!mesaSeleccionada) {
            mostrarNotificacion('⚠️ Debes seleccionar una mesa', 'error');
            return;
        }
        cerrarModal(modalMesa);
        abrirModal(modalPago);
    });

    btnCancelarPago.addEventListener('click', () => cerrarModal(modalPago));

    // --- SELECCIONAR MESA ---
    document.addEventListener('click', (e) => {
        const mesaItem = e.target.closest('.mesa-item');
        if (mesaItem && mesaItem.dataset.estado === 'disponible') {
            // Deseleccionar todas las mesas
            document.querySelectorAll('.mesa-item').forEach(m => {
                m.classList.remove('seleccionada');
            });
            
            // Seleccionar la mesa clickeada
            mesaItem.classList.add('seleccionada');
            mesaSeleccionada = mesaItem.dataset.id;
            
            // Habilitar botón siguiente
            btnMesaSiguiente.disabled = false;
            
            mostrarNotificacion(`✅ Mesa ${mesaItem.querySelector('p').textContent} seleccionada`, 'exito');
        }
    });

    // --- FUNCIONES MODALES ---
    function abrirModal(modal) {
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function cerrarModal(modal) {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // --- ENVIAR PEDIDO ---
    formPedido.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (carrito.length === 0) {
            mostrarNotificacion('❌ El carrito está vacío', 'error');
            return;
        }
        if (!mesaSeleccionada) {
            mostrarNotificacion('❌ Debes seleccionar una mesa', 'error');
            return;
        }

        const metodo_pago = document.getElementById('metodo_pago').value;
        const total = parseFloat(totalSpan.textContent);

        try {
            const response = await fetch('/pedido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    metodo_pago,
                    total,
                    mesa_id: mesaSeleccionada,
                    productos: carrito
                })
            });

            const data = await response.json();
            if (data.success) {
                mostrarNotificacion('✅ Pedido realizado con éxito', 'exito');
                cerrarModal(modalPago);
                carrito.length = 0;
                actualizarCarrito();
                mesaSeleccionada = null;
                
                // Resetear selección de mesa
                document.querySelectorAll('.mesa-item').forEach(m => {
                    m.classList.remove('seleccionada');
                });
                btnMesaSiguiente.disabled = true;
            } else {
                mostrarNotificacion(`❌ ${data.message}`, 'error');
            }
        } catch (error) {
            console.error(error);
            mostrarNotificacion('❌ Error al enviar el pedido', 'error');
        }
    });

    // --- NOTIFICACIONES ---
    function mostrarNotificacion(mensaje, tipo) {
        const notificacion = document.getElementById('notificacionPedido');
        if (notificacion) {
            notificacion.textContent = mensaje;
            notificacion.className = `notificacion-pedido ${tipo}`;
            notificacion.style.display = 'block';
            
            setTimeout(() => {
                notificacion.style.display = 'none';
            }, 3000);
        }
    }

    // --- INICIALIZAR ---
    actualizarCarrito();
});