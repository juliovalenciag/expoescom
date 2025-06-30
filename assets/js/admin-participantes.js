document.addEventListener("DOMContentLoaded", () => {
  const tabla = document.querySelector("#participantsTable tbody");

  // Filtro en tiempo real
  document.getElementById("searchBox").addEventListener("input", function () {
    const filtro = this.value.toLowerCase();
    tabla.querySelectorAll("tr").forEach((tr) => {
      const texto = tr.textContent.toLowerCase();
      tr.style.display = texto.includes(filtro) ? "" : "none";
    });
  });

  // Añadir Participante (modal)
  document.querySelector(".btn-action.btn-new")?.addEventListener("click", (e) => {
    e.preventDefault();
    abrirModal("modalCrear");
    document.getElementById("formCrear").reset();
  });

  // Botón eliminar
  tabla.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const boleta = btn.dataset.boleta;
      if (confirm(`¿Eliminar al participante con boleta ${boleta}?`)) {
        const res = await fetch(`/expoescom/admin/api/participantes/${boleta}`, {
          method: "DELETE",
        });
        if (res.ok) location.reload();
      }
    });
  });

  // Botón editar (cargar datos y mostrar modal)
  tabla.querySelectorAll(".btn-edit").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const boleta = btn.dataset.boleta;
      const row = btn.closest("tr");
      const celdas = row.querySelectorAll("td");

      // Prellenar el modal con datos
      const form = document.getElementById("formEditar");
      form.boleta_original.value = boleta;
      form.boleta.value = celdas[0].textContent.trim();
      form.nombre.value = celdas[1].textContent.split(" ")[0];
      form.apellido_paterno.value = celdas[1].textContent.split(" ")[1];
      form.apellido_materno.value = celdas[1].textContent.split(" ")[2];
      form.nombre.disabled = false;
      form.correo.value = celdas[4].textContent.trim();
      form.telefono.value = celdas[7].textContent.trim();
      form.semestre.value = celdas[6].textContent.trim();
      form.carrera.value = celdas[5].textContent.trim();

      abrirModal("modalEditar");
    });
  });

  // Botón ganador
  tabla.querySelectorAll(".btn-toggle-winner").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const boleta = btn.dataset.boleta;
      const res = await fetch(`/expoescom/admin/api/participantes/${boleta}/ganador`, {
        method: "POST",
      });
      if (res.ok) location.reload();
    });
  });

  // Formulario Crear
  document.getElementById("formCrear").addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    const res = await fetch("/expoescom/admin/api/participantes", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (res.ok) location.reload();
    else alert("Error al crear participante");
  });

  // Formulario Editar
  document.getElementById("formEditar").addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;
    const boletaOriginal = form.boleta_original.value;
    const data = Object.fromEntries(new FormData(form).entries());

    const res = await fetch(`/expoescom/admin/api/participantes/${boletaOriginal}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (res.ok) location.reload();
    else alert("Error al actualizar participante");
  });
});

// Utilidades modales
function abrirModal(id) {
  document.getElementById(id).classList.add("active");
}
function cerrarModal(id) {
  document.getElementById(id).classList.remove("active");
}
