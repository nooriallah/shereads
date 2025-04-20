<div class="container-fluid">
    <!-- row -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Categories</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form>
                            @csrf
                            <label for="cat_name">Add new category</label>
                            <div class="mb-3 d-flex align-items-center">
                                <input class="form-control form-control-lg" name="cat_name" id="cat_name" type="text"
                                       placeholder="Enter category name">
                                <input class="btn btn-md btn-success rounded-3" type="submit" value="Add"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

