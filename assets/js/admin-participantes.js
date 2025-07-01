document.addEventListener("DOMContentLoaded", () => {
  // 1) Columnas dropdown toggle
  const dd = document.querySelector(".columns-dropdown");
  document.getElementById("columnsBtn").addEventListener("click", () => {
    dd.classList.toggle("open");
  });
  // cerrar al hacer click afuera
  document.addEventListener("click", (e) => {
    if (!dd.contains(e.target)) dd.classList.remove("open");
  });

  // 2) Mostrar/ocultar columnas
  document
    .querySelectorAll(".columns-menu input[type=checkbox]")
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

  // 3) Buscador global
  const searchInput = document.getElementById("searchInput");
  searchInput.addEventListener("input", () => {
    const q = searchInput.value.toLowerCase();
    document.querySelectorAll(".data-table tbody tr").forEach((tr) => {
      const text = Array.from(tr.querySelectorAll("td"))
        .filter((td) => td.offsetParent !== null) // solo visibles
        .map((td) => td.textContent.toLowerCase())
        .join(" ");
      tr.style.display = text.includes(q) ? "" : "none";
    });
  });

  // 4) Eliminar participante
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tr = btn.closest("tr");
      const boleta = tr.dataset.boleta;
      if (!confirm("¿Eliminar este participante?")) return;
      fetch(`/expoescom/admin/api/participantes/${boleta}`, {
        method: "DELETE",
      })
        .then((r) => r.json())
        .then((js) => {
          if (js.success) tr.remove();
          else alert(js.error || "Error al eliminar");
        });
    });
  });

  // 5) Toggle ganador
  document.querySelectorAll(".btn-toggle-winner").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tr = btn.closest("tr");
      const boleta = tr.dataset.boleta;
      const cellIcon = tr.querySelector('td[data-col="es_ganador"] i');
      const btnIcon = btn.querySelector("i");

      const wasWinner = cellIcon.classList.contains("fa-trophy");

      fetch(`/expoescom/admin/api/participantes/${boleta}/ganador`, {
        method: "POST",
      })
        .then((r) => r.json())
        .then((js) => {
          if (!js.success) {
            return alert(js.error || "Error al alternar ganador");
          }
          // invertimos iconos
          if (wasWinner) {
            cellIcon.classList.replace("fa-trophy", "fa-medal");
            btnIcon.classList.replace("fa-trophy", "fa-medal");
          } else {
            cellIcon.classList.replace("fa-medal", "fa-trophy");
            btnIcon.classList.replace("fa-medal", "fa-trophy");
          }
        });
    });
  });
});
