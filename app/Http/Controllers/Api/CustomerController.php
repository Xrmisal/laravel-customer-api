<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Customer::all(), 200);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $customer = Customer::create($request->validated());
    
            return response()->json([
                'message' => 'Customer added',
                'data' => $customer,
            ], 201);
        } catch (\Throwable $e) {
            // Catch database or unexpected exceptions
            return response()->json([
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
    

    public function show(string $id)
    {
        return Customer::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());
        return response()->json($customer, 200);
    }

    public function destroy(string $id)
    {
        Customer::destroy($id);
        return response()->json(null, 204);
    }
}
