<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // أي شخص ممكن يسجل مؤسسة جديدة
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // بيانات المؤسسة
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_email' => [
                'required',
                'email',
                Rule::unique('organizations', 'email')->withoutTrashed(),
            ],
            'organization_phone' => ['nullable', 'string', 'max:20'],
            'organization_description' => ['nullable', 'string'],
            'organization_region' => ['required', Rule::in(Organization::REGIONS)],
            'organization_type' => ['required', Rule::in(Organization::TYPES)],
            'agreed_to_terms' => ['required', 'boolean', 'accepted'],
            // بيانات الـ admin الشخصية
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->withoutTrashed(),
            ],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'], // لازم يكون فيه حقل admin_password_confirmation
        ];
    }
    public function messages(): array
    {
        return [
            'organization_email.unique' => 'هاد الإيميل مسجل مسبقًا لمؤسسة أخرى.',
            'admin_email.unique' => 'هاد الإيميل مستخدم مسبقًا.',
            'organization_region.required' => 'يرجى تحديد المنطقة.',
            'organization_region.in' => 'المنطقة المحددة غير صحيحة.',
            'organization_type.required' => 'يرجى تحديد نوع المؤسسة.',
            'organization_type.in' => 'نوع المؤسسة المحدد غير صحيح.',
            'agreed_to_terms.accepted' => 'يجب الموافقة على الشروط والأحكام للتسجيل.',
        ];
    }
}
