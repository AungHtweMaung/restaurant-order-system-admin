<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Modifier;
use Illuminate\Http\Request;

class MenuModifierController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->filter()->paginate(10);
        return view('menu-modifiers.index', compact('menus'));
    }

    public function create() {
        $menus = Menu::whereNull('deleted_at')->get();
        $modifiers = Modifier::whereNull('deleted_at')->get();
        return view('menu-modifiers.create', compact('menus', 'modifiers'));
    }
}
