<?php

namespace App\Http\Controllers\Customer;

use App\DTOs\Subscription\CreateSubscriptionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
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
        $subscriptions = Subscription::where('user_id', $request->user()->id)
            ->with(['items.product', 'address'])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Customer/Subscriptions/Index', [
            'subscriptions' => SubscriptionResource::collection($subscriptions),
        ]);
    }

    public function store(CreateSubscriptionRequest $request): JsonResponse
    {
        $dto = CreateSubscriptionDTO::fromArray(
            $request->validated(),
            $request->user()->tenant_id,
            $request->user()->id
        );

        $subscription = $this->service->create($dto);

        return response()->json(['data' => new SubscriptionResource($subscription)], 201);
    }

    public function show(Subscription $subscription): Response
    {
        $this->authorize('view', $subscription);

        return Inertia::render('Customer/Subscriptions/Show', [
            'subscription' => new SubscriptionResource(
                $subscription->load(['items.product', 'address', 'deliveries', 'invoices'])
            ),
        ]);
    }

    public function pause(Request $request, Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);
        $validated = $request->validate(['pause_until' => ['nullable', 'date', 'after:today']]);

        return response()->json(['data' => new SubscriptionResource(
            $this->service->pause($subscription, $validated['pause_until'] ?? null)
        )]);
    }

    public function resume(Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);

        return response()->json(['data' => new SubscriptionResource(
            $this->service->resume($subscription)
        )]);
    }

    public function cancel(Request $request, Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        return response()->json(['data' => new SubscriptionResource(
            $this->service->cancel($subscription, $validated['reason'])
        )]);
    }

    public function skip(Request $request, Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);
        $validated = $request->validate([
            'date'   => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $skip = $this->service->skipDate($subscription, $validated['date'], $validated['reason'] ?? '');

        return response()->json(['data' => $skip]);
    }
}
