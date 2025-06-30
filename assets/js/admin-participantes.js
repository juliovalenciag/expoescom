document.addEventListener("DOMContentLoaded", () => {
  const API = "/expoescom/admin/api/participantes";
  const tableBody = document.querySelector("#participantsTable tbody");
  const searchBox = document.getElementById("searchBox");
  const winnerFilter = document.getElementById("winnerFilter");
  const btnNew = document.getElementById("btnNew");
  const modalCreate = document.getElementById("modalCreate");
  const modalEdit = document.getElementById("modalEdit");
  const formCreate = document.getElementById("formCreate");
  const formEdit = document.getElementById("formEdit");
  const btnDelete = document.getElementById("btnDelete");

  let allRows = [],
    editingBoleta = null;

  // —— 1) Regex idénticos a registro.js ——
  const regex = {
    boleta: /^(?:\d{10}|(?:PE|PP)\d{8})$/,
    nombre: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$/,
    apellido_paterno: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$/,
    apellido_materno: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$/,
    telefono: /^\d{10}$/,
    curp: /^[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS)[B-DF-HJ-NP-TV-ZÑ]{3}[A-Z\d]\d$/,
    correo: /^[\w.+-]+@alumno\.ipn\.mx$/,
    nombre_proyecto: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
    nombre_equipo: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
  };

  // Aplica validación sobre blur en ambos formularios
  function bindValidations(form) {
    Object.entries(regex).forEach(([fld, re]) => {
      const inp = form.querySelector(`[name="${fld}"]`);
      if (!inp) return;
      inp.addEventListener("blur", (e) => {
        const ok = re.test(e.target.value.trim());
        const fg = e.target.closest(".field-group");
        fg.classList.toggle("invalid", !ok);
      });
    });
    // especial: confirmar contraseñas
    const p1 = form.querySelector('[name="password"]');
    const p2 = form.querySelector('[name="password2"]');
    if (p1 && p2) {
      p2.addEventListener("blur", (e) => {
        const ok = p1.value === p2.value && p2.value.length > 0;
        p2.closest(".field-group").classList.toggle("invalid", !ok);
      });
    }
  }

  bindValidations(formCreate);
  bindValidations(formEdit);

  // —— 2) Cascada: horario → academias → unidades ——
  // const selHorC = document.getElementById("createHorario");
  // const selAcaC = document.getElementById("createAcademia");
  // const selUniC = document.getElementById("createUnidad");
  // const selHorE = document.getElementById("editHorario");
  // const selAcaE = document.getElementById("editAcademia");
  // const selUniE = document.getElementById("editUnidad");

  // function clear(sel) {
  //   sel.innerHTML = `<option value="">--</option>`;
  //   sel.disabled = true;
  // }

  // function setupCascade(hor, aca, uni) {
  //   clear(aca);
  //   clear(uni);
  //   hor.addEventListener("change", () => {
  //     clear(aca);
  //     clear(uni);
  //     const h = hor.value;
  //     if (!h) return;
  //     aca.disabled = false;
  //     window.academias
  //       .filter((a) => a.horarios.includes(h))
  //       .forEach((a) => aca.add(new Option(a.nombre, a.id)));
  //   });
  //   aca.addEventListener("change", () => {
  //     clear(uni);
  //     const id = aca.value;
  //     if (!id) return;
  //     uni.disabled = false;
  //     (window.unidadesPorAcademia[id] || []).forEach((u) =>
  //       uni.add(new Option(u.nombre, u.id))
  //     );
  //   });
  // }
  // if (selHorC && selAcaC && selUniC) {
  //   setupCascade(selHorC, selAcaC, selUniC);
  // }
  // if (selHorE && selAcaE && selUniE) {
  //   setupCascade(selHorE, selAcaE, selUniE);
  // }

  // —— 3) CRUD en tabla ——
  async function loadTable() {
    tableBody.innerHTML = `<tr><td colspan="10" class="loading">Cargando participantes…</td></tr>`;
    try {
      const res = await fetch(API);
      const payload = await res.json();

      let data;
      if (Array.isArray(payload)) {
        data = payload;
      } else if (payload.success === true && Array.isArray(payload.data)) {
        data = payload.data;
      } else {
        throw new Error(payload.error || "Formato de respuesta inesperado");
      }

      console.log("Participantes cargados:", data); // <-- Mueve aquí el console.log

      allRows = data;
      applyFilters();
    } catch (err) {
      console.error("Error al cargar participantes:", err);
      tableBody.innerHTML = `<tr><td colspan="10" class="loading">Error al cargar datos</td></tr>`;
    }
  }

  function applyFilters() {
    let rows = [...allRows];
    const q = searchBox.value.trim().toLowerCase();
    if (q)
      rows = rows.filter((r) =>
        Object.values(r).some((v) => String(v).toLowerCase().includes(q))
      );
    if (winnerFilter.checked) rows = rows.filter((r) => r.es_ganador);
    renderTable(rows);
  }

  function renderTable(rows) {
    console.log("Renderizando tabla con:", rows);
    if (!rows.length) {
      tableBody.innerHTML = `<tr><td colspan="10" class="loading">No hay participantes</td></tr>`;
      return;
    }
    tableBody.innerHTML = rows
      .map(
        (r) => `
      <tr>
        <td>${r.boleta}</td>
        <td>${r.nombre} ${r.apellido_paterno} ${r.apellido_materno}</td>
        <td>${r.carrera}</td>
        <td>${r.correo}</td>
        <td>${r.nombre_equipo}</td>
        <td>${r.nombre_proyecto}</td>
        <td>${r.salon_id || "—"}</td>
        <td>${r.bloque || "—"}</td>
        <td>${r.es_ganador ? "Sí" : "No"}</td>
        <td class="actions">
          <button class="action-btn btn-toggle" data-boleta="${r.boleta}">
            <i class="fa-solid fa-trophy${r.es_ganador ? "" : "-regular"}"></i>
          </button>
          <button class="action-btn btn-assign" data-equipo="${r.equipo_id}">
            <i class="fa-solid fa-door-open"></i>
          </button>
          <button class="action-btn btn-edit" data-boleta="${r.boleta}">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
        </td>
      </tr>
    `
      )
      .join("");
    attachRowHandlers();
  }

  function attachRowHandlers() {
    document.querySelectorAll(".btn-toggle").forEach((b) =>
      b.addEventListener("click", async () => {
        await fetch(`${API}/${b.dataset.boleta}/ganador`, { method: "POST" });
        loadTable();
      })
    );
    document
      .querySelectorAll(".btn-assign")
      .forEach((b) =>
        b.addEventListener(
          "click",
          () => (location.href = `/expoescom/admin/asignar/${b.dataset.equipo}`)
        )
      );
    document
      .querySelectorAll(".btn-edit")
      .forEach((b) =>
        b.addEventListener("click", () => openEditModal(b.dataset.boleta))
      );
  }

  // —— 4) Abrir/Cerrar modales ——
  btnNew.addEventListener("click", () => {
    formCreate.reset();
    clear(selAcaC);
    clear(selUniC);
    modalCreate.classList.add("active");
  });
  document
    .querySelectorAll(".modal-close")
    .forEach((x) =>
      x.addEventListener("click", (e) =>
        document
          .getElementById(e.currentTarget.dataset.close)
          .classList.remove("active")
      )
    );

  function openEditModal(boleta) {
    editingBoleta = boleta;
    const rec = allRows.find((r) => r.boleta === boleta);
    if (!rec) return;
    formEdit.reset();
    Object.entries(rec).forEach(([k, v]) => {
      const inp = formEdit.querySelector(`[name="${k}"]`);
      if (inp) inp.value = v;
    });
    // forzar cascada
    selHorE.dispatchEvent(new Event("change"));
    selAcaE.value = rec.academia_id;
    selAcaE.dispatchEvent(new Event("change"));
    modalEdit.classList.add("active");
  }

  // —— 5) Crear / Editar / Eliminar ——
  formCreate.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(formCreate));
    const res = await fetch(API, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      modalCreate.classList.remove("active");
      loadTable();
    } else {
      alert("Error al crear");
    }
  });

  formEdit.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!editingBoleta) return;
    const payload = Object.fromEntries(new FormData(formEdit));
    const res = await fetch(`${API}/${editingBoleta}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      modalEdit.classList.remove("active");
      loadTable();
    } else {
      alert("Error al guardar");
    }
  });

  btnDelete?.addEventListener("click", async () => {
    if (!editingBoleta || !confirm("¿Eliminar este participante?")) return;
    const res = await fetch(`${API}/${editingBoleta}`, { method: "DELETE" });
    if (res.ok) {
      modalEdit.classList.remove("active");
      loadTable();
    } else {
      alert("No se pudo eliminar");
    }
  });

  // —— 6) Filtros en tiempo real ——
  searchBox.addEventListener("input", applyFilters);
  winnerFilter.addEventListener("change", applyFilters);

  // —— Iniciar ——
  loadTable();
});
