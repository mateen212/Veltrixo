<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WalletRecharge;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletTransaction::with('tenant')
            ->when($request->search, fn($q, $s) => $q->whereHas('tenant', fn($u) => $u->where('name', 'like', "%$s%")))
            ->when($request->type, fn($q, $t) => $q->where('type', $t));

        $totalIn  = WalletTransaction::where('type', 'credit')->sum('amount');
        $totalOut = WalletTransaction::where('type', 'debit')->sum('amount');

        return Inertia::render('Admin/Wallets/Index', [
            'transactions' => $query->latest()->paginate(30)->through(fn($t) => [
                'id'            => $t->id,
                'customer_name' => $t->tenant?->name ?? '—',
                'type'          => $t->type,
                'amount'        => number_format($t->amount, 2),
                'description'   => $t->description ?? '',
                'created_at'    => $t->created_at->format('Y-m-d H:i'),
            ]),
            'filters' => $request->only('search', 'type'),
            'stats'   => [
                'totalIn'  => number_format($totalIn, 2),
                'totalOut' => number_format($totalOut, 2),
                'netFlow'  => number_format($totalIn - $totalOut, 2),
            ],
        ]);
    }

    public function credit(Request $request, User $user)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01', 'description' => 'nullable|string|max:255']);
        WalletTransaction::create([
            'tenant_id'   => $user->id,
            'type'        => 'credit',
            'amount'      => $request->amount,
            'description' => $request->description ?? 'Admin credit',
        ]);
        return back()->with('success', 'Wallet credited.');
    }

    public function approveRecharge(WalletRecharge $recharge)
    {
        $recharge->update(['status' => 'approved']);
        WalletTransaction::create([
            'tenant_id'   => $recharge->tenant_id,
            'type'        => 'credit',
            'amount'      => $recharge->amount,
            'description' => 'Recharge approved',
        ]);
        return back()->with('success', 'Recharge approved.');
    }

    public function rejectRecharge(WalletRecharge $recharge)
    {
        $recharge->update(['status' => 'rejected']);
        return back()->with('success', 'Recharge rejected.');
    }
}
