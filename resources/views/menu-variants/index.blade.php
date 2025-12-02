@extends('layouts.app', [
    'elementActive' => 'menu-variants',
])

@section('content')
    <div>
        {{-- Toast messages --}}

        <h2 class="text-center my-3">Menu Variants</h2>

        <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success text-white border" data-bs-toggle="modal"
                    data-bs-target="#createMenuVariantModal">
                    Create
                </button>
            </div>

            @include('filters.menu-variants-filter')
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Is Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = ($menuVariants->currentPage() - 1) * $menuVariants->perPage() + 1;
                    @endphp
                    @forelse($menuVariants as $menuVariant)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ optional($menuVariant->menu)->eng_name }} /<br><br>{{ optional($menuVariant->menu)->mm_name }}</td>
                            <td>{{ $menuVariant->name }}</td>
                            <td>{{ $menuVariant->price ? $menuVariant->price : '-' }}</td>
                            <td>
                                @if ($menuVariant->is_available)
                                    <span class="badge rounded-pill text-bg-success text-white fw-bolder">Yes</span>
                                @else
                                    <span class="badge rounded-pill text-bg-danger text-white fw-bolder">No</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                    data-bs-target="#editMenuVariantModal" onclick="editMenuVariant({{ $menuVariant->id }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{route('menu-variants.destroy', $menuVariant->id)}}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this menu?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @php $index++; @endphp
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No menu variants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $menuVariants->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createMenuVariantModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="createMenuVariantModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content border-purple">
                <div class="modal-header">
                    <h5 class="modal-title">Create Menu Variant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('menu-variants.store') }}" method="POST"  id="createMenuVariantForm"
                    class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                            <select class="form-select" id="menu_id" name="menu_id" required>
                                <option value="" selected disabled>Select Menu</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->eng_name }} /
                                        {{ $menu->mm_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="menu_id"></div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" data-error-for="name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" min="0"
                                step="1">
                            <div class="invalid-feedback" data-error-for="price"></div>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                                checked>
                            <label class="form-check-label" for="is_available">Is Available</label>
                            <div class="invalid-feedback" data-error-for="is_available"></div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Menu Variant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuVariantModal" tabindex="-1" aria-labelledby="editMenuVariantModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content border-purple">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu Variant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editMenuVariantForm" action="#" method="POST" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_menu_id" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_menu_id" name="edit_menu_id" required>
                                <option value="" disabled>Select Menu</option>
                                @foreach ($menus as $menu)
                                    <option
                                        value="{{ $menu->id }}">{{ $menu->eng_name }} /
                                        {{ $menu->mm_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error-for="edit_menu_id"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                            <div class="invalid-feedback" data-error-for="edit_name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit_price" name="edit_price" min="0"
                                step="1">
                            <div class="invalid-feedback" data-error-for="edit_price"></div>
                        </div>

                        <div class="mb-3">
                            <input type="checkbox" class="form-check-input" id="edit_is_available"
                                name="edit_is_available" checked>
                            <label class="form-check-label" for="edit_is_available">Is Available</label>
                            <div class="invalid-feedback" data-error-for="edit_is_available"></div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update MenuVariant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function editMenuVariant(id) {
            $.ajax({
                url: '{{ route("menu-variants.show", ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    const form = document.getElementById('editMenuVariantForm');
                    form.action = '{{ route("menu-variants.update", ':id') }}'.replace(':id', id);
                    $('#edit_menu_id').val(data.menu_id).trigger('change');
                    $('#edit_name').val(data.name);
                    $('#edit_price').val(data.price);
                    $('#edit_is_available').prop('checked', data.is_available);

                },
                error: function() {
                    toastr.error('Failed to fetch menu variant data.', 'Error');
                }
            });
        }
        // Clear create menu modal form and validation on close
        $('#createMenuVariantModal').on('hidden.bs.modal', function() {
            const form = $('#createMenuVariantForm')[0];
            form.reset();
            $(this).find('.invalid-feedback').text('');
            $(this).find('.form-control').removeClass('is-invalid');
        });

        // Initialize select2 for category_id in createMenu modal with dropdownParent to fix modal conflict
        $('#createMenuVariantModal').on('shown.bs.modal', function() {
            $('#menu_id').select2({
                dropdownParent: $('#createMenuVariantModal')
            });
        });

        // Initialize select2 for category_id in editMenu modal with dropdownParent to fix modal conflict
        $('#editMenuVariantModal').on('shown.bs.modal', function() {
            $('#edit_menu_id').select2({
                dropdownParent: $('#editMenuVariantModal')
            });
        });
    </script>
@endpush
