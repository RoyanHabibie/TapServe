<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuStoreRequest;
use App\Http\Requests\MenuUpdateRequest;
use App\Models\Category;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = $this->menuService->getAll(auth()->user()->shop_id);
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::where('shop_id', auth()->user()->shop_id)->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(MenuStoreRequest $request)
    {
        $this->menuService->store(auth()->user()->shop_id, $request->validated());
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::where('shop_id', auth()->user()->shop_id)->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(MenuUpdateRequest $request, Menu $menu)
    {
        $this->menuService->update($menu, $request->validated());
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $this->menuService->delete($menu);
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}
