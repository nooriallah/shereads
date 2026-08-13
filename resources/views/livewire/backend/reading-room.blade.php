<div class="container-fluid">

    {{-- Toolbar (wire:ignore: page number/progress are managed by the viewer JS) --}}
    <div class="card mb-3" wire:ignore>
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <div>
                    <h4 class="mb-0 fs-18 font-w600">{{ $book->title }}</h4>
                    <small class="text-muted">{{ $book->authors->pluck('full_name')->join(', ') }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm text-white" style="background:#05653D;" id="rr-prev"
                    title="Previous page">
                    <i class="fa fa-chevron-left"></i>
                </button>

                <span class="d-flex align-items-center gap-1">
                    <input type="number" id="rr-page-input" class="form-control form-control-sm text-center"
                        style="width: 70px;" min="1" value="{{ $page }}">
                    <span class="text-muted text-nowrap">/ <span id="rr-total">…</span></span>
                </span>

                <button type="button" class="btn btn-sm text-white" style="background:#05653D;" id="rr-next"
                    title="Next page">
                    <i class="fa fa-chevron-right"></i>
                </button>

                <span class="vr mx-1 d-none d-md-inline-block"></span>

                <button type="button" class="btn btn-sm btn-outline-secondary d-none d-md-inline-block" id="rr-zoom-out"
                    title="Zoom out">
                    <i class="fa fa-search-minus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary d-none d-md-inline-block" id="rr-zoom-in"
                    title="Zoom in">
                    <i class="fa fa-search-plus"></i>
                </button>
            </div>

            <div class="d-none d-lg-block" style="min-width: 160px;">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" id="rr-progress" role="progressbar"
                        style="width: 0%; background:#E7B944;"></div>
                </div>
                <small class="text-muted" id="rr-progress-label">&nbsp;</small>
            </div>
        </div>
    </div>

    {{-- Viewer (wire:ignore: PDF.js owns this DOM — Livewire must never morph it) --}}
    <div class="card" wire:ignore>
        <div class="card-body text-center p-2 p-md-4" style="background: #f4f4f4; min-height: 70vh;">
            <div id="rr-loading" class="py-5">
                <div class="spinner-border" style="color:#05653D;" role="status">
                    <span class="visually-hidden">Loading…</span>
                </div>
                <p class="text-muted mt-3">Opening your book…</p>
            </div>
            <canvas id="rr-canvas" class="shadow-sm rounded mx-auto"
                style="max-width: 100%; height: auto; display: none;"></canvas>
        </div>
    </div>

    @assets
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @endassets

    @script
        <script>
            (() => {
                const url = @js(route('book.content', $book->id));
                let pageNum = @js($page);
                let pdfDoc = null;
                let scale = 1.4;
                let rendering = false;
                let pendingPage = null;

                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                const canvas = document.getElementById('rr-canvas');
                const ctx = canvas.getContext('2d');
                const pageInput = document.getElementById('rr-page-input');
                const totalEl = document.getElementById('rr-total');
                const progressBar = document.getElementById('rr-progress');
                const progressLabel = document.getElementById('rr-progress-label');
                const loading = document.getElementById('rr-loading');

                function renderPage(num) {
                    rendering = true;
                    pdfDoc.getPage(num).then((page) => {
                        const viewport = page.getViewport({ scale });
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        page.render({ canvasContext: ctx, viewport }).promise.then(() => {
                            rendering = false;
                            if (pendingPage !== null) {
                                const next = pendingPage;
                                pendingPage = null;
                                renderPage(next);
                            }
                        });
                    });

                    pageInput.value = num;
                    const pct = Math.min(100, Math.round(num / pdfDoc.numPages * 100));
                    progressBar.style.width = pct + '%';
                    progressLabel.textContent = pct + '% read';
                }

                function queueRender(num) {
                    if (rendering) {
                        pendingPage = num;
                    } else {
                        renderPage(num);
                    }
                }

                function goTo(num) {
                    if (!pdfDoc) return;
                    num = Math.min(Math.max(1, num), pdfDoc.numPages);
                    if (num === pageNum) return;
                    pageNum = num;
                    queueRender(pageNum);
                    // Persist position (one lightweight request per page turn).
                    $wire.savePage(pageNum, pdfDoc.numPages);
                }

                pdfjsLib.getDocument({ url, withCredentials: true }).promise.then((doc) => {
                    pdfDoc = doc;
                    totalEl.textContent = doc.numPages;
                    pageNum = Math.min(pageNum, doc.numPages);
                    loading.style.display = 'none';
                    canvas.style.display = 'inline-block';
                    renderPage(pageNum);
                }).catch((err) => {
                    loading.innerHTML = '<p class="text-danger">Could not open this book. Please try again or contact support.</p>';
                    console.error('[ReadingRoom]', err);
                });

                document.getElementById('rr-prev').addEventListener('click', () => goTo(pageNum - 1));
                document.getElementById('rr-next').addEventListener('click', () => goTo(pageNum + 1));
                document.getElementById('rr-zoom-in').addEventListener('click', () => {
                    scale = Math.min(3, scale + 0.2);
                    if (pdfDoc) queueRender(pageNum);
                });
                document.getElementById('rr-zoom-out').addEventListener('click', () => {
                    scale = Math.max(0.6, scale - 0.2);
                    if (pdfDoc) queueRender(pageNum);
                });
                pageInput.addEventListener('change', () => goTo(parseInt(pageInput.value || '1', 10)));

                const keyHandler = (e) => {
                    if (e.target.tagName === 'INPUT') return;
                    if (e.key === 'ArrowLeft') goTo(pageNum - 1);
                    if (e.key === 'ArrowRight') goTo(pageNum + 1);
                };
                document.addEventListener('keydown', keyHandler);

                // Clean up the key listener when leaving via wire:navigate.
                document.addEventListener('livewire:navigating', () => {
                    document.removeEventListener('keydown', keyHandler);
                }, { once: true });
            })();
        </script>
    @endscript
</div>
