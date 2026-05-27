<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableSession;
use Illuminate\Http\Request;
use App\Services\SessionService;

class SessionController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function index()
    {
        $sessions = TableSession::with('table')
            ->where('shop_id', auth()->user()->shop_id)
            ->whereIn('status', ['open', 'payment_pending'])
            ->latest()
            ->get();

        return view('admin.sessions.index', compact('sessions'));
    }

    public function close($id)
    {
        $session = TableSession::where('shop_id', auth()->user()->shop_id)->findOrFail($id);
        $this->sessionService->closeSession($session);

        return redirect()->route('admin.sessions.index')->with('success', 'Session ditutup.');
    }

    public function cancel($id)
    {
        $session = TableSession::where('shop_id', auth()->user()->shop_id)->findOrFail($id);
        $this->sessionService->cancelSession($session);

        return redirect()->route('admin.sessions.index')->with('success', 'Session dibatalkan.');
    }
}
