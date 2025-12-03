@extends('layouts.app', [
    'elementActive' => 'menu-modifiers',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Create Menu Modifier</h2>
        <div class="row justify-content-center">
            <div class="col-10">
                <form method="POST" id="" class="form-submit">
                    @csrf

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
                    <div class="row mb-3 justify-content-end">
                        <label class="form-label">Modifiers</label>
                        @foreach ($modifiers as $modifier)
                            <div class="col-4 my-3">
                                <div class="gap-5">
                                    <input style="font-size: 20px;" class="form-check-input me-3" type="checkbox" name="modifier_ids[]"
                                        value="{{ $modifier->id }}" id="modifier_{{ $modifier->id }}">
                                    <label class="form-check-label fs-5" for="modifier_{{ $modifier->id }}">
                                        {{ $modifier->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>


                </form>
            </div>
        </div>
    </div>
@endsection
