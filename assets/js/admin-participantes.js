document.getElementById("filtro").addEventListener("keyup", function () {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("#tabla-participantes tbody tr").forEach((tr) => {
    const texto = tr.textContent.toLowerCase();
    tr.style.display = texto.includes(filtro) ? "" : "none";
  });
});
