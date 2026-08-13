<section class="container-fluid p-0" id="page_wrapper">
    <div class="container py-2 py-sm-5">
        <header>
            <img src="/frontend/assets/images/logo_green.png" alt="She Reads">
        </header>

        <div class="result_prev row mt-5">
            <div class="col-12">
                <h1 class="fw-bolder">Your personalized books list
                    <br>
                    <span class="fw-normal fs-5">
                        Based on your answers, we’ve handpicked a selection of books that suit your interests.
                        Ready to dive in and discover your next favorite read?
                    </span>
                </h1>
            </div>
        </div>

        {{-- Preview of the personalized list --}}
        <div class="row mt-4 g-4">
            @forelse ($previewBooks as $book)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="text-center">
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('backend/images/books/bookpic.png') }}"
                            alt="{{ $book->title }}" class="w-100 rounded shadow-sm"
                            style="aspect-ratio: 3/4; object-fit: cover;">
                        <h6 class="mt-2 fw-bold mb-0" style="color:#05653D;">{{ $book->title }}</h6>
                        <small class="text-muted">{{ $book->authors->pluck('full_name')->join(', ') }}</small>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">
                        Our library is growing — sign up now and your personalized list will be waiting
                        for you as soon as new books arrive.
                    </p>
                </div>
            @endforelse

            @if ($hiddenCount > 0)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 rounded border"
                        style="aspect-ratio: 3/4; background: rgba(5, 101, 61, .06); border-style: dashed !important;">
                        <span class="fs-2 fw-bold" style="color:#05653D;">+{{ $hiddenCount }}</span>
                        <small class="text-muted text-center px-2">more books picked for you</small>
                    </div>
                </div>
            @endif
        </div>

        <p class="mt-4">
            <a href="{{ route('register') }}" class="btn btn-success primary-bg fw-bold px-4 py-2" id="btn_sugnup"
                style="background:#05653D; border-color:#05653D;">
                Sign Up to Access Full List
            </a>
        </p>
    </div>
</section>
