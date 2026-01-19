<div class="container-fluid">
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <!-- row -->
    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add new author</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                       
                        @include("livewire.backend.author.author-form")

                    </div>
                </div>
            </div>
        </div>

    </div>




</div>
