<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use App\Models\Rider;
use App\Services\Delivery\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    public function __construct(private DeliveryService $service) {}

    public function index(Request $request): Response
    {
        $date = $request->get('date', today()->toDateString());
        $tenantId = $request->user()->tenant_id;

        $deliveries = Delivery::where('tenant_id', $tenantId)
            ->where('delivery_date', $date)
            ->with(['user', 'address', 'rider', 'items'])
            ->orderBy('status')
            ->paginate(50);

        $riders = Rider::forTenant($tenantId)->available()->with('user')->get();

        return Inertia::render('Admin/Deliveries/Index', [
            'deliveries' => DeliveryResource::collection($deliveries),
            'riders'     => $riders,
            'date'       => $date,
            'stats'      => [
                'total'      => $deliveries->total(),
                'delivered'  => Delivery::where('tenant_id', $tenantId)->where('delivery_date', $date)->where('status', 'delivered')->count(),
                'pending'    => Delivery::where('tenant_id', $tenantId)->where('delivery_date', $date)->whereIn('status', ['scheduled', 'assigned'])->count(),
                'missed'     => Delivery::where('tenant_id', $tenantId)->where('delivery_date', $date)->where('status', 'missed')->count(),
            ],
        ]);
    }

    public function assign(Request $request, Delivery $delivery): JsonResponse
    {
        $validated = $request->validate(['rider_id' => ['required', 'integer', 'exists:riders,id']]);

        $rider = Rider::findOrFail($validated['rider_id']);
        $delivery = $this->service->assignRider($delivery, $rider);

        return response()->json(['data' => new DeliveryResource($delivery->load(['rider.user']))]);
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'delivery_ids' => ['required', 'array'],
            'delivery_ids.*' => ['integer'],
            'rider_id'     => ['required', 'integer', 'exists:riders,id'],
        ]);

        $rider = Rider::findOrFail($validated['rider_id']);
        $count = $this->service->bulkAssign($validated['delivery_ids'], $rider);

        return response()->json(['message' => "{$count} deliveries assigned.", 'count' => $count]);
    }

    public function markMissed(Request $request, Delivery $delivery): JsonResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        return response()->json(['data' => new DeliveryResource(
            $this->service->markMissed($delivery, $validated['reason'])
        )]);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate(['date' => ['nullable', 'date']]);

        $deliveries = $this->service->generateDailyDeliveries(
            $request->user()->tenant_id,
            isset($validated['date']) ? \Carbon\Carbon::parse($validated['date']) : today()
        );

        return response()->json(['message' => count($deliveries) . ' deliveries generated.', 'count' => count($deliveries)]);
    }
}
