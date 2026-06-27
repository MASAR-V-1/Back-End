<x-app-layout>
    {{-- رأس الصفحة وزر العودة --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div class="flex items-center gap-2.5">
            <div class="p-2 bg-white rounded-lg shadow-sm border border-slate-200 text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">تفاصيل المؤسسة: {{ $organization->name }}</h1>
                <p class="text-xs text-slate-500 font-medium">مراجعة البيانات الكاملة للمؤسسة واتخاذ قرار الاعتماد</p>
            </div>
        </div>

        <div>
            <a href="{{ route('super-admin.organizations.index', ['status' => 'all']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 transition-colors inline-flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    {{-- تقسيم الصفحة إلى شقين (Two-Column Layout) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- العمود الأيمن (العريض): يحتوي على بيانات المؤسسة والمالك --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- هنا تضع كود التنبيه للمؤسسة المحذوفة تماماً --}}
            @if ($organization->trashed())
                <div
                    class="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-xs font-semibold flex items-center gap-2 shadow-sm animate-pulse">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>تنبيه أمني: هذه المؤسسة مرفوضة.</span>
                </div>
            @endif
            {{-- بطاقة بيانات المؤسسة --}}
            <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
                <div
                    class="px-4 py-3 border-b border-slate-200 bg-slate-50/50 flex items-center gap-2 text-slate-700 font-semibold text-xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    بيانات المؤسسة الأساسية
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">اسم المؤسسة</span>
                        <span class="text-slate-900 font-semibold text-sm">{{ $organization->name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">نوع المؤسسة</span>
                        <span class="text-slate-700 font-medium">{{ $organization->organization_type ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">المنطقة / المحافظة</span>
                        <span class="text-slate-700 font-medium">{{ $organization->region ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">عدد الموظفين المتوقع</span>
                        {{-- <span class="text-slate-700 font-medium">{{ $organization->users()->whereHas('roles', fn($q) => $q->where('name', 'employee'))->count() ?? '---' }}</span> --}}
                        <span
                            class="text-slate-700 font-medium">{{ $organization->users->filter(fn($user) => $user->isEmployee())->count() ?? '---' }}</span>
                    </div>
                    <div class="md:col-span-2 border-t border-slate-100 pt-3 mt-1">
                        <span class="block text-slate-400 font-medium mb-1">وصف المؤسسة</span>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line text-[13px]">
                            {{ $organization->description ?? 'لا يوجد وصف مضاف للمؤسسة.' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- بطاقة بيانات المالك وقنوات التواصل --}}
            <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
                <div
                    class="px-4 py-3 border-b border-slate-200 bg-slate-50/50 flex items-center gap-2 text-slate-700 font-semibold text-xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    بيانات المالك والتواصل
                </div>
                @php
                    $owner = $organization->users?->first(fn($user) => $user->isOrgAdmin());
                @endphp
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">اسم المالك (المسؤول الرئيسي)</span>
                        <span class="text-slate-900 font-semibold">{{ $owner?->name ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium mb-1">البريد الإلكتروني الشخصي للمالك</span>
                        <span class="text-slate-700 font-medium">{{ $owner?->email ?? '---' }}</span>
                    </div>
                    <div
                        class="md:col-span-2 border-t border-slate-100 pt-3 mt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-slate-400 font-medium mb-1">البريد الإلكتروني للمؤسسة</span>
                            <span class="text-slate-700 font-medium">{{ $organization->email }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-medium mb-1">رقم الهاتف</span>
                            <span
                                class="text-slate-700 font-medium dir-ltr inline-block text-right">{{ $organization->phone ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- العمود الأيسر (الجانبي): حالة الطلب، التفعيل، والإجراءات الفورية --}}
        <div class="space-y-6">

            {{-- بطاقة حالة التفعيل (is_active) --}}
            <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
                <div
                    class="px-4 py-3 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between text-slate-700 font-semibold text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        حالة حساب المالك والموظفين
                    </div>

                    {{-- حالة الحساب --}}
                    @if ($organization->isSuspended())
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">مُجمدة
                            / موقوفة</span>
                    @else
                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-bold">نشطة
                            حالياً</span>
                    @endif
                </div>

                <div class="p-4">
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                        التحكم في صلاحية دخول المالك والموظفين التابعين له إلى لوحة التحكم الخاصة بهم.
                    </p>

                    @if ($owner)
                        <form method="POST"
                            action="{{ route('super-admin.organizations.toggleActive', $organization) }}"
                            id="toggle-active-form">
                            @csrf
                            @method('PATCH')

                            @if ($organization->isSuspended())
                                <button type="submit"
                                    class="toggle-active-btn w-full text-center px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded text-xs font-semibold transition-colors shadow-sm block"
                                    data-status="activate">
                                    إلغاء تجميد المؤسسة
                                </button>
                            @else
                                <button type="submit"
                                    class="toggle-active-btn w-full text-center px-4 py-2 bg-slate-800 text-white hover:bg-slate-900 rounded text-xs font-semibold transition-colors shadow-sm block"
                                    data-status="deactivate">
                                    تجميد المؤسسة كاملة
                                </button>
                            @endif
                        </form>
                    @else
                        <span class="text-xs text-red-500 font-medium block text-center py-2">لا يوجد مالك مرتبط بهذه
                            المنظمة حالياً لاتخاذ إجراء.</span>
                    @endif
                </div>
            </div>

            {{-- بطاقة حالة الطلب والقرار (الاعتماد) --}}
            <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
                <div
                    class="px-4 py-3 border-b border-slate-200 bg-slate-50/50 flex items-center gap-2 text-slate-700 font-semibold text-xs">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    حالة الطلب والقرار
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <span class="block text-slate-400 text-xs font-medium mb-2">تاريخ تقديم الطلب:</span>
                        <span class="text-xs text-slate-600 font-semibold block mb-3">
                            {{ $organization->created_at->format('Y-m-d - h:i A') }}
                        </span>

                        <span class="block text-slate-400 text-xs font-medium mb-1">الحالة الحالية للطلب:</span>
                        <div class="mt-1.5">
                            @if ($organization->status === 'approved')
                                <span
                                    class="w-full text-center block px-3 py-2 rounded text-xs font-bold border bg-green-50 text-green-700 border-green-200 shadow-sm">
                                    مقبولة ومعتمدة بالنظام
                                </span>
                            @elseif ($organization->status === 'rejected')
                                <span
                                    class="w-full text-center block px-3 py-2 rounded text-xs font-bold border bg-red-50 text-red-700 border-red-200 shadow-sm">
                                    مرفوضة نهائياً
                                </span>
                            @elseif ($organization->status === 'needs_changes')
                                <span
                                    class="w-full text-center block px-3 py-2 rounded text-xs font-bold border bg-orange-50 text-orange-700 border-orange-200 shadow-sm">
                                    بانتظار تعديل من قِبل المالك
                                </span>
                            @elseif ($organization->status === 'incomplete')
                                <span
                                    class="w-full text-center block px-3 py-2 rounded text-xs font-bold border bg-yellow-50 text-yellow-700 border-yellow-200 shadow-sm">
                                    بيانات التسجيل ناقصة
                                </span>
                            @else
                                <span
                                    class="w-full text-center block px-3 py-2 rounded text-xs font-bold border bg-amber-50 text-amber-700 border-amber-200 shadow-sm animate-pulse">
                                    قيد الانتظار والمراجعة
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- تظهر خيارات الاعتماد فقط إذا كان الطلب تحت المراجعة --}}
                    @if ($organization->status === 'pending_review')
                        <div class="border-t border-slate-100 pt-4 space-y-2">
                            <span class="block text-slate-500 text-xs font-bold mb-2">اتخاذ قرار بشأن المؤسسة:</span>

                            <form method="POST"
                                action="{{ route('super-admin.organizations.approve', $organization) }}"
                                class="approve-form w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full text-center px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded text-xs font-semibold transition-colors shadow-sm block">
                                    موافقة واعتماد الطلب
                                </button>
                            </form>

                            <button type="button"
                                class="request-changes-btn w-full text-center px-4 py-2 bg-orange-500 text-white hover:bg-orange-600 rounded text-xs font-semibold transition-colors shadow-sm"
                                data-action="{{ route('super-admin.organizations.requestChanges', $organization) }}">
                                طلب تعديلات على البيانات
                            </button>

                            <button type="button"
                                class="reject-btn w-full text-center px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded text-xs font-semibold transition-colors shadow-sm"
                                data-action="{{ route('super-admin.organizations.reject', $organization) }}">
                                رفض الطلب نهائياً
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Forms المخفية للتوافق والتعامل مع جافاسكريبت والـ SweetAlert2 تماماً كالفهرس --}}
    <form id="reject-form" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="rejection_reason" id="rejection_reason_input">
    </form>

    <form id="request-changes-form" method="POST" class="hidden">
        @csrf
        <div id="review-notes-inputs"></div>
    </form>

    {{-- السكريبتات متطابقة تماماً مع الـ Index لتعمل الأزرار بنفس الاحترافية --}}
    <script>
        // تأكيد تغيير حالة التفعيل (is_active)
        document.getElementById('toggle-active-form')?.addEventListener('submit', function(event) {
            event.preventDefault();
            const button = this.querySelector('.toggle-active-btn');
            const isDeactivate = button.dataset.status === 'deactivate';

            Swal.fire({
                title: isDeactivate ? 'تجميد الحساب؟' : 'تفعيل الحساب؟',
                text: isDeactivate ?
                    'هل أنت متأكد من تعطيل حساب المؤسسة؟ لن يتمكن الموظفون والمسؤول من دخول النظام.' :
                    'هل تريد إعادة تفعيل حساب المؤسسة لتمكينهم من العمل مجدداً؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isDeactivate ? '#1e293b' : '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isDeactivate ? 'نعم، قم بالتجميد' : 'نعم، تفعيل الآن',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
        // تأكيد الموافقة
        document.addEventListener('submit', (event) => {
            if (!event.target.matches('.approve-form')) return;
            event.preventDefault();
            Swal.fire({
                title: 'تأكيد الموافقة',
                text: 'هل أنت متأكد من الموافقة على هذه المؤسسة؟ سيتم فتح كافة الصلاحيات لها.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، موافقة',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) event.target.submit();
            });
        });

        // زر الرفض النهائي
        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.reject-btn');
            if (!btn) return;

            Swal.fire({
                title: 'سبب الرفض النهائي',
                html: '<p class="text-xs text-red-600 mb-2">تنبيه: الرفض النهائي سيحذف بيانات المؤسسة والمستخدم المرتبط بها.</p>',
                input: 'textarea',
                inputPlaceholder: 'اكتب سبب رفض الطلب بشكل نهائي...',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'تأكيد الرفض',
                cancelButtonText: 'إلغاء',
                inputValidator: (value) => {
                    if (!value || value.trim().length === 0) {
                        return 'سبب الرفض مطلوب';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('reject-form');
                    form.action = btn.dataset.action;
                    document.getElementById('rejection_reason_input').value = result.value;
                    form.submit();
                }
            });
        });

        // زر طلب تعديل
        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.request-changes-btn');
            if (!btn) return;

            const fields = [{
                    key: 'organization_name',
                    label: 'اسم المؤسسة'
                },
                {
                    key: 'admin_name',
                    label: 'اسم المسؤول'
                },
                {
                    key: 'organization_region',
                    label: 'المنطقة'
                },
                {
                    key: 'organization_type',
                    label: 'نوع المؤسسة'
                },
            ];

            const html = `
                <div class="text-right space-y-3">
                    ${fields.map(f => `
                                                        <div class="border rounded-md p-2">
                                                            <label class="flex items-center gap-2 text-sm font-medium cursor-pointer">
                                                                <input type="checkbox" class="field-checkbox" data-field="${f.key}">
                                                                ${f.label}
                                                            </label>
                                                            <textarea
                                                                class="field-note hidden w-full mt-2 border rounded p-2 text-sm"
                                                                data-field="${f.key}"
                                                                placeholder="اكتب ملاحظتك على ${f.label}..."></textarea>
                                                        </div>
                                                    `).join('')}
                </div>
            `;

            Swal.fire({
                title: 'تحديد الحقول المطلوب تعديلها',
                html: html,
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'إرسال طلب التعديل',
                cancelButtonText: 'إلغاء',
                didOpen: () => {
                    document.querySelectorAll('.field-checkbox').forEach((checkbox) => {
                        checkbox.addEventListener('change', (e) => {
                            const textarea = document.querySelector(
                                `.field-note[data-field="${e.target.dataset.field}"]`
                            );
                            textarea.classList.toggle('hidden', !e.target.checked);
                        });
                    });
                },
                preConfirm: () => {
                    const notes = {};
                    document.querySelectorAll('.field-checkbox:checked').forEach((checkbox) => {
                        const field = checkbox.dataset.field;
                        const note = document.querySelector(
                            `.field-note[data-field="${field}"]`).value.trim();

                        if (!note) {
                            Swal.showValidationMessage(`يرجى كتابة ملاحظة لكل حقل محدد`);
                            throw new Error('missing note');
                        }
                        notes[field] = note;
                    });

                    if (Object.keys(notes).length === 0) {
                        Swal.showValidationMessage('يرجى تحديد حقل واحد على الأقل');
                        throw new Error('no fields selected');
                    }

                    return notes;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('request-changes-form');
                    form.action = btn.dataset.action;

                    const container = document.getElementById('review-notes-inputs');
                    container.innerHTML = '';

                    Object.entries(result.value).forEach(([field, note]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `review_notes[${field}]`;
                        input.value = note;
                        container.appendChild(input);
                    });

                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
