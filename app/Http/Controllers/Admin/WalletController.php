<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletRechargeRequest;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index(Request $request): Response
    {
        $wallets = Wallet::where('tenant_id', $request->user()->tenant_id)
            ->with('user')
            ->orderByDesc('balance')
            ->paginate(20);

        $pendingRecharges = WalletRechargeRequest::where('tenant_id', $request->user()->tenant_id)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return Inertia::render('Admin/Wallets/Index', [
            'wallets'         => WalletResource::collection($wallets),
            'pendingRecharges' => $pendingRecharges,
        ]);
    }

    public function credit(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'amount'      => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $transaction = $this->walletService->credit(
            $user,
            $validated['amount'],
            'admin_credit',
            $validated['description']
        );

        return response()->json(['data' => $transaction]);
    }

    public function approveRecharge(Request $request, WalletRechargeRequest $recharge): JsonResponse
    {
        $recharge->update(['status' => 'approved', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        $this->walletService->credit(
            $recharge->user,
            (float) $recharge->amount,
            'wallet_recharge',
            'Wallet recharge approved',
            ['recharge_id' => $recharge->id]
        );

        return response()->json(['message' => 'Recharge approved.']);
    }

    public function rejectRecharge(Request $request, WalletRechargeRequest $recharge): JsonResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $recharge->update([
            'status'           => 'rejected',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        return response()->json(['message' => 'Recharge rejected.']);
    }
}
