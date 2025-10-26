<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::query()
            ->when($r->search, fn($qq) => $qq->where(function ($w) use ($r) {
                $w->where('name', 'like', "%{$r->search}%")
                    ->orWhere('sku', 'like', "%{$r->search}%");
            }))
            ->orderByDesc('created_at');

        return response()->json($q->paginate($r->integer('per_page', 10)));
    }

    public function store(ProductRequest $req)
    {
        return response()->json(Product::create($req->validated()), 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(ProductRequest $req, Product $product)
    {
        $product->update($req->validated());
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'deleted']);
    }
}
