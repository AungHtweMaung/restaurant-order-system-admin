@extends('layouts.app', [
    'elementActive' => 'menu-modifiers',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Menu Modifier</h2>

        {{-- <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">

            </div>

            @include('filters.menu-filter')
        </div> --}}

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = ($menus->currentPage() - 1) * $menus->perPage() + 1;
                    @endphp
                    @forelse($menus as $menu)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ $menu->eng_name }}<br><br>{{ $menu->mm_name }}</td>
                            <td>
                                <a href="{{route('menu-modifiers.create')}}" type="button" class="btn btn-sm btn-warning me-2">
                                    <i class="far fa-add"></i>
                                </a>
                                <a type="button" class="btn btn-sm btn-warning me-2">
                                    <i class="far fa-edit"></i>
                                </a>

                                <form action="{{route('menus.destroy', $menu->id)}}" method="POST" style="display:inline;">
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
                            <td colspan="4" class="text-center">No menus found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $menus->appends(request()->query())->links() }}
        </div>
    </div>
@endsection



