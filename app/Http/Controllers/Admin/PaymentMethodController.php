<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    private function shopId(): int
    {
        return auth()->user()->shop_id;
    }

    public function index()
    {
        $methods = PaymentMethod::where('shop_id', $this->shopId())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.payment-methods.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $shopId = $this->shopId();

        $request->validate([
            'name'       => ['required', 'string', 'max:50'],
            'icon'       => ['required', 'string', 'max:50'],
            'color'      => ['required', 'string', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
        ]);

        $code = Str::slug($request->name, '_');

        if (PaymentMethod::where('shop_id', $shopId)->where('code', $code)->exists()) {
            return back()->withInput()
                ->with('error', 'Metode pembayaran dengan nama serupa sudah ada.');
        }

        PaymentMethod::create([
            'shop_id'    => $shopId,
            'name'       => $request->name,
            'code'       => $code,
            'icon'       => $request->icon,
            'color'      => $request->color,
            'is_active'  => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        abort_if($paymentMethod->shop_id !== $this->shopId(), 403);
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        abort_if($paymentMethod->shop_id !== $this->shopId(), 403);

        $request->validate([
            'name'       => ['required', 'string', 'max:50'],
            'icon'       => ['required', 'string', 'max:50'],
            'color'      => ['required', 'string', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
        ]);

        $paymentMethod->update([
            'name'       => $request->name,
            'icon'       => $request->icon,
            'color'      => $request->color,
            'sort_order' => $request->sort_order ?? $paymentMethod->sort_order,
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        abort_if($paymentMethod->shop_id !== $this->shopId(), 403);

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        abort_if($paymentMethod->shop_id !== $this->shopId(), 403);

        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $paymentMethod->is_active,
        ]);
    }
}
