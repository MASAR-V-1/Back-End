<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'agreed_to_terms' => ['required', 'boolean', 'accepted'],
            'admin_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->withoutTrashed(),
            ],
            'admin_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ], // لازم يكون فيه حقل admin_password_confirmation

            'organization_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[1-9][0-9]{7,14}$/'
            ],
            'organization_region' => ['nullable', Rule::in(Organization::REGIONS)],
            'organization_type' => ['nullable', Rule::in(Organization::TYPES)],

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
            'organization_name.required' => 'اسم المنظمة مطلوب',
            'organization_email.required' => 'البريد الإلكتروني للمنظمة مطلوب',
            'agreed_to_terms.required' => 'يجب الموافقة على الشروط',
            'admin_email.required' => 'البريد الإلكتروني للمدير مطلوب',
            'admin_password.required' => 'كلمة المرور مطلوبة',
            'admin_password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'admin_password.confirmed' => 'كلمة المرور غير متطابقة',
            'organization_phone.regex' => 'رقم الهاتف غير صحيح. يجب أن يكون بصيغة دولية صحيحة (مثال: +970599999999).',
        ];
    }
}
