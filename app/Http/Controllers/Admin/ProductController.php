<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::forTenant($request->user()->tenant_id)
            ->with(['category', 'variants'])
            ->withCount('subscriptionItems')
            ->orderBy('sort_order')
            ->paginate(20);

        $categories = Category::where('tenant_id', $request->user()->tenant_id)->active()->get();

        return Inertia::render('Admin/Products/Index', [
            'products'   => ProductResource::collection($products),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'category_id'             => ['nullable', 'integer', 'exists:categories,id'],
            'description'             => ['nullable', 'string'],
            'sku'                     => ['nullable', 'string', 'max:100'],
            'unit'                    => ['required', 'string', 'max:50'],
            'price'                   => ['required', 'numeric', 'min:0'],
            'sale_price'              => ['nullable', 'numeric', 'min:0'],
            'is_active'               => ['boolean'],
            'is_subscription_product' => ['boolean'],
            'is_featured'             => ['boolean'],
            'sort_order'              => ['integer', 'min:0'],
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $validated['slug'] = \Str::slug($validated['name']);

        $product = Product::create($validated);

        return response()->json(['data' => new ProductResource($product)], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'price'       => ['sometimes', 'numeric', 'min:0'],
            'sale_price'  => ['nullable', 'numeric', 'min:0'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer'],
            'description' => ['nullable', 'string'],
        ]);

        $product->update($validated);

        return response()->json(['data' => new ProductResource($product)]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);
        $product->delete();
        return response()->json(['message' => 'Product deleted.']);
    }
}
