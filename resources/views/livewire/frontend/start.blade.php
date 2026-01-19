

    <section class="page_wrapper get_started container-fluid" id="page_wrapper">
        <div class="container py-2 py-sm-5">
            <header>
                <img src="/frontend/assets/images/logo_green.png" alt srcset>
            </header>

            <div class="row justify-content-between">
                <div class="col-md-5">
                    <img src="/frontend/assets/images/start_image.png" class="start_image w-100" alt srcset>
                </div>
                <div class="col-md-6 d-flex justify-content-center flex-column gap-4">
                    <h1>Let's find your next <br /> favourite book </h1>

                    <div class="buttons d-flex flex-column align-items-start gap-4">

                        <p>
                            <a href={{ route('startnow') }} class="btn btn-warning fw-bold" id="btn_getstart">Get Started</a>
                        </p>
                        <p class="primary-color fw-bold">
                            Already have an account?
                            <a href={{ route("login") }}>
                                <span class="text-dark">Sign In</span></a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </section>

