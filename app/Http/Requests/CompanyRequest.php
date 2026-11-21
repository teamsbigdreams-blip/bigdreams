<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $company = $this->route('company');
        $companyId = $company?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // Sesuaikan nama tabel ('m_company' atau 'company') dengan tabel aktual di database
        $table = 'company';

        return [
            'company_name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique($table, 'company_name')->ignore($companyId),
            ],
            'company_code' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:6',
                Rule::unique($table, 'company_code')->ignore($companyId),
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
            'company_name' => 'Company Name',
            'company_code' => 'Company Code',
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
