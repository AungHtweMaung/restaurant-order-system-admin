<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Requests\MenuStoreRequest;
use App\Http\Requests\MenuUpdateRequest;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('deleted_at')->get();
        $menus = Menu::latest()->filter()->paginate(10);
        return view('menus.index', compact('menus', 'categories'));
    }

    public function store(MenuStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('menu_images', 'public');
            $data['image_path'] = $imagePath;
        }

        Menu::create($data);

        session()->flash('success', 'Menu created successfully.');

        return response()->json(['redirectUrl' => route('menus.index')]);
    }

    public function show(Menu $menu) {
        return response()->json($menu);
    }


    public function update(MenuUpdateRequest $request, Menu $menu) {
        $data = $request->validated();

        if ($request->hasFile('edit_image_path')) {
            (new FileService())->deleteImage($menu->image_path ?? '');
            $imagePath = (new FileService())->storeImage($request->edit_image_path, 'menus');
            // dd($imagePath);
            $data['edit_image_path'] = $imagePath;
        } else {
            unset($data['edit_image_path']);
        }

        $menuUpdate = [
            'category_id' => $data['edit_category_id'],
            'eng_name' => $data['edit_eng_name'],
            'mm_name' => $data['edit_mm_name'],
            'price' => $data['edit_price'],
            'eng_description' => $data['edit_eng_description'],
            'mm_description' => $data['edit_mm_description'],
            'image_path' => $data['edit_image_path'] ?? $menu->image_path,
            'is_available' => $data['edit_is_available']
        ];
        $menu->update($menuUpdate);
        // dd($menuUpdate);

        session()->flash('success', 'Menu updated successfully.');

        return response()->json(['redirectUrl' => route('menus.index')]);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        session()->flash('success', 'Menu deleted successfully.');

        return redirect()->route('menus.index');
    }

}
