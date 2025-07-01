document.addEventListener("DOMContentLoaded", () => {
  // —————————————————————
  // Regex de validación
  // —————————————————————
  const regex = {
    boleta: /^(?:\d{10}|(?:PE|PP)\d{8})$/,
    nombre: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$/,
    telefono: /^\d{10}$/,
    curp: /^[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-ZÑ]{5}[A-Z0-9]\d$/,
    correo_local: /^[\w.+-]+$/,
    password: /^(?=.*[A-Z])(?=.*\d)(?=.*[#?!@$%^&*\-_]).{8,}$/,
    proyecto: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
    equipo: /^[\wÁÉÍÓÚÜÑáéíóúüñ\s]{3,}$/,
  };

  const form = document.getElementById("addForm");
  const fields = Array.from(form.querySelectorAll(".field-group"));
  const formError = document.getElementById("form-error");

  // —————————————————————
  // Validar un campo
  // —————————————————————
  function validateField(group) {
    const input = group.querySelector("input,select");
    const name = input.name;
    const val = input.value.trim();
    let ok = false;

    switch (name) {
      case "boleta":
        ok = regex.boleta.test(val);
        break;
      case "nombre":
      case "apellido_paterno":
      case "apellido_materno":
        ok = regex.nombre.test(val);
        break;
      case "genero":
      case "carrera":
      case "academia_id":
      case "unidad_id":
      case "horario_preferencia":
        ok = val !== "";
        break;
      case "curp":
        ok = regex.curp.test(val);
        break;
      case "telefono":
        ok = regex.telefono.test(val);
        break;
      case "correo_local":
        ok = regex.correo_local.test(val);
        break;
      case "password":
        ok = regex.password.test(val);
        break;
      case "semestre":
        ok = val !== "" && +val >= 1 && +val <= 8;
        break;
      case "nombre_equipo":
        ok = regex.equipo.test(val);
        break;
      case "nombre_proyecto":
        ok = regex.proyecto.test(val);
        break;
    }

    group.classList.toggle("invalid", !ok);
    return ok;
  }

  // —————————————————————
  // Eventos blur → validar
  // —————————————————————
  fields.forEach((g) => {
    const inp = g.querySelector("input,select");
    inp.addEventListener("blur", () => validateField(g));
  });

  // Agrandar CURP en mayúsculas
  document.getElementById("curp").addEventListener("input", (e) => {
    e.target.value = e.target.value.toUpperCase();
  });

  // —————————————————————
  // Dinámica academias → unidades
  // —————————————————————
  const addAcademia = document.getElementById("addAcademia");
  const addUnidad = document.getElementById("addUnidad");
  addAcademia.addEventListener("change", () => {
    addUnidad.innerHTML = '<option value="">Selecciona unidad</option>';
    (window.unidadesPorAcademia[addAcademia.value] || []).forEach((u) => {
      const o = document.createElement("option");
      o.value = u.id;
      o.textContent = u.nombre;
      addUnidad.appendChild(o);
    });
  });

  // —————————————————————
  // Submit → validación global
  // —————————————————————
  form.addEventListener("submit", (e) => {
    let allOk = true;
    formError.textContent = "";

    fields.forEach((g) => {
      if (!validateField(g)) allOk = false;
    });

    if (!allOk) {
      e.preventDefault();
      formError.textContent = "Corrige los campos resaltados.";
    }
  });

  // —————————————————————
  // Toggle ojo
  // —————————————————————
  document.querySelectorAll(".eye-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tgt = document.getElementById(btn.dataset.target);
      const show = tgt.type === "password";
      tgt.type = show ? "text" : "password";
      btn.firstElementChild.classList.toggle("fa-eye-slash", show);
      btn.firstElementChild.classList.toggle("fa-eye", !show);
    });
  });
});
