<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerCollection;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $service){}


    public function index(): CustomerCollection
    {
        return $this->service->index();
    }


    public function store(CustomerRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }


    public function show(Customer $customer)
    {
        return $this->service->show($customer);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        return $this->service->update($customer, $request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        return $this->service->destroy($customer);
    }
}
