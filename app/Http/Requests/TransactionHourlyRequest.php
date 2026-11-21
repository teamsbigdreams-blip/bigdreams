<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionHourlyRequest extends FormRequest
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
        $transactionHourly = $this->route('transactionHourly') ?? $this->route('transaction_hourly');
        $transactionHourlyId = $transactionHourly?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $companyId = $this->input('company_id') ?? $transactionHourly?->company_id;
        $datetime = $this->input('datetime') ?? $transactionHourly?->datetime;

        $uniqueCompanyDatetime = Rule::unique('transaction_hourly', 'company_id')
            ->where(fn($query) => $query->where('datetime', $datetime));

        if ($transactionHourlyId !== null) {
            $uniqueCompanyDatetime->ignore($transactionHourlyId);
        }

        return [
            'company_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                Rule::exists('company', 'id'),
                ...($companyId !== null && $datetime !== null ? [$uniqueCompanyDatetime] : []),
            ],
            'datetime' => [
                $isUpdate ? 'sometimes' : 'required',
                'date',
            ],
            'open' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'high' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'low' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'close' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'volume' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_id' => 'Company',
            'datetime' => 'Datetime',
            'open' => 'Open Price',
            'high' => 'High Price',
            'low' => 'Low Price',
            'close' => 'Close Price',
            'volume' => 'Volume',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not be greater than :max characters.',
        ];
    }
}
