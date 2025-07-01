// admin-login.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const usuario = document.getElementById("usuario");
  const password = document.getElementById("password");
  const uError = document.getElementById("usuarioError");
  const pError = document.getElementById("passwordError");
  const eyeBtns = document.querySelectorAll(".eye-btn");

  const userRx = /^[a-zA-Z0-9_]{4,30}$/;

  function validateUser() {
    const v = usuario.value.trim();
    if (!v) {
      uError.textContent = "Usuario es obligatorio.";
      return false;
    }
    if (!userRx.test(v)) {
      uError.textContent = "Usuario inválido.";
      return false;
    }
    uError.textContent = "";
    return true;
  }

  function validatePass() {
    if (!password.value.trim()) {
      pError.textContent = "Contraseña es obligatoria.";
      return false;
    }
    pError.textContent = "";
    return true;
  }

  usuario.addEventListener("blur", validateUser);
  password.addEventListener("blur", validatePass);

  eyeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const tgt = document.getElementById(btn.dataset.target);
      if (tgt.type === "password") {
        tgt.type = "text";
        btn.firstElementChild.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        tgt.type = "password";
        btn.firstElementChild.classList.replace("fa-eye-slash", "fa-eye");
      }
    });
  });

  form.addEventListener("submit", (e) => {
    const okU = validateUser();
    const okP = validatePass();
    if (!okU || !okP) {
      e.preventDefault();
    }
  });
});
