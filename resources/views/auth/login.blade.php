
@extends("layouts.default")

@section("contents")

     <!-- signin Section -->
    <section id="signin" class="contact section d-flex justify-content-center ">
        <div class="container card section-title p-4 shadow" data-aos="fade-up">

            @if (session()->has("success"))
            <div class="alert alert-success">
            {{ session()->get("success") }}
            </div>    
            @endif
             @if (session()->has("error"))
            <div class="alert alert-danger">
            {{ session()->get("error") }}
            </div>    
            @endif
            
            <h3 class="mb-4">Sign In</h3>
            <form action="{{ route("login.post") }}" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                @csrf
                <div class="row justify-content-center">

                    <div class="col-md-6 mb-4">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-4">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit mb-4">Log In</button>

                <div class="row gy-4">
                    <p>Don't have an account? <a href="{{ route('register')}}"> Click here</a></p>
                </div>

            </form>
        </div>

    </section>

  @endsection
