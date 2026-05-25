<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = TenantPlan::query()
              ->when($request->search, function ($q, $s) {
                 return $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
              });

        $plans = $query->orderBy('sort_order')->paginate(20)->through(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'price_monthly' => number_format($p->price_monthly, 2),
            'price_yearly'  => number_format($p->price_yearly, 2),
            'is_active' => (bool) $p->is_active,
            'is_public' => (bool) $p->is_public,
            'features'  => $p->features ?? [],
        ]);

        return Inertia::render('SuperAdmin/Plans/Index', [
            'plans' => $plans,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:80|unique:tenant_plans,slug',
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_customers' => 'nullable|integer|min:0',
            'max_riders' => 'nullable|integer|min:0',
            'max_products' => 'nullable|integer|min:0',
            'max_orders_per_month' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($data['features'])) {
            $data['features'] = array_values($data['features']);
        }

        TenantPlan::create($data);

        return back()->with('success', 'Plan created.');
    }

    public function update(Request $request, TenantPlan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => "required|string|max:80|unique:tenant_plans,slug,{$plan->id}",
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_customers' => 'nullable|integer|min:0',
            'max_riders' => 'nullable|integer|min:0',
            'max_products' => 'nullable|integer|min:0',
            'max_orders_per_month' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($data['features'])) {
            $data['features'] = array_values($data['features']);
        }

        $plan->update($data);

        return back()->with('success', 'Plan updated.');
    }

    public function destroy(TenantPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }
}
