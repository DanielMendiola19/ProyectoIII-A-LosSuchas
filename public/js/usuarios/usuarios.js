// Modal elementos
const modal = document.getElementById("modalConfirmacionUsuario");
const mensaje = document.getElementById("mensajeEliminarUsuario");
const btnConfirmar = document.getElementById("btnConfirmarEliminarUsuario");
const btnCancelar = document.getElementById("btnCancelarEliminarUsuario");

let formActual = null;

// Abrir modal
document.querySelectorAll(".abrir-modal").forEach(btn => {
    btn.addEventListener("click", () => {
        const nombre = btn.getAttribute("data-nombre");

        mensaje.textContent = `¿Realmente deseas eliminar a ${nombre}?`;
        formActual = btn.closest("form");

        modal.classList.add("show");
    });
});

// Confirmar eliminación
btnConfirmar.addEventListener("click", () => {
    if (formActual) formActual.submit();
});

// Cancelar
btnCancelar.addEventListener("click", () => {
    modal.classList.remove("show");
});

// Cerrar al hacer click fuera
modal.addEventListener("click", e => {
    if (e.target === modal) {
        modal.classList.remove("show");
    }
});
