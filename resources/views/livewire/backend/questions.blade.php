<div class="container-fluid position-relative">

    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
    <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-4 p-3 rounded">
        <div>
            <h2 class="mb-1">Questionnaire</h2>
            <p class="text-muted mb-0">
                These questions greet every new visitor. Each answer option can signal
                interests (with a weight) — that's what powers the recommendations.
            </p>
        </div>
        <button class="btn btn-outline-primary d-inline-block px-4 align-self-start" wire:click="newQuestion">
            Add new question
        </button>
    </div>

    {{-- ============ QUESTION FORM ============ --}}
    @if ($formMode === 'question')
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{ $questionId ? 'Edit Question' : 'New Question' }}</h4>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="saveQuestion">
                <div class="mb-3">
                    <label for="question_text" class="form-label font-w500">Question text</label>
                    <input type="text" id="question_text" class="form-control form-control-lg" placeholder="e.g. What do you enjoy reading the most?" wire:model="question_text">
                    @error('question_text') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="question_active" wire:model="question_active">
                    <label class="form-check-label" for="question_active">Active (shown to visitors)</label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">{{ $questionId ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ============ OPTION FORM ============ --}}
    @if ($formMode === 'option')
    <div class="card mb-4">
        <div class="card-header d-block">
            <h4 class="card-title mb-1">{{ $optionId ? 'Edit Answer Option' : 'New Answer Option' }}</h4>
            @if ($parentQuestion)
            <small class="text-muted">For question: <strong>{{ $parentQuestion->question_text }}</strong></small>
            @endif
        </div>
        <div class="card-body">
            <form wire:submit.prevent="saveOption">
                <div class="mb-3">
                    <label for="option_text" class="form-label font-w500">Answer text</label>
                    <input type="text" id="option_text" class="form-control form-control-lg" placeholder="e.g. Novels and stories" wire:model="option_text">
                    @error('option_text') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="option_active" wire:model="option_active">
                    <label class="form-check-label" for="option_active">Active (shown to visitors)</label>
                </div>

                <div class="mb-3">
                    <label class="form-label font-w500 d-block">
                        Recommendation signals
                        <small class="text-muted">— which interests does this answer suggest, and how strongly?</small>
                    </label>
                    <div class="row g-2">
                        @foreach ($allInterests as $interest)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2">
                                <span>{{ $interest->name }}</span>
                                <select class="form-select form-select-sm" style="max-width: 120px;" wire:model="signals.{{ $interest->id }}">
                                    <option value="0">None</option>
                                    <option value="1">Weak (1)</option>
                                    <option value="2">Medium (2)</option>
                                    <option value="3">Strong (3)</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if ($allInterests->isEmpty())
                    <p class="text-muted mb-0">No interests yet — add them on the Interests page first.</p>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">{{ $optionId ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ============ QUESTIONS LIST ============ --}}
    @forelse ($questions as $question)
    <div class="card mb-3" wire:key="q-{{ $question->id }}">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex flex-column">
                    <button class="btn btn-xs p-0 border-0" title="Move up" wire:click="moveQuestion({{ $question->id }}, 'up')" @disabled($loop->first)>
                        <i class="fa fa-chevron-up {{ $loop->first ? 'text-muted' : '' }}"></i>
                    </button>
                    <button class="btn btn-xs p-0 border-0" title="Move down" wire:click="moveQuestion({{ $question->id }}, 'down')" @disabled($loop->last)>
                        <i class="fa fa-chevron-down {{ $loop->last ? 'text-muted' : '' }}"></i>
                    </button>
                </span>
                <h4 class="card-title mb-0">
                    {{ $loop->iteration }}. {{ $question->question_text }}
                </h4>
                <span @class(['badge', 'badge-success'=> $question->is_active, 'badge-secondary' => ! $question->is_active])>
                    {{ $question->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="d-flex gap-1">
                <button class="btn btn-outline-primary btn-xs d-flex gap-1 align-items-center" wire:click="newOption({{ $question->id }})">
                    <i class="fa fa-plus"></i> Add answer
                </button>
                <button class="btn btn-outline-success btn-xs" title="{{ $question->is_active ? 'Deactivate' : 'Activate' }}" wire:click="toggleQuestion({{ $question->id }})">
                    <i class="fa {{ $question->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                </button>
                <button class="btn btn-outline-primary btn-xs" title="Edit" wire:click="editQuestion({{ $question->id }})">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-xs" title="Delete" wire:click="deleteQuestion({{ $question->id }})" wire:confirm="Delete this question? Its answer options AND all collected visitor answers for it will be removed.">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="card-body py-3">
            @if ($question->options->isEmpty())
            <p class="text-muted mb-0">
                No answer options yet — a question without options is skipped in the questionnaire.
            </p>
            @else
            <div class="table-responsive">
                <table class="table verticle-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Order</th>
                            <th>Answer</th>
                            <th>Signals (interest × weight)</th>
                            <th style="width: 90px;">Status</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($question->options as $option)
                        <tr wire:key="o-{{ $option->id }}">
                            <td>
                                <button class="btn btn-xs p-0 border-0 me-1" title="Move up" wire:click="moveOption({{ $option->id }}, 'up')" @disabled($loop->first)>
                                    <i class="fa fa-chevron-up {{ $loop->first ? 'text-muted' : '' }}"></i>
                                </button>
                                <button class="btn btn-xs p-0 border-0" title="Move down" wire:click="moveOption({{ $option->id }}, 'down')" @disabled($loop->last)>
                                    <i class="fa fa-chevron-down {{ $loop->last ? 'text-muted' : '' }}"></i>
                                </button>
                            </td>
                            <td class="font-w500">{{ $option->option_text }}</td>
                            <td>
                                @forelse ($option->interests as $interest)
                                <span class="badge light badge-primary me-1">
                                    {{ $interest->name }} ×{{ $interest->pivot->weight }}
                                </span>
                                @empty
                                <span class="badge badge-warning" title="This answer doesn't influence recommendations yet">
                                    No signals
                                </span>
                                @endforelse
                            </td>
                            <td>
                                <span @class(['badge', 'badge-success'=> $option->is_active, 'badge-secondary' => ! $option->is_active])>
                                    {{ $option->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-outline-success btn-xs" title="{{ $option->is_active ? 'Deactivate' : 'Activate' }}" wire:click="toggleOption({{ $option->id }})">
                                    <i class="fa {{ $option->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                                <button class="btn btn-outline-primary btn-xs my-1" title="Edit" wire:click="editOption({{ $option->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-xs" title="Delete" wire:click="deleteOption({{ $option->id }})" wire:confirm="Delete this answer option? Collected visitor answers using it will be removed.">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-0">No questions yet — add your first question to start the questionnaire.</p>
        </div>
    </div>
    @endforelse

</div>
