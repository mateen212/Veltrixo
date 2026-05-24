<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\RechargeWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\WalletRechargeRequest;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function show(Request $request): Response
    {
        $user = $request->user();
        $wallet = $this->walletService->getOrCreate($user);

        return Inertia::render('Customer/Wallet', [
            'wallet'       => new WalletResource($wallet),
            'transactions' => $wallet->transactions()->latest()->paginate(20),
            'recharges'    => WalletRechargeRequest::where('user_id', $user->id)->latest()->limit(10)->get(),
        ]);
    }

    public function recharge(RechargeWalletRequest $request): JsonResponse
    {
        $recharge = WalletRechargeRequest::create([
            'uuid'              => \Str::uuid(),
            'tenant_id'         => $request->user()->tenant_id,
            'user_id'           => $request->user()->id,
            'wallet_id'         => $this->walletService->getOrCreate($request->user())->id,
            'amount'            => $request->validated('amount'),
            'payment_method'    => $request->validated('payment_method'),
            'payment_reference' => $request->validated('payment_reference'),
            'notes'             => $request->validated('notes'),
            'status'            => 'pending',
        ]);

        if ($request->hasFile('receipt_image')) {
            $recharge->addMedia($request->file('receipt_image'))->toMediaCollection('receipt');
        }

        return response()->json(['data' => $recharge, 'message' => 'Recharge request submitted.'], 201);
    }
}
