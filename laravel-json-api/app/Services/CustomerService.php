<?php

namespace App\Services;

use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerService
{
     public function index(): CustomerCollection
     {
        $customers = Customer::query()
            ->latest()
            ->paginate(15);

        return new CustomerCollection($customers);
    }
     public function store(CustomerRequest $request): JsonResponse
     {
        $customer = Customer::create($request->validated());

        return response()->json([
            'status' => 200,
            'data' => new CustomerResource($customer),
            'message' => 'Customer created successfully',
        ]);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'status' => 200,
            'data' => new CustomerResource($customer),
            'message' => 'Fetched successfully',
        ]);
    }

    public function update(Customer $customer, CustomerRequest $request): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json([
            'status' => 200,
            'data' => new CustomerResource($customer->refresh()),
            'message' => 'Customer update successfully',
        ]);

    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'status' => 200,
            'data' => [],
            'message' => 'Deleted customer successfully',
        ]);
    }

}
