<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>EMPREENDEDORES - Advec Santo André</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/img/favicon.png" rel="icon">
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/bootstrap/css/bootstrap.min.css"
    rel="stylesheet">
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css"
    rel="stylesheet">
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/glightbox/css/glightbox.min.css"
    rel="stylesheet">
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/swiper/swiper-bundle.min.css"
    rel="stylesheet">

  <!-- Main CSS File -->
  <link href="<?= base_url('templates/Empreendedor-02'); ?>/assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: eNno
  * Template URL: https://bootstrapmade.com/enno-free-simple-bootstrap-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="<?= base_url('templates/Empreendedor-02'); ?>/assets/img/logo.png" alt=""> -->
        <h1 class="sitename">ADVEC Santo André</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>


        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="<?= base_url('/registration') ?>">INSCRIÇÃO</a>

    </div>
  </header>

  <main class="main">



    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>ADVEC Santo André</span>
        <h2>INSCREVA-SE</h2>
        <p>O Encontro de Empreendedores será realizado no dia 15/03/2025 às 09h00</p>
        <p>ENTRADA FRANCA</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <?php if (isset($validation)): ?>
            <div class="col-12">


              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">ATENÇÃO</h4>
                <hr>

                <p class="mb-0">Preencha as informações antes de prosseguir</p>
                <p>
                  <?= $validation->listErrors() ?>
                </p>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>


            </div>
          <?php endif; ?>

          <div class="col-lg-7">
            <form action="<?= base_url('/do-registration') ?>" method="post" class="php-email-form"
              data-aos="fade-up" data-aos-delay="200">
              <div class="row gy-4">

                <div class="col-md-12">
                  <label for="name-field" class="pb-2">Nome</label>
                  <input type="text" name="t_nome" id="t_nome" class="form-control" required=""
                    value="<?= (isset($fields) ? $fields['t_nome'] : "") ?>">
                </div>

                <div class="col-md-6">
                  <label for="email-field" class="pb-2">Email</label>
                  <input type="email" class="form-control" name="t_email" id="t_email"
                    value="<?= (isset($fields) ? $fields['t_email'] : "") ?>">
                </div>

                <div class="col-md-6">
                  <label for="subject-field" class="pb-2">Telefone (WhatsApp)</label>
                  <input type="tel" class="form-control" name="t_telefone" id="t_telefone" placeholder="XX XXXX-XXXX" required=""
                    value="<?= (isset($fields) ? $fields['t_telefone'] : "") ?>">
                </div>
                <div class="col-md-6">
                  <label for="subject-field" class="pb-2">Trará algum acompanhante ?</label>
                  <select class="form-select" id="cb_acompanhante"
                    name="cb_acompanhante">
                    <option value="S" <?= (isset($fields) ? ($fields['cb_acompanhante'] == 'S' ? "selected" : "") : ""); ?>>
                      SIM</option>
                    <option value="N" <?= (isset($fields) ? ($fields['cb_acompanhante'] == 'N' ? "selected" : "") : ""); ?>>
                      NÃO</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="subject-field" class="pb-2">Quantos</label>
                  <input type="number" class="form-control" name="t_qtde_acompanhante" id="t_qtde_acompanhante"
                    value="<?= (isset($fields) ? $fields['t_qtde_acompanhante'] : "") ?>">
                </div>

                <div class="col-md-4">
                  <label for="subject-field" class="pb-2">@ Instagram</label>
                  <input type="text" class="form-control" name="t_instagram" id="t_instagram"
                    value="<?= (isset($fields) ? $fields['t_instagram'] : "") ?>">
                </div>

                <div class="col-md-8">
                  <label for="subject-field" class="pb-2">Ramo de atividade</label>
                  <input type="text" class="form-control" name="t_ramo_atividade" id="t_ramo_atividade"
                    value="<?= (isset($fields) ? $fields['t_ramo_atividade'] : "") ?>">
                </div>
              
                <div class="col-md-12">
                  <label for="message-field" class="pb-2">Deixe uma mensagem caso desejar</label>
                  <textarea class="form-control" name="t_mensagem" rows="5"
                    id="t_mensagem"><?= (isset($fields) ? $fields['t_mensagem'] : "") ?></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Enviando</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Sua inscrição foi enviada!</div>

                  <button type="submit">ENVIAR INSCRIÇÃO</button>
                </div>
              </div>
            </form>
          </div><!-- End Contact Form -->

          <div class="col-lg-5">
            <div class="info-wrap">
              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h3>Local</h3>
                  <p>Av. Industrial, 1607 - Jardim</p>
                  <p>Santo André - SP</p>
                  <p>09080-510</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-telephone flex-shrink-0"></i>
                <div>
                  <h3>Telefone</h3>
                  <p>55 11 4436-3064</p>
                </div>
              </div><!-- End Info Item -->


              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3654.932031354101!2d-46.54006648233957!3d-23.64260504682115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce42c7253de66f%3A0xb599e2b730488b2a!2sAv.%20Industrial%2C%201607%20-%20Jardim%2C%20Santo%20Andr%C3%A9%20-%20SP%2C%2009080-510!5e0!3m2!1sen!2sbr!4v1739998273361!5m2!1sen!2sbr"
                frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="d-flex align-items-center">
            <span class="sitename">ADVEC Santo André</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Av. Industrial, 1607 - Jardim</p>
            <p>Santo André - SP</p>
            <p>09080-510</p>
            <p class="mt-3"><strong>Info:</strong> <span>11 4436-3064</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">

        </div>

        <div class="col-lg-2 col-md-3 footer-links">

        </div>

        <div class="col-lg-4 col-md-12">
          <h4>Siga-nos</h4>
          <p>Acompanhe nossos trabalhos pelas redes sociais</p>
          <div class="social-links d-flex">
            <a href="https://www.instagram.com/advecsantoandre"><i class="bi bi-instagram"></i></a>
            <a href="https://www.youtube.com/@advecsantoandre"><i class="bi bi-youtube"></i></a>
            <a href="https://www.facebook.com/advecsantoandre"><i class="bi bi-facebook"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">ADVEC.org</strong> <span>Todos os Direitos
          Reservados</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Implementado by <a href="https://elpidio.pro.br/">EABC Tecnologia</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script
    src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php if(1==2){ ?>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/php-email-form/validate.js"></script>
  <?php } ?>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/aos/aos.js"></script>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script
    src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
   
  <script src="<?= base_url('templates/Empreendedor-02'); ?>/assets/js/main.js"></script>
<script>

$(document).ready(function () {
            $('.t_telefone').mask('(99) 99999-9999');
        });
</script>
</body>

</html>