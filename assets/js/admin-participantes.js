document
  .querySelectorAll(".column-selector input[type=checkbox]")
  .forEach((cb) => {
    const col = cb.dataset.col;
    const toggle = () => {
      document
        .querySelectorAll(`th[data-col="${col}"], td[data-col="${col}"]`)
        .forEach((el) => (el.style.display = cb.checked ? "" : "none"));
    };
    cb.addEventListener("change", toggle);
    toggle();
  });

// 2) Delete
document.querySelectorAll(".btn-delete").forEach((btn) => {
  btn.addEventListener("click", () => {
    const tr = btn.closest("tr");
    const boleta = tr.dataset.boleta;
    if (!confirm("¿Eliminar este participante?")) return;
    fetch(`/expoescom/admin/api/participantes/${boleta}`, { method: "DELETE" })
      .then((r) => r.json())
      .then((js) => {
        if (js.success) tr.remove();
        else alert(js.error || "Error al eliminar");
      });
  });
});

// 3) Toggle ganador
document.querySelectorAll(".btn-toggle-winner").forEach((btn) => {
  btn.addEventListener("click", () => {
    const tr = btn.closest("tr");
    const boleta = tr.dataset.boleta;
    fetch(`/expoescom/admin/api/participantes/${boleta}/ganador`, {
      method: "POST",
    })
      .then((r) => r.json())
      .then((js) => {
        if (!js.success) return alert(js.error || "Error");
        // actualizar celda “Ganador”
        const cell = tr.querySelector('td[data-col="es_ganador"]');
        const isNow = cell.textContent.trim() === "No";
        cell.textContent = isNow ? "Sí" : "No";
        // actualizar icono
        const ico = btn.querySelector("i");
        ico.classList.toggle("fa-trophy", isNow);
        ico.classList.toggle("fa-medal", !isNow);
      });
  });
});
