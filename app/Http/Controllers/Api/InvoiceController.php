<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InvoiceStoreRequest;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    // List with filters + pagination
    public function index(Request $r)
    {
        $q = Invoice::with('customer')
            ->when($r->customer_id, fn($qq) => $qq->where('customer_id', $r->customer_id))
            ->when($r->date_from, fn($qq) => $qq->whereDate('created_at', '>=', $r->date_from))
            ->when($r->date_to, fn($qq) => $qq->whereDate('created_at', '<=', $r->date_to))
            ->when($r->search, fn($qq) => $qq->whereHas(
                'customer',
                fn($c) => $c->where('name', 'like', "%{$r->search}%")
            ))
            ->orderByDesc('created_at');

        return response()->json($q->paginate($r->integer('per_page', 10)));
    }

    // Show single invoice (with items + customer)
    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load('items', 'customer'));
    }

    // Create invoice (atomic, deduct stock)
    public function store(InvoiceStoreRequest $req)
    {
        $userId = $req->user()->id;

        return DB::transaction(function () use ($req, $userId) {
            $itemsIn = collect($req->items);
            $productIds = $itemsIn->pluck('product_id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()->get()->keyBy('id');

            $prepared = [];
            $total = '0.00';

            foreach ($itemsIn as $row) {
                $p = $products[$row['product_id']];
                $qty = (int)$row['quantity'];

                if ($p->quantity < $qty) {
                    abort(response()->json([
                        'message' => "Insufficient stock for SKU {$p->sku}"
                    ], 409));
                }

                $price = (string)$p->price; // snapshot
                $subtotal = bcmul($price, (string)$qty, 2);
                $total = bcadd($total, $subtotal, 2);

                // deduct stock
                $p->decrement('quantity', $qty);

                $prepared[] = [
                    'product_id' => $p->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ];
            }

            $invoice = Invoice::create([
                'customer_id' => $req->customer_id,
                'total'       => $total,
                'created_by'  => $userId,
            ]);

            $invoice->items()->createMany($prepared);

            return response()->json($invoice->load('items', 'customer'), 201);
        });
    }
}
