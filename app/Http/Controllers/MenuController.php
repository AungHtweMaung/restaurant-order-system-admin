<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\MenuStoreRequest;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('deleted_at')->get();
        $menus = Menu::latest()->paginate(10);
        return view('menus.index', compact('menus', 'categories'));
    }

    public function store(MenuStoreRequest $request)
    {
        logger($request->all());
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('menu_images', 'public');
            $data['image_path'] = $imagePath;
        }

        Menu::create($data);

        session()->flash('success', 'Menu created successfully.');

        return response()->json(['success' => 'Menu created successfully.', 'redirectUrl' => route('menus.index')]);
    }
}
