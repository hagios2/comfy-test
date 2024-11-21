<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInsurancePolicyRequest;
use App\Http\Requests\UpdateInsurancePolicyRequest;
use App\Http\Resources\InsurancePolicyCollection;
use App\Models\InsurancePolicy;
use App\Services\InsurancePolicyService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsurancePolicyController extends Controller
{
     use AuthorizesRequests;
    public function __construct(protected InsurancePolicyService $service)
    {
    }

    public function adminStats(): JsonResponse
    {
        return $this->service->adminStarts();
    }

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): InsurancePolicyCollection
    {
        $this->authorize('execute', InsurancePolicy::class);

        return $this->service->index($request);
    }


    /**
     * @throws AuthorizationException
     */
    public function store(StoreInsurancePolicyRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }


    /**
     * @throws AuthorizationException
     */
    public function show(InsurancePolicy $insurancePolicy): JsonResponse
    {
        $this->authorize('execute', InsurancePolicy::class);

        return $this->service->show($insurancePolicy);
    }


    /**
     * @throws AuthorizationException
     */
    public function update(UpdateInsurancePolicyRequest $request, InsurancePolicy $insurancePolicy): JsonResponse
    {
        $this->authorize('execute', InsurancePolicy::class);

        return $this->service->update($insurancePolicy, $request);
    }


    /**
     * @throws AuthorizationException
     */
    public function destroy(InsurancePolicy $insurancePolicy): JsonResponse
    {
         $this->authorize('delete', InsurancePolicy::class);

         return $this->service->destroy($insurancePolicy);
    }
}
