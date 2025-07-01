document.addEventListener("DOMContentLoaded", () => {
  // Elementos
  const salonesTable = document.querySelector("#salonesTable tbody");
  const bloquesTable = document.querySelector("#bloquesTable tbody");
  const salonModal = document.getElementById("salonModal");
  const bloqueModal = document.getElementById("bloqueModal");
  const salonForm = document.getElementById("salonForm");
  const bloqueForm = document.getElementById("bloqueForm");
  const addSalonBtn = document.getElementById("addSalonBtn");
  const addBloqueBtn = document.getElementById("addBloqueBtn");
  let editingSalonId = null;
  let editingBloqueId = null;

  // Cargar datos
  async function loadSalones() {
    const res = await fetch("/expoescom/admin/api/salones");
    const data = await res.json();
    salonesTable.innerHTML = data
      .map(
        (s) => `
      <tr>
        <td>${s.id}</td>
        <td>${s.capacidad}</td>
        <td>
          <button class="btn-edit" data-id="${s.id}"><i class="fa-solid fa-pen"></i></button>
          <button class="btn-delete" data-id="${s.id}"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>
    `
      )
      .join("");
    attachSalonActions();
  }

  async function loadBloques() {
    const res = await fetch("/expoescom/admin/api/bloques");
    const data = await res.json();
    bloquesTable.innerHTML = data
      .map(
        (b) => `
      <tr>
        <td>${b.id}</td>
        <td>${b.tipo}</td>
        <td>${b.hora_inicio}</td>
        <td>${b.hora_fin}</td>
        <td>
          <button class="btn-edit" data-id="${b.id}"><i class="fa-solid fa-pen"></i></button>
          <button class="btn-delete" data-id="${b.id}"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>
    `
      )
      .join("");
    attachBloqueActions();
  }

  // Acciones Salones
  function attachSalonActions() {
    salonModal.hidden = true;
    salonForm.reset();
    editingSalonId = null;

    salonesTable.querySelectorAll(".btn-edit").forEach((btn) => {
      btn.onclick = async () => {
        editingSalonId = btn.dataset.id;
        document.getElementById("salonModalTitle").textContent = "Editar Salón";
        const res = await fetch(
          `/expoescom/admin/api/salones/${editingSalonId}`
        );
        const s = await res.json();
        salonForm.id.value = s.id;
        salonForm.capacidad.value = s.capacidad;
        salonForm.id.disabled = true;
        salonModal.hidden = false;
      };
    });

    salonesTable.querySelectorAll(".btn-delete").forEach((btn) => {
      btn.onclick = async () => {
        if (!confirm("¿Eliminar este salón?")) return;
        await fetch(`/expoescom/admin/api/salones/${btn.dataset.id}`, {
          method: "DELETE",
        });
        loadSalones();
      };
    });
  }

  // Acciones Bloques
  function attachBloqueActions() {
    bloqueModal.hidden = true;
    bloqueForm.reset();
    editingBloqueId = null;

    bloquesTable.querySelectorAll(".btn-edit").forEach((btn) => {
      btn.onclick = async () => {
        editingBloqueId = btn.dataset.id;
        document.getElementById("bloqueModalTitle").textContent =
          "Editar Bloque";
        const res = await fetch(
          `/expoescom/admin/api/bloques/${editingBloqueId}`
        );
        const b = await res.json();
        bloqueForm.id.value = b.id;
        bloqueForm.tipo.value = b.tipo;
        bloqueForm.hora_inicio.value = b.hora_inicio;
        bloqueForm.hora_fin.value = b.hora_fin;
        bloqueForm.id.disabled = true;
        bloqueModal.hidden = false;
      };
    });

    bloquesTable.querySelectorAll(".btn-delete").forEach((btn) => {
      btn.onclick = async () => {
        if (!confirm("¿Eliminar este bloque?")) return;
        await fetch(`/expoescom/admin/api/bloques/${btn.dataset.id}`, {
          method: "DELETE",
        });
        loadBloques();
      };
    });
  }

  // Mostrar modal de nuevo
  addSalonBtn.onclick = () => {
    editingSalonId = null;
    salonForm.id.disabled = false;
    document.getElementById("salonModalTitle").textContent = "Nuevo Salón";
    salonForm.reset();
    salonModal.hidden = false;
  };
  addBloqueBtn.onclick = () => {
    editingBloqueId = null;
    bloqueForm.id.disabled = false;
    document.getElementById("bloqueModalTitle").textContent = "Nuevo Bloque";
    bloqueForm.reset();
    bloqueModal.hidden = false;
  };

  // Cancelar
  document.getElementById("cancelSalon").onclick = () =>
    (salonModal.hidden = true);
  document.getElementById("cancelBloque").onclick = () =>
    (bloqueModal.hidden = true);

  // Envío formulario Salón
  salonForm.onsubmit = async (e) => {
    e.preventDefault();
    const url = editingSalonId
      ? `/expoescom/admin/api/salones/${editingSalonId}`
      : "/expoescom/admin/api/salones";
    const method = editingSalonId ? "PUT" : "POST";
    const payload = {
      id: salonForm.id.value.trim(),
      capacidad: salonForm.capacidad.value,
    };
    await fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    salonModal.hidden = true;
    loadSalones();
  };

  // Envío formulario Bloque
  bloqueForm.onsubmit = async (e) => {
    e.preventDefault();
    const url = editingBloqueId
      ? `/expoescom/admin/api/bloques/${editingBloqueId}`
      : "/expoescom/admin/api/bloques";
    const method = editingBloqueId ? "PUT" : "POST";
    const payload = {
      id: bloqueForm.id.value,
      tipo: bloqueForm.tipo.value,
      hora_inicio: bloqueForm.hora_inicio.value,
      hora_fin: bloqueForm.hora_fin.value,
    };
    await fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    bloqueModal.hidden = true;
    loadBloques();
  };

  // Inicializar tabla
  loadSalones();
  loadBloques();
});
