<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Wallet\ApproveWalletRechargeJob;
use App\Models\WalletTransaction;
use App\Models\WalletRechargeRequest;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = WalletTransaction::where('tenant_id', $tenantId)
            ->with('user')
            ->when($request->search, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
            )
            ->when($request->type, fn ($q, $t) => $q->where('type', $t));

        $stats = WalletTransaction::where('tenant_id', $tenantId);

        $totalIn  = (clone $stats)->where('type', 'credit')->sum('amount');
        $totalOut = (clone $stats)->where('type', 'debit')->sum('amount');

        // Pending recharge requests
        $pendingRecharges = WalletRechargeRequest::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'                 => $r->id,
                'customer_name'      => $r->user?->name ?? '—',
                'amount'             => number_format($r->amount, 2),
                'payment_method'     => $r->payment_method,
                'payment_reference'  => $r->payment_reference,
                'created_at'         => $r->created_at->format('d M Y, H:i'),
            ]);

        return Inertia::render('Admin/Wallets/Index', [
            'transactions' => $query->latest()->paginate(30)->through(fn ($t) => [
                'id'            => $t->id,
                'customer_name' => $t->user?->name ?? '—',
                'type'          => $t->type,
                'amount'        => 'Rs ' . number_format($t->amount, 2),
                'description'   => $t->description ?? '',
                'created_at'    => $t->created_at->format('d M Y, H:i'),
            ]),
            'pendingRecharges' => $pendingRecharges,
            'filters'          => $request->only('search', 'type'),
            'stats'            => [
                'totalIn'  => 'Rs ' . number_format($totalIn, 2),
                'totalOut' => 'Rs ' . number_format($totalOut, 2),
                'netFlow'  => 'Rs ' . number_format($totalIn - $totalOut, 2),
            ],
        ]);
    }

    public function credit(Request $request, User $user)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:0.01|max:1000000',
            'description' => 'nullable|string|max:255',
        ]);

        $this->walletService->credit(
            $user,
            (float) $request->amount,
            'adjustment',
            $request->description ?? 'Admin manual credit',
            ['admin_id' => auth()->id()]
        );

        return back()->with('success', 'Rs ' . number_format($request->amount, 2) . ' credited to wallet.');
    }

    public function approveRecharge(WalletRechargeRequest $recharge)
    {
        if ($recharge->status !== 'pending') {
            return back()->with('error', 'This recharge request has already been processed.');
        }

        ApproveWalletRechargeJob::dispatch($recharge->id, auth()->id())
            ->onQueue('wallets');

        return back()->with('success', 'Recharge approval queued. Wallet will be credited shortly.');
    }

    public function rejectRecharge(Request $request, WalletRechargeRequest $recharge)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        if ($recharge->status !== 'pending') {
            return back()->with('error', 'This recharge request has already been processed.');
        }

        $recharge->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes'       => $request->reason,
        ]);

        return back()->with('success', 'Recharge request rejected.');
    }
}

