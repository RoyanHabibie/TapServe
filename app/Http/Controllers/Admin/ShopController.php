<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function show()
    {
        $shop = auth()->user()->shop;
        return view('admin.settings.shop', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:100'],
            'logo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $shop = auth()->user()->shop;

        $data = [
            'name'    => $request->name,
            'slug'    => Str::slug($request->name),
            'address' => $request->address,
            'phone'   => $request->phone,
            'email'   => $request->email,
        ];

        if ($request->hasFile('logo')) {
            if ($shop->logo) {
                Storage::disk('public')->delete($shop->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $shop->update($data);

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function destroyLogo()
    {
        $shop = auth()->user()->shop;
        if ($shop->logo) {
            Storage::disk('public')->delete($shop->logo);
            $shop->update(['logo' => null]);
        }
        return back()->with('success', 'Logo berhasil dihapus.');
    }
}
