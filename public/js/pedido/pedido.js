document.addEventListener('DOMContentLoaded', () => {
    const carrito = [];
    const btnCarrito = document.getElementById('btnCarrito');
    const modalCarrito = document.getElementById('modalCarrito');
    const modalPago = document.getElementById('modalPago');
    const listaCarrito = document.getElementById('listaCarrito');
    const contadorCarrito = document.getElementById('contadorCarrito');
    const totalSpan = document.getElementById('total');
    const btnCerrarCarrito = document.getElementById('btnCerrarCarrito');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnCancelarPago = document.getElementById('btnCancelarPago');
    const formPedido = document.getElementById('formPedido');

    // --- AGREGAR PRODUCTO AL CARRITO (click en cualquier parte de la tarjeta) ---
    document.addEventListener('click', (e) => {
        // Encontrar la tarjeta del producto clickeada
        const productCard = e.target.closest('.product-card');
        if (!productCard) return;

        // Evitar que se active cuando se hace click en el botón específico (para no duplicar)
        if (e.target.classList.contains('btn-agregar') || e.target.closest('.btn-agregar')) {
            return; // Dejar que el evento del botón maneje esto
        }

        const id = productCard.dataset.id;
        const nombre = productCard.dataset.nombre;
        const precio = parseFloat(productCard.dataset.precio);
        const imagen = productCard.dataset.imagen;

        const existente = carrito.find(p => p.id == id);
        if (existente) {
            existente.cantidad++;
        } else {
            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
        }

        actualizarCarrito();

        // Efecto visual en toda la tarjeta
        productCard.style.transform = 'scale(0.95)';
        setTimeout(() => {
            productCard.style.transform = '';
        }, 200);

        // Mostrar confirmación
        mostrarNotificacion(`✅ ${nombre} agregado al carrito`, 'exito');
    });

    // --- AGREGAR PRODUCTO CON BOTÓN ESPECÍFICO ---
    document.addEventListener('click', (e) => {
        const boton = e.target.closest('.btn-agregar');
        if (!boton) return;

        const card = boton.closest('.product-card');
        if (!card) return;

        const id = card.dataset.id;
        const nombre = card.dataset.nombre;
        const precio = parseFloat(card.dataset.precio);
        const imagen = card.dataset.imagen;

        const existente = carrito.find(p => p.id == id);
        if (existente) {
            existente.cantidad++;
        } else {
            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
        }

        actualizarCarrito();

        // Efecto visual específico del botón
        const originalHTML = boton.innerHTML;
        const originalBackground = boton.style.background;
        
        boton.style.background = 'var(--verde-exito)';
        boton.innerHTML = '<i class="fas fa-check"></i> Agregado';
        
        setTimeout(() => {
            boton.style.background = originalBackground;
            boton.innerHTML = originalHTML;
        }, 1000);
    });

    // --- ACTUALIZAR CARRITO ---
    function actualizarCarrito() {
        listaCarrito.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            listaCarrito.innerHTML = `
                <div class="carrito-vacio">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Tu carrito está vacío</p>
                </div>
            `;
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
                                <span class="precio-unitario">Bs ${p.precio.toFixed(2)} c/u</span>
                                <strong class="precio-total">Bs ${(p.precio * p.cantidad).toFixed(2)}</strong>
                            </div>
                        </div>
                        <button class="btn-eliminar-item" data-index="${i}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                listaCarrito.appendChild(item);
            });
        }

        totalSpan.textContent = total.toFixed(2);
        contadorCarrito.textContent = carrito.reduce((a, b) => a + b.cantidad, 0);
    }

    // --- CONTROL DE CANTIDAD Y ELIMINAR ---
    listaCarrito.addEventListener('click', (e) => {
        const botonCantidad = e.target.closest('.cantidad-btn');
        if (botonCantidad) {
            const index = parseInt(botonCantidad.dataset.index);
            const action = botonCantidad.dataset.action;

            if (action === 'mas') carrito[index].cantidad++;
            if (action === 'menos') {
                carrito[index].cantidad--;
                if (carrito[index].cantidad <= 0) carrito.splice(index, 1);
            }

            actualizarCarrito();
            return;
        }

        const botonEliminar = e.target.closest('.btn-eliminar-item');
        if (botonEliminar) {
            const index = parseInt(botonEliminar.dataset.index);
            carrito.splice(index, 1);
            actualizarCarrito();
        }
    });

    // --- MODALES ---
    btnCarrito.addEventListener('click', () => {
        if (carrito.length === 0) {
            mostrarNotificacion('🛒 Carrito vacío - Agrega algunos productos', 'info');
            return;
        }
        modalCarrito.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });

    btnCerrarCarrito.addEventListener('click', () => {
        modalCarrito.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    btnSiguiente.addEventListener('click', () => {
        modalCarrito.style.display = 'none';
        modalPago.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });

    btnCancelarPago.addEventListener('click', () => {
        modalPago.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modalCarrito || e.target === modalPago) {
            e.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // --- ENVIAR PEDIDO ---
    formPedido.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (carrito.length === 0) {
            mostrarNotificacion('❌ El carrito está vacío', 'error');
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
                body: JSON.stringify({ metodo_pago, total, productos: carrito })
            });

            const data = await response.json();
            if (data.success) {
                mostrarNotificacion('✅ Pedido realizado con éxito', 'exito');
                modalPago.style.display = 'none';
                document.body.style.overflow = 'auto';
                carrito.length = 0;
                actualizarCarrito();
            } else {
                mostrarNotificacion('❌ Error al procesar el pedido', 'error');
            }
        } catch (error) {
            console.error(error);
            mostrarNotificacion('❌ Error de conexión', 'error');
        }
    });

    // --- NOTIFICACIONES ---
    function mostrarNotificacion(mensaje, tipo) {
        let notificacion = document.querySelector('.notificacion-pedido');
        if (!notificacion) {
            notificacion = document.createElement('div');
            notificacion.className = 'notificacion-pedido';
            document.body.appendChild(notificacion);
        }
        notificacion.textContent = mensaje;
        notificacion.className = `notificacion-pedido ${tipo}`;
        notificacion.style.display = 'block';

        setTimeout(() => { notificacion.style.display = 'none'; }, 3000);
    }

    // --- INICIALIZAR ---
    actualizarCarrito();
});