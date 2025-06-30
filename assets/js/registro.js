document.addEventListener("DOMContentLoaded", () => {
  // ——————————————————————————————————————————————————————————
  // 1) Steppers, botones, etc.
  // ——————————————————————————————————————————————————————————
  const form = document.getElementById("registroForm");
  const steps = Array.from(document.querySelectorAll(".step"));
  const contents = Array.from(document.querySelectorAll(".step-content"));
  const nextBtns = document.querySelectorAll(".btn-next");
  const prevBtns = document.querySelectorAll(".btn-prev");
  const barFill = document.querySelector(".bar-fill");
  let current = 1;

  function updateStepper() {
    steps.forEach((s, i) => {
      s.classList.toggle("active", i + 1 === current);
      contents[i].classList.toggle("active", i + 1 === current);
    });
    const pct = ((current - 1) / (steps.length - 1)) * 100;
    barFill.style.width = pct + "%";
  }

  nextBtns.forEach((b) =>
    b.addEventListener("click", () => {
      current = Math.min(current + 1, steps.length);
      updateStepper();
    })
  );
  prevBtns.forEach((b) =>
    b.addEventListener("click", () => {
      current = Math.max(current - 1, 1);
      updateStepper();
    })
  );
  steps.forEach((s, i) =>
    s.addEventListener("click", () => {
      current = i + 1;
      updateStepper();
    })
  );
  updateStepper();

  // ——————————————————————————————————————————————————————————
  // 2) Regex & toggles
  // ——————————————————————————————————————————————————————————
  const regex = {
    boleta: /^(?:\d{10}|(?:PE|PP)\d{8})$/,
    nombre: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$/,
    telefono: /^\d{10}$/,
    curp: /^[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS)[B-DF-HJ-NP-TV-ZÑ]{3}[A-Z\d]\d$/,
    correo: /^[\w.+-]+@alumno\.ipn\.mx$/,
    contrasena: /^(?=.*[A-Z])(?=.*\d)(?=.*[#?!@$%^&*\-_]).{6,}$/,
    unidad_aprendizaje: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
    nombre_proyecto: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
    nombre_equipo: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
  };

  document.querySelectorAll(".eye-btn").forEach((btn) =>
    btn.addEventListener("click", () => {
      const inp = document.getElementById(btn.dataset.target);
      const show = inp.type === "password";
      inp.type = show ? "text" : "password";
      btn.firstElementChild.classList.toggle("fa-eye-slash", show);
      btn.firstElementChild.classList.toggle("fa-eye", !show);
    })
  );

  const curpInput = document.getElementById("curp");
  curpInput.addEventListener(
    "input",
    (e) => (e.target.value = e.target.value.toUpperCase())
  );
  curpInput.addEventListener("blur", () => {
    const ok = regex.curp.test(curpInput.value.trim());
    curpInput.closest(".field-group").classList.toggle("invalid", !ok);
  });

  // ——————————————————————————————————————————————————————————
  // 3) Dinámica de Academias → Unidades (viene de PHP)
  //    PHP inyecta antes de este script:
  //      <script>window.unidadesPorAcademia = <?= json_encode($unidadesPorAcademia, JSON_UNESCAPED_UNICODE) ?>;</script>
  // ——————————————————————————————————————————————————————————
  const horarioEl = document.getElementById("horario");
  const academiaEl = document.getElementById("academia");
  const unidadEl = document.getElementById("unidad");

  // 3.1) Cuando cambie el horario, filtramos academias
  function poblarAcademiasPorHorario() {
    const h = horarioEl.value;
    academiaEl.innerHTML = '<option value="">Selecciona academia</option>';
    window.academias
      .filter((a) => a.horarios.includes(h))
      .forEach((a) => {
        const opt = document.createElement("option");
        opt.value = a.id;
        opt.textContent = a.nombre;
        academiaEl.appendChild(opt);
      });
    // si venimos con old, re-seleccionar
    if (window.oldAcademiaId) academiaEl.value = window.oldAcademiaId;
    poblarUnidades(); // refresca unidades
  }
  horarioEl.addEventListener("change", poblarAcademiasPorHorario);

  // 3.2) Cuando cambie academia -> poblar unidades
  function poblarUnidades() {
    const aid = academiaEl.value;
    unidadEl.innerHTML = '<option value="">Selecciona unidad</option>';
    (window.unidadesPorAcademia[aid] || []).forEach((u) => {
      const opt = document.createElement("option");
      opt.value = u.id;
      opt.textContent = u.nombre;
      unidadEl.appendChild(opt);
    });
    if (window.oldUnidadId) unidadEl.value = window.oldUnidadId;
  }
  academiaEl.addEventListener("change", poblarUnidades);

  // 3.3) disparar carga inicial si venimos con old
  if (window.oldAcademiaId) {
    horarioEl.dispatchEvent(new Event("change"));
    academiaEl.dispatchEvent(new Event("change"));
  }

  // ——————————————————————————————————————————————————————————
  // 4) Submit → Validación y Modal
  // ——————————————————————————————————————————————————————————
  const modal = document.getElementById("confirmModal");
  const detailsEl = document.getElementById("confirmDetails");
  const editBtn = document.getElementById("editBtn");
  const confirmBtn = document.getElementById("confirmBtn");
  const modifyBtn = document.getElementById("modifyBtn");

  // Submit → validación + modal
  function preventSubmit(e) {
    e.preventDefault();

    // limpieza
    let allOk = true;
    form
      .querySelectorAll(".field-group")
      .forEach((f) => f.classList.remove("invalid"));
    document.getElementById("form-error").textContent = "";

    // validaciones...
    const rules = [
      ["boleta", "boleta"],
      ["nombre", "nombre"],
      [
        "genero",
        () => !!document.querySelector('input[name="genero"]:checked'),
      ],
      ["telefono", "telefono"],
      ["curp", "curp"],
      ["semestre", (v) => v !== ""],
      ["carrera", (v) => v !== ""],
      ["correo", "correo"],
      ["contrasena", "contrasena"],
      ["horario", (v) => v !== ""],
      ["academia", (v) => v !== ""],
      ["unidad", (v) => v !== ""],

      ["nombre_proyecto", "nombre_proyecto"],
      ["nombre_equipo", "nombre_equipo"],
    ];
    rules.forEach(([fld, rule]) => {
      let ok;
      if (typeof rule === "string") {
        const el = form.querySelector(`#${fld}`);
        ok = regex[rule].test(el.value.trim());
        if (!ok) el.closest(".field-group").classList.add("invalid");
      } else {
        ok = rule();
        if (!ok) document.querySelector(".sexo-group").classList.add("invalid");
      }
      if (!ok) allOk = false;
    });

    if (!allOk) {
      document.getElementById("form-error").textContent =
        "Corrige los campos resaltados.";
      const first = form.querySelector(".invalid").closest(".step-content");
      current = +first.dataset.step;
      updateStepper();
      return;
    }

    // Construir tabla de resumen
    const fields = [
      { id: "boleta", label: "Boleta" },
      { id: "nombre", label: "Nombre(s)" },
      { id: "apellido_paterno", label: "Apellido Paterno" },
      { id: "apellido_materno", label: "Apellido Materno" },
      { id: "genero", label: "Género" },
      { id: "telefono", label: "Teléfono" },
      { id: "curp", label: "CURP" },
      { id: "semestre", label: "Semestre" },
      { id: "carrera", label: "Carrera" },
      { id: "correo", label: "Correo" },
      { id: "horario", label: "Horario" },
      { id: "academia", label: "Academia" },
      { id: "unidad", label: "Unidad Aprendizaje" },
      { id: "nombre_proyecto", label: "Proyecto" },
      { id: "nombre_equipo", label: "Equipo" },
    ];
    let html = '<table class="confirm-table">';
    fields.forEach((f) => {
      let value = "";
      if (f.id === "genero") {
        value = (document.querySelector('input[name="genero"]:checked') || {})
          .value;
      } else {
        const el = form.querySelector(`#${f.id}`);
        value = el ? el.value : "";
      }
      html += `<tr><th>${f.label}</th><td>${value}</td></tr>`;
    });
    html += "</table>";
    detailsEl.innerHTML = html;
    modal.classList.add("active");
  }

  document.getElementById("clearForm").addEventListener("click", () => {
    if (confirm("¿Estás seguro de que quieres borrar todos los datos?")) {
      document.getElementById("registroForm").reset();
      document
        .querySelectorAll(".step-content")
        .forEach((s) => s.classList.remove("active"));
      document
        .querySelector('.step-content[data-step="1"]')
        .classList.add("active");
      document
        .querySelectorAll(".step")
        .forEach((s) => s.classList.remove("active"));
      document.querySelector('.step[data-step="1"]').classList.add("active");
    }
  });

  // Asocia y más
  form.addEventListener("submit", preventSubmit);

  // Botones del modal
  editBtn.addEventListener("click", () => modal.classList.remove("active"));

  confirmBtn.addEventListener("click", () => {
    modal.classList.remove("active");
    form.removeEventListener("submit", preventSubmit);
    form.submit(); // se envía al servidor y ejecuta register()
  });

  modifyBtn.addEventListener("click", () => {
    form
      .querySelectorAll("input,select,button")
      .forEach((i) => (i.disabled = false));
    modifyBtn.style.display = "none";
    document.getElementById("form-success").textContent = "";
    current = 1;
    updateStepper();
  });
});
