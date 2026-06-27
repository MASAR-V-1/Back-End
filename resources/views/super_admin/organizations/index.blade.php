<x-app-layout>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-4">
        <div class="flex items-center gap-2.5">
            <div class="p-2 bg-white rounded-lg shadow-sm border border-slate-200 text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900">إدارة طلبات المؤسسات</h1>
                <p class="text-xs text-slate-500">مراجعة واعتماد الحسابات المتقدمة للنظام</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
            <a href="{{ route('super-admin.organizations.index', ['status' => 'all']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $status === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                الكل
            </a>

            <a href="{{ route('super-admin.organizations.index', ['status' => 'pending']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                قيد الانتظار
            </a>

            <a href="{{ route('super-admin.organizations.index', ['status' => 'needs_changes']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $status === 'needs_changes' ? 'bg-orange-500 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                تحتاج تعديل
            </a>

            <a href="{{ route('super-admin.organizations.index', ['status' => 'approved']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                المقبولة
            </a>

            <a href="{{ route('super-admin.organizations.index', ['status' => 'rejected']) }}"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                المرفوضة
            </a>
        </div>
    </div>

    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2 text-slate-700 font-semibold text-xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                قائمة البيانات الحالية
                ({{ ['pending' => 'قيد الانتظار', 'needs_changes' => 'تحتاج تعديل', 'approved' => 'المقبولة', 'rejected' => 'المرفوضة', 'all' => 'الكل'][$status] ?? $status }})
            </div>

            <button onclick="window.location.reload();"
                class="p-1 text-slate-400 hover:text-slate-600 rounded transition-colors" title="تحديث البيانات">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.213 6H16v5" />
                </svg>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse whitespace-nowrap">
                <thead>
                    <tr
                        class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="px-4 py-3">اسم المؤسسة</th>
                        <th class="px-4 py-3">البريد الإلكتروني</th>
                        <th class="px-4 py-3">نوع المؤسسة</th>
                        <th class="px-4 py-3">المنطقة</th>
                        <th class="px-4 py-3">اسم المالك</th>
                        <th class="px-4 py-3">الهاتف</th>
                        <th class="px-4 py-3">تاريخ التقديم</th>
                        <th class="px-4 py-3">تاريخ التحديث</th>
                        <th class="px-4 py-3 text-center">الإجراءات والعمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($organizations as $org)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $org->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $org->email }}</td>
                            <td class="px-4 py-3 dir-ltr text-right text-slate-500">
                                {{ $org->organization_type ?? '---' }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $org->region ?? '---' }}</td>
                            <td class="px-4 py-3 text-slate-400">
                                {{ optional($org->users?->first(fn($user) => $user->hasRole('org_admin')))->name ?? '---' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $org->phone ?? '---' }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $org->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $org->updated_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    @if ($org->status === 'pending_review')
                                        <form method="POST"
                                            action="{{ route('super-admin.organizations.approve', $org) }}"
                                            class="approve-form inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1 bg-blue-600 text-white hover:bg-blue-700 rounded text-[11px] font-medium transition-colors shadow-sm">
                                                موافقة
                                            </button>
                                        </form>

                                        <button type="button"
                                            class="request-changes-btn px-2.5 py-1 bg-orange-500 text-white hover:bg-orange-600 rounded text-[11px] font-medium transition-colors shadow-sm"
                                            data-action="{{ route('super-admin.organizations.requestChanges', $org) }}">
                                            طلب تعديل
                                        </button>

                                        <button type="button"
                                            class="reject-btn px-2.5 py-1 bg-red-500 text-white hover:bg-red-600 rounded text-[11px] font-medium transition-colors shadow-sm"
                                            data-action="{{ route('super-admin.organizations.reject', $org) }}">
                                            رفض
                                        </button>
                                    @elseif ($org->status === 'needs_changes')
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold border bg-orange-50 text-orange-700 border-orange-200">
                                            بانتظار تعديل org_admin
                                        </span>
                                    @elseif ($org->status === 'incomplete')
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold border bg-yellow-50 text-yellow-700 border-yellow-200">
                                            بيانات ناقصة
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $org->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $org->status === 'approved' ? 'مقبولة' : 'مرفوضة' }}
                                        </span>
                                    @endif

                                    <a href="{{ route('super-admin.organizations.show', $org) }}"
                                        class="px-2.5 py-1 bg-slate-600 text-white hover:bg-slate-700 rounded text-[11px] font-medium transition-colors shadow-sm inline-block">
                                        عرض
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-4 py-12 text-center text-slate-400 bg-slate-50/20">
                                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H13m-3.586-2.414a1 1 0 00-.707-.293H4" />
                                </svg>
                                <span class="text-xs font-medium">لا توجد سجلات متاحة في هذه القائمة حالياً.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($organizations->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $organizations->links() }}
            </div>
        @endif

    </div>

    {{-- Hidden forms - يتم تعبيتهم وإرسالهم عبر JS --}}
    <form id="reject-form" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="rejection_reason" id="rejection_reason_input">
    </form>

    <form id="request-changes-form" method="POST" class="hidden">
        @csrf
        <div id="review-notes-inputs"></div>
    </form>

    <script>
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

        // زر الرفض - SweetAlert فيه textarea للسبب
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

        // زر طلب تعديل - SweetAlert فيه checkboxes لكل حقل رئيسي + textarea تظهر عند التحديد
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
