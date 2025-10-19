// ===================== Sistema de notificaciones mejorado =====================
class NotificacionManager {
    constructor() {
        this.notificacionQueue = [];
        this.isShowing = false;
        this.init();
    }

    init() {
        if (!document.getElementById('notificacion-pedido-container')) {
            const container = document.createElement('div');
            container.id = 'notificacion-pedido-container';
            container.style.cssText = `
                position: fixed;
                top: 25px;
                right: 25px;
                z-index: 3000;
                max-width: 380px;
                width: 100%;
            `;
            document.body.appendChild(container);
        }
    }

    mostrar(mensaje, tipo = 'info', duracion = 3000) {
        const notificacion = { mensaje, tipo, duracion, id: Date.now() + Math.random() };
        this.notificacionQueue.push(notificacion);
        this.procesarCola();
    }

    procesarCola() {
        if (this.isShowing || this.notificacionQueue.length === 0) return;
        this.isShowing = true;
        const notificacion = this.notificacionQueue.shift();
        this.crearNotificacion(notificacion);
    }

    crearNotificacion({ mensaje, tipo, duracion }) {
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion-pedido ${tipo}`;
        notificacion.innerHTML = `
            <div class="notificacion-icono">${this.getIcono(tipo)}</div>
            <span class="notificacion-texto">${mensaje}</span>
            <div class="notificacion-progreso"></div>
        `;

        const container = document.getElementById('notificacion-pedido-container') || document.body;
        container.appendChild(notificacion);

        setTimeout(() => notificacion.classList.add('show'), 10);

        setTimeout(() => {
            notificacion.classList.remove('show');
            setTimeout(() => {
                if (notificacion.parentNode) notificacion.parentNode.removeChild(notificacion);
                this.isShowing = false;
                this.procesarCola();
            }, 400);
        }, duracion);
    }

    getIcono(tipo) {
        const iconos = { exito: '✓', error: '✕', info: 'ℹ', warning: '⚠' };
        return iconos[tipo] || 'ℹ';
    }
}

const notificaciones = new NotificacionManager();

// ===================== Función para cambiar colores según estado =====================
function actualizarColorFila(select) {
    const fila = select.closest('tr');
    const estado = select.value.toLowerCase();
    fila.classList.remove('pendiente', 'en-preparacion', 'listo', 'entregado');
    switch(estado) {
        case 'pendiente': fila.classList.add('pendiente'); break;
        case 'en preparación': fila.classList.add('en-preparacion'); break;
        case 'listo': fila.classList.add('listo'); break;
        case 'entregado': fila.classList.add('entregado'); break;
    }
}

// ===================== DOMContentLoaded =====================
document.addEventListener("DOMContentLoaded", function() {
    // ===== DETALLE DEL PEDIDO (hover + total) =====
    const filasDetalle = document.querySelectorAll(".tabla-detalle tbody tr");
    const totalEl = document.getElementById("totalPedido");

    if (filasDetalle.length > 0 && totalEl) {
        let total = 0;
        filasDetalle.forEach(fila => {
            const celda = fila.querySelector("td[data-label='Total']");
            if (celda) total += parseFloat(celda.textContent) || 0;

            fila.addEventListener("mouseenter", () => {
                fila.style.transform = "scale(1.01)";
                fila.style.transition = "transform 0.2s ease";
            });
            fila.addEventListener("mouseleave", () => fila.style.transform = "scale(1)");
        });
        totalEl.textContent = total.toFixed(2);

        const contenedor = document.querySelector(".detalle-pedido");
        if (contenedor) {
            contenedor.style.opacity = 0;
            setTimeout(() => {
                contenedor.style.transition = "opacity 0.8s ease";
                contenedor.style.opacity = 1;
            }, 150);
        }
    }

    // ===== MANEJO DE FORMULARIOS DE ESTADO EN HISTORIAL =====
    const formsEstado = document.querySelectorAll('.form-estado');
    formsEstado.forEach(form => {
        const select = form.querySelector('select[name="estado"]');
        actualizarColorFila(select); // Color inicial

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const estado = select.value;
            const url = this.action;
            const token = this.querySelector('input[name="_token"]').value;

            notificaciones.mostrar('🔄 Actualizando estado...', 'info');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: JSON.stringify({ estado })
                });

                const data = await response.json();

                if (data.success) {
                    notificaciones.mostrar('✅ Estado actualizado correctamente', 'exito');
                    actualizarColorFila(select);
                } else {
                    notificaciones.mostrar(`❌ ${data.error || 'Error al actualizar estado'}`, 'error');
                }
            } catch (err) {
                console.error(err);
                notificaciones.mostrar('❌ Error de conexión al servidor', 'error');
            }
        });

        // Cambio de color inmediato al seleccionar
        select.addEventListener('change', () => actualizarColorFila(select));
    });
});
