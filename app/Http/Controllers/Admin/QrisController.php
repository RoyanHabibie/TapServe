<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrisController extends Controller
{
    public function show()
    {
        $shop = auth()->user()->shop;
        return view('admin.settings.qris', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $shop = auth()->user()->shop;

        if ($shop->qris_image) {
            Storage::disk('public')->delete($shop->qris_image);
        }

        $path = $request->file('qris_image')->store('qris', 'public');

        $shop->update(['qris_image' => $path]);

        return back()->with('success', 'Gambar QRIS berhasil diperbarui.');
    }

    public function destroy()
    {
        $shop = auth()->user()->shop;

        if ($shop->qris_image) {
            Storage::disk('public')->delete($shop->qris_image);
            $shop->update(['qris_image' => null]);
        }

        return back()->with('success', 'Gambar QRIS berhasil dihapus.');
    }
}
