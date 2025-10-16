document.addEventListener("DOMContentLoaded", () => {
  const filas = document.querySelectorAll(".tabla-detalle tbody tr");
  const totalEl = document.getElementById("totalPedido");
  let total = 0;

  // Calcular total dinámicamente
  filas.forEach(fila => {
    const celda = fila.querySelector("td[data-label='Total']");
    if (celda) {
      total += parseFloat(celda.textContent) || 0;
    }

    // Efecto hover + transición suave
    fila.addEventListener("mouseenter", () => {
      fila.style.transform = "scale(1.01)";
      fila.style.transition = "transform 0.2s ease";
    });
    fila.addEventListener("mouseleave", () => {
      fila.style.transform = "scale(1)";
    });
  });

  // Mostrar total formateado
  totalEl.textContent = total.toFixed(2);

  // Pequeña animación al cargar
  const contenedor = document.querySelector(".detalle-pedido");
  contenedor.style.opacity = 0;
  setTimeout(() => {
    contenedor.style.transition = "opacity 0.8s ease";
    contenedor.style.opacity = 1;
  }, 150);
});
