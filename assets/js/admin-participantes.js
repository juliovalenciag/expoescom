// assets/js/admin-participantes.js
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("partModal");
  const form = document.getElementById("partForm");
  const nuevoBtn = document.getElementById("nuevoPartBtn");
  const cancelBtn = document.getElementById("partCancel");
  const tbody = document.querySelector("table.data-table tbody");
  let editingBoleta = null;

  nuevoBtn.addEventListener("click", () => {
    editingBoleta = null;
    form.reset();
    document.getElementById("modalTitle").textContent = "Nuevo Participante";
    form.partBoleta.disabled = false;
    modal.hidden = false;
  });
  cancelBtn.addEventListener("click", () => (modal.hidden = true));

  tbody.addEventListener("click", (e) => {
    const tr = e.target.closest("tr");
    if (!tr) return;
    const boleta = tr.dataset.boleta;

    // EDITAR
    if (e.target.closest(".btn-edit")) {
      editingBoleta = boleta;
      document.getElementById("modalTitle").textContent = "Editar Participante";
      form.partBoleta.value = boleta;
      form.partNombre.value = tr.children[1].textContent;
      const apellidos = tr.children[2].textContent.split(" ");
      form.partApellidoP.value = apellidos[0] || "";
      form.partApellidoM.value = apellidos[1] || "";
      form.partGenero.value = tr.children[5].textContent;
      form.partTelefono.value = tr.children[4].textContent;
      form.partSemestre.value = tr.children[6].textContent;
      form.partCarrera.value = tr.children[7].textContent;
      form.partCorreoLocal.value = tr.children[3].textContent.split("@")[0];
      form.partBoleta.disabled = true;
      form.partPassword.value = "";
      modal.hidden = false;
    }

    // ELIMINAR
    if (e.target.closest(".btn-delete")) {
      if (!confirm("Eliminar participante?")) return;
      fetch(`/expoescom/admin/api/participantes/${boleta}`, {
        method: "DELETE",
      })
        .then((r) => r.json())
        .then((js) => {
          if (js.success) tr.remove();
          else alert(js.error);
        });
    }

    // TOGGLE GANADOR
    if (e.target.closest(".btn-toggle")) {
      fetch(`/expoescom/admin/api/participantes/${boleta}/ganador`, {
        method: "POST",
      })
        .then((r) => r.json())
        .then((js) => {
          if (js.success) location.reload();
          else alert(js.error);
        });
    }
  });

  form.addEventListener("submit", (ev) => {
    ev.preventDefault();
    const data = {
      boleta: form.partBoleta.value.trim(),
      nombre: form.partNombre.value.trim(),
      apellido_paterno: form.partApellidoP.value.trim(),
      apellido_materno: form.partApellidoM.value.trim(),
      genero: form.partGenero.value,
      telefono: form.partTelefono.value.trim(),
      semestre: form.partSemestre.value,
      carrera: form.partCarrera.value,
      correo: form.partCorreoLocal.value.trim() + "@alumno.ipn.mx",
    };
    if (form.partPassword.value) {
      data.password = form.partPassword.value;
    }
    const url = editingBoleta
      ? `/expoescom/admin/api/participantes/${editingBoleta}`
      : "/expoescom/admin/api/participantes";
    const method = editingBoleta ? "PUT" : "POST";

    fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((r) => r.json())
      .then((js) => {
        if (js.success) location.reload();
        else alert(js.error);
      });
  });
});
