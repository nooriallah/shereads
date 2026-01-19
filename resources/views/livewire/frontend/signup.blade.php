
    <section class="container-fluid p-0 question1_wrapper" id="page_wrapper">

        <div class="row container p-md-0">

            <div class="col-md-6 me-md-5">
                <img src="/frontend/assets//images/f_q_1.png" class="w-100 question_image" alt>
            </div>

            <div class="col-md-5 d-flex ms-md-5  gap-4 flex-column">

                <div class="arrow mt-3 mt-md-5 mb-md-5 ">
                    <img src="/frontend/assets/images/logo_green.png" alt="">

                </div>


                <div class="signup_form">
                    <h1>Create Your Account</h1>
                    <p>Sign up to unlock your personalized reading experience.</p>

                    <form action="" method="" class="d-flex flex-column gap-3 mt-5">
                        <input type="text" name="full_name" placeholder="Full name">
                        <input type="email" name="email" placeholder="Email address">
                        <input type="password" name="password" placeholder="Password">
                        <input type="password" name="confrim_password" placeholder="Confirm Password">
                        <input type="submit" value="Sign Up">
                    </form>
                    <p class="text-center">
                        Already have an account? <a href={{ route("login") }} class="text-decoration-none">Sign In</a>
                    </p>


                </div>

            </div>

        </div>

    </section>
