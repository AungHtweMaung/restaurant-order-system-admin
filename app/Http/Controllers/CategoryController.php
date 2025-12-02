<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::filter()->latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function store(CategoryStoreRequest $request)
    {
        Category::create([
            'eng_name' => $request->eng_name,
            'mm_name' => $request->mm_name,
        ]);

        session()->flash('success', 'Category created successfully.');

        return response()->json(['redirectUrl' => route('categories.index')]);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update([
            'eng_name' => $request->eng_name,
            'mm_name' => $request->mm_name,
        ]);

        session()->flash('success', 'Category updated successfully.');

        return response()->json(['redirectUrl' => route('categories.index')]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        session()->flash('success', 'Category deleted successfully.');

        return redirect()->route('categories.index');
    }


}
