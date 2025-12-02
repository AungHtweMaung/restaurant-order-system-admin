@extends('layouts.app', [
    'elementActive' => 'categories',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Category</h2>

        <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success text-white border" data-bs-toggle="modal"
                    data-bs-target="#createCategoryModal">
                    Create
                </button>
            </div>

            @include('filters.category-filter')
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>English Name</th>
                        <th>Myanmar Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = ($categories->currentPage() - 1) * $categories->perPage() + 1;
                    @endphp
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ $category->eng_name }}</td>
                            <td>{{ $category->mm_name }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal" onclick="editCategory({{ $category->id }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @php $index++; @endphp
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $categories->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-purple">
                <div class="modal-header">
                    <h5 class="modal-title">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST" id="createCategoryForm" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="eng_name" class="form-label">English Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eng_name" name="eng_name">
                            <div class="invalid-feedback" data-error-for="eng_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="mm_name" class="form-label">Myanmar Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mm_name" name="mm_name">
                            <div class="invalid-feedback" data-error-for="mm_name"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-purple">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" action="#" method="POST" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_eng_name" class="form-label">English Name</label>
                            <input type="text" class="form-control" id="edit_eng_name" name="eng_name">
                            <div class="invalid-feedback" data-error-for="eng_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_mm_name" class="form-label">Myanmar Name</label>
                            <input type="text" class="form-control" id="edit_mm_name" name="mm_name">
                            <div class="invalid-feedback" data-error-for="mm_name"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function editCategory(id) {
            $.ajax({
                url: '{{ route('categories.show', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    const form = document.getElementById('editCategoryForm');
                    form.action = '{{ route('categories.update', ':id') }}'.replace(':id', id);
                    document.getElementById('edit_eng_name').value = data.eng_name;
                    document.getElementById('edit_mm_name').value = data.mm_name;
                },
                error: function() {
                    toastr.error('Failed to fetch category data.', 'Error');
                }
            });
        }

        $(document).ready(function() {
            // Clear create modal form when closed
            $('#createCategoryModal').on('hidden.bs.modal', function() {
                const form = $('#createCategoryForm')[0];
                form.reset();
                $(this).find('.invalid-feedback').text('');
                $(this).find('.form-control').removeClass('is-invalid');
            });

            // Clear edit modal form when closed
            $('#editCategoryModal').on('hidden.bs.modal', function() {
                const form = $('#editCategoryForm')[0];
                form.reset();
                $(this).find('.invalid-feedback').text('');
                $(this).find('.form-control').removeClass('is-invalid');
            });

            // Remove any stuck backdrops if modal fails to close properly
            $(document).on('click', '.modal-backdrop', function() {
                $('.modal-backdrop').remove();
                $('modal-backdrop.show').remove();
                $('body').removeClass('modal-open');
            });


        });
    </script>
@endpush
