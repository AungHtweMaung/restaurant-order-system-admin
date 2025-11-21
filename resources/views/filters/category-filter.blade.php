<div class="col-md-8 col-lg-6">
    <form action="{{ url()->full() }}" method="GET">
        <div class="form-group gap-3 d-flex">

            <input type="text" value="{{ request('searchName') }}" class="form-control form-control-sm" id="searchName"
                name="searchName" placeholder="Search...">
            <a href="{{ route('categories.index') }}" class="btn btn-danger text-white">Reset</a>
            <button type="submit" class="btn btn-primary">Search</button>

        </div>
    </form>
</div>
