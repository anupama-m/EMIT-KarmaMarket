@extends("layouts.default")

@section("contents")
    <!-- Hero Section -->
    <section id="hero" class="hero section ">

      <img src="{{ asset('img/1.jpg') }}" alt="" data-aos="fade-in">

      <div class="container text-center" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h2>Welcome to Karma Market</h2>
            <p>Earn Goods By Doing Good</p>
            <a href="{{ route("login") }}" class="btn-get-started">Get Started</a>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>About Us</h2>
        <p>Karma Market is more than just a platform — it's a movement. 
This platform is designed to encourage social good, reward helpfulness, and reduce waste by enabling people to connect and contribute meaningfully to their community.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-6 order-1 order-lg-2">
            <img src="{{ asset('img/about.jpg') }}" class="img-fluid" alt="">
          </div>
          <div class="col-lg-6 order-2 order-lg-1 content">
            <h3>Our Mission</h3>
            <p>
             To create a digital space where kindness is valued, helping others is celebrated, and small acts make a big impact.
            </p>
            <h4>What You Can Do Here</h4>
            <ul>
              <li><i class="fa-solid fa-check"></i> <span>Post for help when you're in need — someone nearby may have just what you're looking for.</span></li>
              <li><i class="fa-solid fa-check"></i><span>Offer help or donate items you no longer need — and let them find a second life with someone else.</span></li>
              <li><i class="fa-solid fa-check"></i> <span>Earn Karma Points for every good action — your kindness is recognized and rewarded.</span></li>
            </ul>
          </div>
        </div>
      </div>
    </section><!-- /About Section -->
@endsection
