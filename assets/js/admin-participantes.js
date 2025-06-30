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
  document
    .querySelector(".btn-action.btn-new")
    ?.addEventListener("click", (e) => {
      e.preventDefault();
      abrirModal("modalCrear");
      document.getElementById("formCrear").reset();
    });

  // Botón eliminar
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", () => {
      const boleta = btn.dataset.boleta;
      Swal.fire({
        icon: "warning",
        title: "¿Eliminar participante?",
        text: `Esta acción eliminará al participante con boleta ${boleta}`,
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/expoescom/admin/api/participantes/${boleta}`, {
            method: "DELETE",
          }).then(() => location.reload());
        }
      });
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
      const res = await fetch(
        `/expoescom/admin/api/participantes/${boleta}/ganador`,
        {
          method: "POST",
        }
      );
      if (res.ok) location.reload();
    });
  });

  document
    .querySelectorAll("#formCrear input, #formCrear select")
    .forEach((input) => {
      input.addEventListener("input", () => {
        if (input.value.trim() !== "") {
          input.classList.remove("input-error");
        }
      });
    });

  function marcarError(input, mensaje = "") {
    input.classList.add("input-error");
    if (mensaje) {
      input.setCustomValidity(mensaje);
    } else {
      input.setCustomValidity("");
    }
  }

  function limpiarError(input) {
    input.classList.remove("input-error");
    input.setCustomValidity("");
  }
  // Formulario Crear
  document.getElementById("formCrear").addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    let hasError = false;

    // Validar campos requeridos
    [
      "boleta",
      "nombre",
      "apellido_paterno",
      "apellido_materno",
      "curp", 
      "correo",
      "telefono",
      "semestre",
      "carrera",
      "genero",
      "password",
    ].forEach((field) => {
      const input = form.querySelector(`[name="${field}"]`);
      if (!formData.get(field).trim()) {
        input.classList.add("input-error");
        hasError = true;
      }
    });

    if (hasError) {
      alert("Por favor llena todos los campos obligatorios.");
      return;
    }

    // Enviar a la API
    const json = Object.fromEntries(formData.entries());
    const res = await fetch("/expoescom/admin/api/participantes", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(json),
    });

    if (res.ok) {
      cerrarModal("modalCrear");
      location.reload();
    } else {
      const error = await res.json();
      console.error("Error en servidor:", error);
      alert("Error al crear participante: " + (error?.error || "desconocido"));
    }
  });

  // Formulario Editar
  document
    .getElementById("formEditar")
    .addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData.entries());
      const boleta = data.boleta_original;
      delete data.boleta_original;
      const { password, ...dataToSend } = data;
      if (password && password.trim().length >= 6) {
        dataToSend.password = password.trim();
      }

      try {
        const res = await fetch(
          `/expoescom/admin/api/participantes/${boleta}`,
          {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataToSend),
          }
        );

        if (!res.ok) throw new Error("Error al actualizar");
        Swal.fire(
          "Actualizado",
          "Los datos han sido modificados",
          "success"
        ).then(() => location.reload());
      } catch (err) {
        Swal.fire("Error", err.message, "error");
      }
    });
});

// Utilidades modales
function abrirModal(id) {
  document.getElementById(id).classList.add("active");
}
function cerrarModal(id) {
  document.getElementById(id).classList.remove("active");
}
