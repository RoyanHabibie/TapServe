<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableStoreRequest;
use App\Http\Requests\TableUpdateRequest;
use App\Models\RestaurantTable;
use App\Services\RestaurantTableService;
use Illuminate\Http\Request;

class TableController extends Controller
{
    private RestaurantTableService $tableService;

    public function __construct(RestaurantTableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index()
    {
        $tables = $this->tableService->getAll(auth()->user()->shop_id);
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(TableStoreRequest $request)
    {
        $this->tableService->store(auth()->user()->shop_id, $request->validated());
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(RestaurantTable $table)
    {
        abort_if($table->shop_id !== auth()->user()->shop_id, 403);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(TableUpdateRequest $request, RestaurantTable $table)
    {
        abort_if($table->shop_id !== auth()->user()->shop_id, 403);
        $this->tableService->update($table, $request->validated());
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(RestaurantTable $table)
    {
        abort_if($table->shop_id !== auth()->user()->shop_id, 403);
        $this->tableService->delete($table);
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    public function printQr(RestaurantTable $table)
    {
        abort_if($table->shop_id !== auth()->user()->shop_id, 403);
        $url = route('public.menu', ['token' => $table->token]);
        return view('admin.tables.qrcode', compact('table', 'url'));
    }

    public function printAllQr()
    {
        $tables = $this->tableService->getAll(auth()->user()->shop_id);
        return view('admin.tables.qrcodes', compact('tables'));
    }
}
