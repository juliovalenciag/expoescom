document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  const fields = [
    { name: "nombre", validator: (v) => v.trim() !== "" },
    { name: "apellido_paterno", validator: (v) => v.trim() !== "" },
    { name: "apellido_materno", validator: (v) => v.trim() !== "" },
    {
      name: "genero",
      validator: (v) => ["Mujer", "Hombre", "Otro"].includes(v),
    },
    { name: "telefono", validator: (v) => /^\d{10}$/.test(v) },
    { name: "correo_local", validator: (v) => /^[\w.+-]+$/.test(v) },
    { name: "semestre", validator: (v) => /^[1-8]$/.test(v) },
    { name: "carrera", validator: (v) => ["ISC", "LCD", "IIA"].includes(v) },
    { name: "academia_id", validator: (v) => v !== "" },
    { name: "unidad_id", validator: (v) => v !== "" },
    { name: "nombre_equipo", validator: (v) => v.trim().length >= 3 },
    { name: "nombre_proyecto", validator: (v) => v.trim().length >= 3 },
  ];

  function validateField(field) {
    const el = form.querySelector(`[name="${field.name}"]`);
    const group = el.closest(".field-group");
    const ok = field.validator(el.value);
    group.classList.toggle("invalid", !ok);
    return ok;
  }

  // al perder foco / cambiar
  fields.forEach((f) => {
    const el = form.querySelector(`[name="${f.name}"]`);
    if (!el) return;
    el.addEventListener(el.tagName === "SELECT" ? "change" : "blur", () =>
      validateField(f)
    );
  });

  // al enviar
  form.addEventListener("submit", (e) => {
    let allOk = true;
    for (const f of fields) {
      if (!validateField(f)) allOk = false;
    }
    if (!allOk) {
      e.preventDefault();
      // scroll al primer error
      const first = form.querySelector(".field-group.invalid");
      first.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  });
});
