<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    public function index(Request $request): Response
    {
        $deliveries = Delivery::where('user_id', $request->user()->id)
            ->with(['items.product', 'rider.user', 'address'])
            ->orderByDesc('delivery_date')
            ->paginate(20);

        return Inertia::render('Customer/Deliveries/Index', [
            'deliveries' => DeliveryResource::collection($deliveries),
        ]);
    }

    public function show(Delivery $delivery): Response
    {
        $this->authorize('view', $delivery);

        return Inertia::render('Customer/Deliveries/Show', [
            'delivery' => new DeliveryResource($delivery->load(['items.product', 'rider.user', 'address', 'invoice'])),
        ]);
    }
}
