document.addEventListener("DOMContentLoaded", () => {
  // Confirm antes de borrar
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      if (!confirm("¿Seguro que deseas eliminar este registro?")) {
        e.preventDefault();
      }
    });
  });
});
