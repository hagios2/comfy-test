<?php

namespace App\Services;

use App\Http\Requests\StoreInsurancePolicyRequest;
use App\Http\Requests\UpdateInsurancePolicyRequest;
use App\Http\Resources\InsurancePolicyCollection;
use App\Http\Resources\InsurancePolicyResource;
use App\Models\Customer;
use App\Models\InsurancePolicy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InsurancePolicyService
{
    public function adminStarts(): JsonResponse
    {
        $result = DB::table('insurance_policies')
            ->selectRaw("
                COUNT(CASE WHEN status = 'Active' THEN 1 END) AS active_policies,
                SUM(premium_amount) AS total_premium_amount,
                COUNT(CASE WHEN end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 END) AS expiring_policies_count
            ")
            ->first();

        $result->customers_count = Customer::query()->count();

        return response()->json([
            'message' => 'Successfully fetched Stats',
            'data' => $result
        ]);
    }
    public function index(Request $request): InsurancePolicyCollection
    {
        $limit = $request->limit ?? 1;
        $page = $request->page ?? 1;

        $query = InsurancePolicy::query()->with('customer');

        if ($request->filled('keyword')) {
            $query->whereHas('customer', function ($query) use ($request) {
                $query->where('customers.name', 'like', "%$request->keyword%");
            })
            ->orWhere('policy_no', 'like', "%$request->keyword%");
        }

         if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $policies = $query->offset($page)
            ->paginate($limit);

        return new InsurancePolicyCollection($policies);
    }


    public function store(StoreInsurancePolicyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $lastPolicy = InsurancePolicy::query()->latest()->first('id');
        $lastPolicyNo = ( int ) ($lastPolicy?->id ?? 0) + 1;
        $policyNo = 'COMFY-' . Carbon::now()->format('ymd'). sprintf("%04d", $lastPolicyNo);
        Arr::set($data, 'policy_no', $policyNo);

        $policy = InsurancePolicy::create($data);

        return response()->json([
            'message' => 'Insurance Policy added successfully',
            'data' => new InsurancePolicyResource($policy->refresh()),
        ], 201);
    }


    public function show(InsurancePolicy $insurancePolicy): JsonResponse
    {
        return response()->json([
            'message' => 'Insurance Policy added successfully',
            'data' => new InsurancePolicyResource($insurancePolicy),
        ]);
    }


    public function update(InsurancePolicy $insurancePolicy, UpdateInsurancePolicyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $insurancePolicy->update($data);

        return response()->json([
            'message' => 'Insurance Policy updated successfully',
            'data' => new InsurancePolicyResource($insurancePolicy->refresh()),
        ]);
    }


    public function destroy(InsurancePolicy $insurancePolicy): JsonResponse
    {
        $insurancePolicy->delete();

         return response()->json([
            'message' => 'Insurance Policy deleted successfully',
            'data' => [],
        ]);
    }
}


