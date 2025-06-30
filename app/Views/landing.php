<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <title>ExpoEscom 2025</title>
    <link rel="icon" href="<?= BASE_PATH ?>/assets/images/favicon.ico" />

    <script
      src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"
      crossorigin="anonymous"
    ></script>

    <link
      href="https://fonts.googleapis.com/css?family=Varela+Round"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/bootstrap.css">
  </head>
  <body id="page-top">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div
        class="container px-4 px-lg-5 d-flex justify-content-between align-items-center"
      >
        <a
          href="https://www.ipn.mx"
          target="_blank"
          class="navbar-logo logo-left"
          aria-label="IPN"
        >
          <img src="/expoescom/assets/images/IPN_Logo.png" alt="Logo IPN" />
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarResponsive"
          aria-controls="navbarResponsive"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div
          class="collapse navbar-collapse justify-content-center"
          id="navbarResponsive"
        >
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_PATH ?>/register">Registro</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_PATH ?>/login/participante">Participante</a>
            </li>
           <li class="nav-item">
              <a class="nav-link" href="<?= BASE_PATH ?>/login/admin">Administrador</a>
            </li>
          </ul> 
        </div>

        <a
          href="https://www.escom.ipn.mx"
          target="_blank"
          class="navbar-logo logo-right"
          aria-label="ESCOM"
        >
          <img src="/expoescom/assets/images/Escom_Logo.png" alt="Logo ESCOM" />
        </a>
      </div>
    </nav>
    <!-- Masthead-->
    <header class="masthead">
      <div
        class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center"
      >
        <div class="d-flex justify-content-center">
          <div class="text-center">
            <h1 class="mx-auto my-0 text-uppercase">EXPOESCOM</h1>
            <h2 class="text-white-50 mx-auto mt-2 mb-5">2025</h2>
            <a class="btn btn-primary" href="/expoescom/register">Registro</a>
          </div>
        </div>
      </div>
    </header>
    <!-- Noticias -->
    <section id="news" class="py-5 text-center">
      <div class="container">
        <h2 class="mb-4">NOTICIAS</h2>
        <div
          id="carouselNoticias"
          class="carousel slide"
          data-bs-ride="carousel"
        >
          <!-- indicadores -->
          <div class="carousel-indicators">
            <button
              type="button"
              data-bs-target="#carouselNoticias"
              data-bs-slide-to="0"
              class="active"
              aria-current="true"
              aria-label="Slide 1"
            ></button>
            <button
              type="button"
              data-bs-target="#carouselNoticias"
              data-bs-slide-to="1"
              aria-label="Slide 2"
            ></button>
            <button
              type="button"
              data-bs-target="#carouselNoticias"
              data-bs-slide-to="2"
              aria-label="Slide 3"
            ></button>
          </div>

          <!-- elementos -->
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="/expoescom/assets/images/slider1.jpg" alt="Noticia 1" />
            </div>
            <div class="carousel-item">
              <img src="/expoescom/assets/images/slider2.jpg" alt="Noticia 2" />
            </div>
            <div class="carousel-item">
              <img src="/expoescom/assets/images/slider3.jpg" alt="Noticia 3" />
            </div>
          </div>

          <!-- controles -->
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#carouselNoticias"
            data-bs-slide="prev"
          >
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#carouselNoticias"
            data-bs-slide="next"
          >
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      </div>
    </section>

    <!-- About-->
    <section class="about-section text-center" id="about">
      <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
          <div class="col-lg-8">
            <h2 class="text-primary mb-1">¿Qué es ExpoEscom?</h2>
            <p class="text-white-50">
              ExpoEscom es el evento anual del Instituto Politécnico Nacional en
              la Escuela Superior de Cómputo donde estudiantes y empresas
              presentan proyectos, talleres y conferencias sobre tecnología,
              innovación y desarrollo de software. Ven y descubre las últimas
              tendencias en cómputo y networking con especialistas del sector.
            </p>
          </div>
        </div>
        <img class="img-fluid" src="/expoescom/assets/images/compu.png" alt="..." />
        <a class="btn btn-primary" href="/expoescom/register">Registro</a>
      </div>
    </section>
    <!-- Projects-->
    <section class="projects-section" id="projects">
      <div class="container px-4 px-lg-1">
        <!-- Featured Project Row-->
        <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
          <div class="col-xl-8 col-lg-7">
            <img
              class="img-fluid mb-3 mb-lg-0"
              src="/expoescom/assets/images/web.png"
              alt="..."
            />
          </div>
          <div class="col-xl-4 col-lg-5">
            <div class="featured-text text-center text-lg-left">
              <h4 class="text-white">Innovación sin límites</h4>
              <p class="text-white-50 mb-0">
                En ExpoEscom se presentan ideas que transforman el conocimiento en soluciones reales. Cada proyecto es una muestra del ingenio y la dedicación de nuestros estudiantes.
              </p>
            </div>
          </div>
        </div>
        <!-- Project One Row-->
        <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
          <div class="col-lg-6">
            <img
              class="img-fluid"
              src="/expoescom/assets/images/electronica.png"
              alt="..."
            />
          </div>
          <div class="col-lg-6">
            <div class="bg-black text-center h-100 project">
              <div class="d-flex h-100">
                <div
                  class="project-text w-100 my-auto text-center text-lg-left"
                >
                  <h4 class="text-white">Diversidad de disciplinas</h4>
                  <p class="mb-0 text-white-50">
                    Desde aplicaciones web hasta sistemas embebidos, aquí encontrarás una variedad de trabajos que abarcan diferentes áreas de la ingeniería y la tecnología.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Project Two Row-->
        <div class="row gx-0 justify-content-center">
          <div class="col-lg-6">
            <img
              class="img-fluid"
              src="/expoescom/assets/images/dsd.png"
              alt="..."
            />
          </div>
          <div class="col-lg-6 order-lg-first">
            <div class="bg-black text-center h-100 project">
              <div class="d-flex h-80">
                <div
                  class="project-text w-100 my-auto text-center text-lg-right"
                >
                  <h4 class="text-white">Talento en acción</h4>
                  <p class="mb-0 text-white-50">
                    Cada exposición representa una oportunidad para compartir, aprender e inspirar. ExpoEscom es el espacio donde las ideas toman forma y los proyectos cobran vida.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contacto -->
    <section class="contact-section bg-black">
      <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5">
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="card py-4 h-100">
              <div class="card-body text-center">
                <i class="fas fa-map-marked-alt text-primary mb-2"></i>
                <h4 class="text-uppercase m-0">Ubicación</h4>
                <hr class="my-4 mx-auto" />
                <div class="small text-black-50">
                  ESCOM IPN, Unidad Profesional Adolfo López Mateos, Av. Juan de Dios Bátiz, Nueva Industrial Vallejo, Gustavo A. Madero, 07320 Ciudad de México, CDMX
                </div>
                <a href="https://g.co/kgs/3ptZagP" target="_blank">Ver en mapa</a>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="card py-4 h-100">
              <div class="card-body text-center">
                <i class="fas fa-envelope text-primary mb-2"></i>
                <h4 class="text-uppercase m-0">Email</h4>
                <hr class="my-4 mx-auto" />
                <div class="small text-black-50">
                  <a href="mailto:expoescom@escom.ipn.mx">expoescom@escom.ipn.mx</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="card py-4 h-100">
              <div class="card-body text-center">
                <i class="fas fa-mobile-alt text-primary mb-2"></i>
                <h4 class="text-uppercase m-0">Redes Sociales</h4>
                <hr class="my-4 mx-auto" />
                <a href="https://www.instagram.com/escom_ipn_mx/" target="_blank">Instagram</a>
                <a href="https://www.facebook.com/escomipnmx" target="_blank">Facebook</a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
    <!-- Footer -->
    <footer class="footer bg-black small text-center text-white-50">
      <div class="container px-4 px-lg-5">
        <div class="footer-brand">
          <img src="/expoescom/assets/images/favicon.ico" alt="Logo ExpoEscom" />
        </div>
        <p class="text-black-50">&copy; 2025 ExpoEscom. Equipo 1</p>
        <nav class="footer-nav" aria-label="Pie de página">
          <a href="inicio.html">Inicio</a>
          <a href="admin.html">Administrador</a>
          <a href="ganadores.html">Ganadores</a>
        </nav>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/scripts.js"></script>
  </body>
</html>
