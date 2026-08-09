<section class="container-fluid p-0 question1_wrapper" id="page_wrapper">

    <div class="row container p-md-0">

        <div class="col-md-6 me-md-5">
            <img src="{{ asset('frontend/assets/images/f_q_1.png') }}" class="w-100 question_image" alt="All done illustration">
        </div>

        <div class="col-md-5 d-flex ms-md-5 gap-4 flex-column">

            <div class="arrow mt-3 mt-md-5 mb-md-5">
                <img src="{{ asset('frontend/assets/images/logo_green.png') }}" alt="She Reads">
            </div>

            <div class="numbers d-flex gap-0 align-items-center mb-md-5">
                @for ($i = 0; $i < 6; $i++)
                    <span class="fill">{{ $i + 1 }}</span>
                    @if ($i < 5)
                        <hr />
                    @endif
                @endfor
            </div>

            <div class="question">
                <h4 class="fw-bold">You&rsquo;re all set!</h4>
                <p class="mt-3">We&rsquo;ve received your answers and are ready to find the best books for you.</p>
            </div>

            <p>
                <a href="{{ route('analyze') }}" class="btn btn-success primary-bg fw-bold" id="btn_findbooks">Find My Books</a>
            </p>

        </div>

    </div>

</section>
