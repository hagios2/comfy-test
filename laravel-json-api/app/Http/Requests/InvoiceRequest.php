<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'invoice_name' => 'bail|required|string|max:50',
            'additional_note' => 'bail|nullable|string',
            'issue_date' => 'bail|nullable|date|date_format:Y-m-d',
            'due_date' => 'bail|required|date|after:issue_date|date_format:Y-m-d',
            'currency' => 'bail|required|string',
            'total' => 'bail|required|numeric',
            'customer_id' => ['bail', 'required', 'integer', 'exists:customers,id'],
            'line_items' => 'bail|required|array',
            'line_items.*.item' => 'bail|required|string',
            'line_items.*.quantity' => 'bail|required|integer|min:1',
            'line_items.*.unit_price' => 'bail|required|numeric|min:1',
//            'invoice_items.*.invoice_item_id' => ['bail', 'sometimes', 'required', 'string', new EfficientUuidExists(InvoiceItem::class)],
        ];
    }
}
