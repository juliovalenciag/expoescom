document.addEventListener("DOMContentLoaded", () => {
  const apiBase = "/expoescom/admin/api/participantes";
  const tableBody = document.querySelector("#participantsTable tbody");
  const searchInput = document.getElementById("globalSearch");
  const btnNew = document.getElementById("btnNew");
  const modal = document.getElementById("modalParticipante");
  const form = document.getElementById("formParticipante");
  const modalTitle = document.getElementById("modalTitle");
  let isEdit = false;
  let editBoleta = null;

  // Campos
  const fields = {
    boleta: form.pBoleta,
    nombre: form.pNombre,
    apellido_paterno: form.pApellidoP,
    apellido_materno: form.pApellidoM,
    carrera: form.pCarrera,
    correo: form.pCorreo,
    nombre_equipo: form.pEquipo,
    nombre_proyecto: form.pProyecto,
  };

  async function loadParticipants() {
    const res = await fetch(apiBase);
    const data = await res.json();
    tableBody.innerHTML = "";
    data.forEach((p) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.boleta}</td>
        <td>${p.nombre} ${p.apellido_paterno} ${p.apellido_materno}</td>
        <td>${p.carrera}</td>
        <td>${p.correo}</td>
        <td>${p.nombre_equipo}<br><small>${p.nombre_proyecto}</small></td>
        <td>${p.academia}<br><small>${p.unidad}</small></td>
        <td>${p.es_ganador ? "🏆" : ""}</td>
        <td>
          <button class="action-btn edit" data-boleta="${
            p.boleta
          }" title="Editar">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button class="action-btn delete" data-boleta="${
            p.boleta
          }" title="Eliminar">
            <i class="fa-solid fa-trash"></i>
          </button>
          <button class="action-btn winner" data-boleta="${
            p.boleta
          }" title="Alternar Ganador">
            <i class="fa-solid fa-award"></i>
          </button>
        </td>
      `;
      tableBody.appendChild(tr);
    });
    attachRowListeners();
  }

  function attachRowListeners() {
    document.querySelectorAll(".edit").forEach((btn) => {
      btn.onclick = () => openModal(true, btn.dataset.boleta);
    });
    document.querySelectorAll(".delete").forEach((btn) => {
      btn.onclick = async () => {
        if (!confirm("Eliminar participante?")) return;
        await fetch(`${apiBase}/${btn.dataset.boleta}`, { method: "DELETE" });
        loadParticipants();
      };
    });
    document.querySelectorAll(".winner").forEach((btn) => {
      btn.onclick = async () => {
        await fetch(`${apiBase}/${btn.dataset.boleta}/toggle-winner`, {
          method: "POST",
        });
        loadParticipants();
      };
    });
  }

  btnNew.addEventListener("click", () => openModal(false));

  function openModal(edit, boleta = null) {
    isEdit = edit;
    editBoleta = boleta;
    modalTitle.textContent = edit
      ? "Editar participante"
      : "Nuevo participante";
    form.reset();
    if (edit) {
      fetch(`${apiBase}`)
        .then((r) => r.json())
        .then((list) => {
          const p = list.find((u) => u.boleta === boleta);
          for (let k in fields) fields[k].value = p[k] || "";
          fields.boleta.disabled = true;
        });
    } else {
      fields.boleta.disabled = false;
    }
    modal.hidden = false;
  }

  document.getElementById("cancelBtn").onclick = () => {
    modal.hidden = true;
  };

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {};
    for (let k in fields) payload[k] = fields[k].value.trim();
    const url = isEdit ? `${apiBase}/${editBoleta}` : apiBase;
    const method = isEdit ? "PUT" : "POST";
    const res = await fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      modal.hidden = true;
      loadParticipants();
    } else {
      alert("Error al guardar");
    }
  });

  // Buscador global
  searchInput.addEventListener("input", () => {
    const term = searchInput.value.toLowerCase();
    tableBody.querySelectorAll("tr").forEach((tr) => {
      tr.style.display = Array.from(tr.cells).some((td) =>
        td.textContent.toLowerCase().includes(term)
      )
        ? ""
        : "none";
    });
  });

  // Inicial
  loadParticipants();
});
