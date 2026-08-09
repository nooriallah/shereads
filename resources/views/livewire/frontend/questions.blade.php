<section class="container-fluid p-0 question1_wrapper" id="page_wrapper">

    @if ($question)
        <div class="row container p-md-0" wire:key="question-{{ $question->id }}">

            <div class="col-md-6 me-md-5">
                <img src="{{ asset($question->image ?: 'frontend/assets/images/f_q_1.png') }}" class="w-100 question_image" alt="Question illustration">
            </div>

            <div class="col-md-5 d-flex ms-md-5 gap-4 flex-column">

                <div class="arrow mt-3 mt-md-5 mb-md-5">
                    <a href="#" id="btn_arrow" wire:click.prevent="goBack">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.2131 12.5545C14.7034 12.0685 14.707 11.2771 14.221 10.7867C13.735
                                        10.2964 12.9436 10.2929 12.4532 10.7788L9.46202 13.7435C8.3354 14.8601
                                        7.41461 15.7726 6.7616 16.5858C6.08255 17.4313 5.591 18.2895 5.45969
                                        19.3197C5.40211 19.7714 5.40211 20.2285 5.45969 20.6803C5.591 21.7104
                                        6.08255 22.5687 6.7616 23.4142C7.41461 24.2273 8.3354 25.1399 9.46203
                                        26.2565L12.4532 29.2211C12.9436 29.7071 13.735 29.7036 14.221 29.2133C14.707
                                        28.7229 14.7034 27.9315 14.2131 27.4455L11.2748 24.5333C10.0823 23.3514 9.26433
                                        22.538 8.71083 21.8488C8.53095 21.6248 8.3906 21.4277 8.28164 21.25H33.3332C34.0235 21.25 34.5832 20.6903 34.5832 20C34.5832 19.3096 34.0235 18.75 33.3332 18.75H8.28164C8.3906 18.5723 8.53095 18.3751 8.71083 18.1512C9.26433 17.462 10.0823 16.6486 11.2748 15.4667L14.2131 12.5545Z"
                                fill="#2D264B" />
                        </svg>
                    </a>
                </div>

                <div class="numbers d-flex gap-0 align-items-center mb-md-5">
                    @for ($i = 0; $i < $total; $i++)
                        <span @class(['fill' => $i <= $current])>{{ $i + 1 }}</span>
                        @if ($i < $total - 1)
                            <hr />
                        @endif
                    @endfor
                </div>

                <div class="question">
                    <h4 class="fw-bold">{{ $question->question_text }}</h4>
                </div>

                <div class="answers">

                    <div class="form d-flex flex-column align-items-start gap-3" wire:loading.class="pe-none opacity-50">

                        @foreach ($question->options as $option)
                            <input type="radio" class="btn-check" name="answer" id="answer{{ $option->id }}"
                                autocomplete="off"
                                @checked(($answers[$question->id] ?? null) === $option->id)>
                            <label class="btn btn-outline-success" for="answer{{ $option->id }}"
                                wire:click="selectAnswer({{ $option->id }})">
                                {{ $option->option_text }}
                            </label>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    @else
        <div class="row container p-md-0">
            <div class="col-12 text-center py-5">
                <h4 class="fw-bold">The questionnaire is not available right now.</h4>
                <p>Please check back soon.</p>
                <a href="{{ route('start') }}" class="btn btn-success primary-bg fw-bold">Back to home</a>
            </div>
        </div>
    @endif

</section>
