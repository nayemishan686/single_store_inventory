<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $r)
    {
        $q = Customer::query()
            ->when($r->search, fn($qq) => $qq->where(function ($w) use ($r) {
                $w->where('name', 'like', "%{$r->search}%")
                    ->orWhere('email', 'like', "%{$r->search}%");
            }))
            ->orderByDesc('created_at');

        return response()->json($q->paginate($r->integer('per_page', 10)));
    }

    public function store(CustomerRequest $req)
    {
        return response()->json(Customer::create($req->validated()), 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(CustomerRequest $req, Customer $customer)
    {
        $customer->update($req->validated());
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'deleted']);
    }
}
