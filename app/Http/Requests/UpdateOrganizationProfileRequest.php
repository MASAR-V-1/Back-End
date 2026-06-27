<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateOrganizationProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // لازم يكون org_admin وعندو مؤسسة، والباقي نتحقق منه بالـ withValidator
        return $this->user()->isOrgAdmin() && $this->user()->organization;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {    // كل الحقول هون nullable لأنه ممكن org_admin يحدث بس جزء، ويرجع بعدين يكمل الباقي
        return [
            'organization_name' => ['nullable', 'string', 'max:255'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'organization_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[1-9][0-9]{7,14}$/'
            ],
            'organization_description' => ['nullable', 'string'],
            'organization_region' => ['nullable', Rule::in(Organization::REGIONS)],
            'organization_type' => ['nullable', Rule::in(Organization::TYPES)],

        ];
    }

    public function messages(): array
    {
        return [
            'organization_region.in' => 'المنطقة المحددة غير صحيحة.',
            'organization_type.in' => 'نوع المؤسسة المحدد غير صحيح.',
            'organization_phone.regex' => 'رقم الهاتف غير صحيح. يجب أن يكون بصيغة دولية صحيحة (مثال: +970599999999).',
        ];
    }


    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $organization = $this->user()->organization;

            // الحقول الرئيسية الي حاول يبعتها
            $mainFieldsSent = array_filter([
                'organization_name' => $this->organization_name,
                'admin_name' => $this->admin_name,
                'organization_region' => $this->organization_region,
                'organization_type' => $this->organization_type,
            ], fn($value) => !is_null($value));

            // لو بعت أي حقل رئيسي وهو ممنوع يعدل عليهم بهاد الحالة
            if (!empty($mainFieldsSent) && !$organization->canEditMainFields()) {
                $validator->errors()->add(
                    'main_fields',
                    'لا يمكنك تعديل البيانات الرئيسية (الاسم، المنطقة، نوع المؤسسة، اسم المسؤول) خلال المراجعة او بعد الموافقة على الحساب. للتعديل، يرجى التواصل مع الإدارة.'
                );
            }

            // الحقول الثانوية - تأكد إنه مش rejected
            $minorFieldsSent = array_filter([
                'organization_phone' => $this->organization_phone,
                'organization_description' => $this->organization_description,
            ], fn($value) => !is_null($value));

            if (!empty($minorFieldsSent) && !$organization->canEditMinorFields()) {
                $validator->errors()->add('minor_fields', 'لا يمكنك تعديل البيانات حاليًا.');
            }
        });
    }
}

