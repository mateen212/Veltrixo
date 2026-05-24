<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service) {}

    public function index(Request $request): Response
    {
        $subscriptions = Subscription::where('tenant_id', $request->user()->tenant_id)
            ->with(['user', 'address', 'items.product'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => SubscriptionResource::collection($subscriptions),
        ]);
    }

    public function show(Subscription $subscription): Response
    {
        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => new SubscriptionResource(
                $subscription->load(['user', 'address', 'items.product', 'deliveries', 'invoices'])
            ),
        ]);
    }

    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        return response()->json(['data' => new SubscriptionResource(
            $this->service->cancel($subscription, $validated['reason'])
        )]);
    }

    public function pause(Request $request, Subscription $subscription): JsonResponse
    {
        $validated = $request->validate(['pause_until' => ['nullable', 'date', 'after:today']]);

        return response()->json(['data' => new SubscriptionResource(
            $this->service->pause($subscription, $validated['pause_until'] ?? null)
        )]);
    }

    public function resume(Subscription $subscription): JsonResponse
    {
        return response()->json(['data' => new SubscriptionResource(
            $this->service->resume($subscription)
        )]);
    }
}
