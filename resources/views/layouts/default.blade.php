<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Karma Market</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Main CSS File -->
 <link rel="stylesheet" href="{{ asset('main.css') }}">

 <!-- Location CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!--Location JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- =======================================================
  * Template Name: Amoeba
  * Template URL: https://bootstrapmade.com/free-one-page-bootstrap-template-amoeba/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page d-flex flex-column min-vh-100">

  <header id="header" class="header d-flex align-items-center sticky-top shadow">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="{{ route("home") }}" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">Karma Market</h1>
      </a>

      <nav id="navmenu" class="navmenu ">
        <ul>
          <li><a href="{{ route("home") }}" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="{{ route("login") }}">Login</a></li>
          <li><a href="{{ route("register") }}">Register</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">
@yield("contents")
</main>
</body>
  <footer id="footer" class="footer dark-background py-4 mt-auto">
  <div class="container">
    <div class="row justify-content-center text-center">

      <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center mb-4">
        <i class="bi bi-telephone icon mb-2"></i>
        <h4>Contact</h4>
        <p>
          <strong>Email:</strong> <span>KarmaMarket@example.com</span><br>
        </p>
      </div>

      <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center">
        <h4>Follow Us</h4>
        <div class="social-links d-flex justify-content-center gap-3 mt-2">
          <a href="#" class="twitter"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" class="facebook"><i class="fa-brands fa-facebook"></i></a>
          <a href="#" class="instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="linkedin"><i class="fa-brands fa-linkedin"></i></a>
        </div>
      </div>

    </div>
  </div>
</footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="fa-solid fa-up-long"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="/bootstrap/js/bootstrap.js"></script>

  <!-- Main JS File -->
<script src="{{ asset('main.js') }}"></script>



</html>

  <!--  <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Amoeba</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        All the links in the footer should remain intact.
         You can delete the links only if you've purchased the pro version. 
       Licensing information: https://bootstrapmade.com/license/ 
    Purchase the pro version with working PHP/AJAX contact form: [buy-url]
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
-->