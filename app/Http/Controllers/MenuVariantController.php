<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Http\Requests\MenuVariantStoreRequest;
use App\Http\Requests\MenuVariantUpdateRequest;
use Illuminate\Http\Request;

class MenuVariantController extends Controller
{
    public function index()
    {
        $menus = Menu::whereNull('deleted_at')->get();
        $menuVariants = MenuVariant::with('menu:id,eng_name,mm_name')
            ->whereNull('deleted_at')
            ->filter()
            ->paginate(10);

        return view('menu-variants.index', compact('menuVariants', 'menus'));
    }

    public function store(MenuVariantStoreRequest $request)
    {
        $data = $request->validated();

        MenuVariant::create($data);

        session()->flash('success', 'MenuVariant created successfully.');

        return response()->json(['redirectUrl' => route('menu-variants.index')]);
    }

    public function show(MenuVariant $menuVariant)
    {
        return response()->json($menuVariant);
    }

    public function update(MenuVariantUpdateRequest $request, MenuVariant $menuVariant)
    {
        $data = $request->validated();

        $menuVariantUpdate = [
            'menu_id' => $data['edit_menu_id'],
            'name' => $data['edit_name'],
            'price' => $data['edit_price'],
            'is_available' => $data['edit_is_available']
        ];

        // dd($menuVariantUpdate);

        $menuVariant->update($menuVariantUpdate);

        session()->flash('success', 'Menu Variant updated successfully.');

        return response()->json(['redirectUrl' => route('menu-variants.index')]);
    }


    public function destroy(MenuVariant $menuVariant)
    {
        $menuVariant->delete();
        session()->flash('success', 'Menu deleted successfully.');
        return redirect()->route('menu-variants.index');
    }
}
