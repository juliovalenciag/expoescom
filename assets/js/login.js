document.addEventListener("DOMContentLoaded", () => {
  // 1. Referencias
  const form = document.getElementById("loginForm");
  const identifier = document.getElementById("identifier");
  const password   = document.getElementById("password");
  const idError    = document.getElementById("identifierError");
  const pwError    = document.getElementById("passwordError");
  const eyeBtn     = document.querySelector(".eye-btn");

  // 2. Regex
  const boletaRx = /^(?:\d{10}|(?:PE|PP)\d{8})$/;
  const emailRx  = /^[\w.+-]+@alumno\.ipn\.mx$/;

  // 3. Validadores
  function validateIdentifier() {
    const v = identifier.value.trim();
    if (boletaRx.test(v) || emailRx.test(v)) {
      idError.textContent = "";
      identifier.classList.remove("invalid");
      return true;
    } else {
      idError.textContent = "Debe ser boleta (10 dígitos / PExxxxxxx) o correo @alumno.ipn.mx";
      identifier.classList.add("invalid");
      return false;
    }
  }

  function validatePassword() {
    if (password.value.trim() !== "") {
      pwError.textContent = "";
      password.classList.remove("invalid");
      return true;
    } else {
      pwError.textContent = "La contraseña no puede ir vacía";
      password.classList.add("invalid");
      return false;
    }
  }

  // 4. Eventos de blur
  identifier.addEventListener("blur", validateIdentifier);
  password.addEventListener("blur", validatePassword);

  // 5. Toggle “ojo”
  eyeBtn.addEventListener("click", () => {
    const show = password.type === "password";
    password.type = show ? "text" : "password";
    eyeBtn.firstElementChild.classList.toggle("fa-eye-slash", show);
    eyeBtn.firstElementChild.classList.toggle("fa-eye", !show);
  });

  // 6. Al enviar
  form.addEventListener("submit", (e) => {
    const okId = validateIdentifier();
    const okPw = validatePassword();
    if (!okId || !okPw) {
      e.preventDefault();
      // Llevar al primer error visible
      const firstErr = form.querySelector(".invalid");
      firstErr.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  });
});
