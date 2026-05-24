<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
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
        $rider = $request->user()->rider;

        abort_unless($rider, 403, 'Rider profile not found.');

        $date = $request->get('date', today()->toDateString());

        $deliveries = Delivery::where('rider_id', $rider->id)
            ->where('delivery_date', $date)
            ->with(['user', 'address', 'items.product'])
            ->orderBy('status')
            ->get();

        return Inertia::render('Rider/Deliveries/Index', [
            'deliveries' => DeliveryResource::collection($deliveries),
            'date'       => $date,
        ]);
    }

    public function start(Delivery $delivery): JsonResponse
    {
        $this->authorize('update', $delivery);

        return response()->json(['data' => new DeliveryResource(
            $this->service->startDelivery($delivery)
        )]);
    }

    public function complete(Request $request, Delivery $delivery): JsonResponse
    {
        $this->authorize('complete', $delivery);

        $validated = $request->validate([
            'otp'          => ['nullable', 'string', 'size:6'],
            'proof_image'  => ['nullable', 'image', 'max:5120'],
            'latitude'     => ['nullable', 'numeric'],
            'longitude'    => ['nullable', 'numeric'],
            'rider_notes'  => ['nullable', 'string', 'max:500'],
        ]);

        // Verify OTP if provided
        if (!empty($validated['otp'])) {
            if (!$this->service->verifyOtp($delivery, $validated['otp'])) {
                return response()->json(['message' => 'Invalid OTP.'], 422);
            }
            $validated['otp_verified'] = true;
        }

        if ($request->hasFile('proof_image')) {
            $delivery->addMedia($request->file('proof_image'))->toMediaCollection('proof');
        }

        return response()->json(['data' => new DeliveryResource(
            $this->service->complete($delivery, $validated)
        )]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $rider = $request->user()->rider;
        abort_unless($rider, 403);

        $rider->update([
            'current_latitude'   => $validated['latitude'],
            'current_longitude'  => $validated['longitude'],
            'location_updated_at' => now(),
        ]);

        return response()->json(['message' => 'Location updated.']);
    }
}
