<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InsurancePolicyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Fetched Policies successfully.',
            'data' => $this->collection->transform(function ($policy) {
                return [
                    'id' => $policy->id,
                    'policy_no' => $policy->policy_no,
                    'policy_type' => $policy->policy_type,
                    'start_date' => $policy->start_date->format('Y-m-d'),
                    'formatted_start_date' => $policy->start_date->format('D, d F Y'),
                    'end_date' => $policy->end_date->format('Y-m-d'),
                    'formatted_end_date' => $policy->end_date->format('D, d F Y'),
                    'premium_amount' => $policy->premium_amount,
                    'status' => $policy->status,
                    'customer' => new CustomerResource($policy->customer),
                    'created_at' => $policy->created_at->format('D, d F Y'),
                ];
            }),
            'links' => [
                'self' => 'link-value',
            ]
        ];
    }
}
