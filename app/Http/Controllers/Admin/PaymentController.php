<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\TableSession;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Form pembayaran untuk session tertentu.
     */
    public function show(TableSession $session)
    {
        // Pastikan session milik shop user
        if ($session->shop_id !== auth()->user()->shop_id) {
            abort(403);
        }

        $total = $session->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        return view('admin.payments.process', compact('session', 'total'));
    }

    /**
     * Proses pembayaran.
     */
    public function store(ProcessPaymentRequest $request, TableSession $session)
    {
        if ($session->shop_id !== auth()->user()->shop_id) {
            abort(403);
        }

        try {
            $this->paymentService->process(
                $session,
                $request->amount,
                $request->method
            );

            return redirect()->route('cashier.dashboard')
                ->with('success', 'Pembayaran berhasil. Session ditutup.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
