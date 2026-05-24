<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->active !== null && $request->active !== '', fn($q) => $q->where('is_active', (bool) $request->active));

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->latest()->paginate(20)->through(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'description' => $p->description,
                'price'       => number_format($p->price, 2),
                'category'    => $p->category ?? '—',
                'unit'        => $p->unit ?? 'unit',
                'is_active'   => (bool) $p->is_active,
            ]),
            'filters' => $request->only('search', 'active'),
        ]);
    }

    public function create() { return Inertia::render('Admin/Products/Create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'unit'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
        ]);
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function show(Product $product)
    {
        return Inertia::render('Admin/Products/Show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'unit'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
        ]);
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
