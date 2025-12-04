<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Modifier;
use Illuminate\Http\Request;
use App\Http\Requests\MenuModifierStoreRequest;

class MenuModifierController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->filter()->paginate(10);
        return view('menu-modifiers.index', compact('menus'));
    }

    public function create(Menu $menu)
    {
        // show all modifiers to select while create menu modifier
        $modifiers = Modifier::whereNull('deleted_at')->get();
        $menu->load('modifiers');
        $selectedModifierIds = $menu->modifiers->pluck('id')->toArray();
        return view('menu-modifiers.create', compact('menu', 'modifiers', 'selectedModifierIds'));
    }

    public function store(MenuModifierStoreRequest $request, Menu $menu)
    {
        $modifierIds = $request->input('modifier_ids', []); // selected modifiers
        $modifierPrices = $request->input('modifier_price', []); // prices keyed by modifier ID

        // Prepare data for sync: ['modifier_id' => ['price' => 1000]]
        $syncData = [];
        foreach ($modifierIds as $modifierId) {
            $syncData[$modifierId] = [
                'price' => $modifierPrices[$modifierId] ?? null
            ];
        }

        // Sync modifiers with pivot table, including price
        $menu->modifiers()->sync($syncData);

        session()->flash('success', 'Menu Modifier updated successfully.');

        return response()->json(['redirectUrl' => route('menus.modifiers.create', $menu)]);
    }
}
