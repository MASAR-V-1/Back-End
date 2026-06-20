<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('طلبات المؤسسات المعلقة') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="relative overflow-hidden bg-white shadow-sm rounded-xl border border-gray-200">
                        <div class="overflow-x-auto">
                            <div class="mb-4 flex gap-2 border-b border-gray-200">
                                @php
                                    $tabs = [
                                        'pending' => 'قيد المراجعة',
                                        'approved' => 'موافق عليها',
                                        'rejected' => 'مرفوضة',
                                        'all' => 'الكل',
                                    ];
                                @endphp

                                @foreach ($tabs as $key => $label)
                                    <a href="{{ route('super-admin.organizations.index', ['status' => $key]) }}"
                                        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors duration-150
                {{ $status === $key
                    ? 'border-indigo-600 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                            <table class="w-full text-sm text-right text-gray-600 antialiased">
                                <thead
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-500 bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-center w-20">{{ __('ID') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center">{{ __('اسم المؤسسة') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center">{{ __('إيميل المؤسسة') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center">{{ __('صاحب الحساب') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center">{{ __('الحالة') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center">{{ __('تاريخ التسجيل') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center w-56">{{ __('إجراءات') }}</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse ($organizations as $organization)
                                        <tr class="hover:bg-gray-50/70 transition-colors duration-200">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-400 text-xs">
                                                {{ '#' }}{{ $organization->id }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-900">
                                                {{ $organization->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">
                                                {{ $organization->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">
                                                {{ $organization->users->first()?->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $statusLabels = [
                                                        'pending' => ['قيد المراجعة', 'bg-yellow-50 text-yellow-600'],
                                                        'approved' => ['موافق عليها', 'bg-green-50 text-green-600'],
                                                        'rejected' => ['مرفوضة', 'bg-red-50 text-red-600'],
                                                    ];
                                                    [$label, $classes] = $statusLabels[$organization->status] ?? [
                                                        '-',
                                                        'bg-gray-50 text-gray-500',
                                                    ];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $classes }}">
                                                    {{ $label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-xs text-gray-500">
                                                {{ $organization->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                @if ($organization->status === 'pending' && $organization->users->whereNotNull('email_verified_at')->isNotEmpty())
                                                    <div class="flex items-center justify-center gap-x-1.5">
                                                        <form
                                                            action="{{ route('super-admin.organizations.approve', $organization) }}"
                                                            method="POST" class="inline approve-form">
                                                            @csrf
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-md transition-colors duration-150 text-xs font-semibold">
                                                                {{ __('موافقة') }}
                                                            </button>
                                                        </form>

                                                        <button type="button"
                                                            class="reject-btn inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-colors duration-150 text-xs font-semibold"
                                                            data-action="{{ route('super-admin.organizations.reject', $organization) }}">
                                                            {{ __('رفض') }}
                                                        </button>
                                                    </div>
                                                @elseif ($organization->status === 'pending' && $organization->users->whereNull('email_verified_at')->isNotEmpty())
                                                    <span class="text-gray-400 text-xs">لم يتم تفعيل حساب صاحب المؤسسة
                                                        بعد</span>
                                                @elseif ($organization->status === 'approved')
                                                    <span class="text-green-600 text-xs" lang="ar" dir="rtl"
                                                        title="تمت الموافقة بواسطة {{ $organization->approver->name }} الساعة {{ $organization->approved_at->format('H:i:s') }}">تمت
                                                        الموافقة بواسطة {{ $organization->approver->name }} منذ
                                                        {{ $organization->approved_at->diffForHumans() }}</span>
                                                @elseif ($organization->status === 'rejected')
                                                    <span class="text-red-600 text-xs" lang="ar" dir="rtl"
                                                        title="{{ $organization->rejection_reason }}">{{ Str::words($organization->rejection_reason, 10) }}</span>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7"
                                                class="px-6 py-12 whitespace-nowrap text-sm font-medium text-center text-gray-400 bg-gray-50/50">
                                                <div class="flex flex-col items-center justify-center space-y-2">
                                                    <svg class="w-8 h-8 text-gray-300" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    <span>{{ __('لا يوجد طلبات معلقة حاليًا') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $organizations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form للرفض، نفس فورم بس نولّد action ديناميكي --}}
    <form id="reject-form" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="rejection_reason" id="rejection_reason_input">
    </form>

    <script>
        // تأكيد الموافقة
        document.addEventListener('submit', (event) => {
            if (!event.target.matches('.approve-form')) return;
            event.preventDefault();
            Swal.fire({
                title: 'تأكيد الموافقة',
                text: 'هل أنت متأكد من الموافقة على هذه المؤسسة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، موافقة',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });

        // زر الرفض - يطلب سبب الرفض عبر SweetAlert input
        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.reject-btn');
            if (!btn) return;

            const actionUrl = btn.dataset.action;

            Swal.fire({
                title: 'سبب الرفض',
                input: 'textarea',
                inputPlaceholder: 'اكتب سبب رفض الطلب...',
                inputAttributes: {
                    'aria-label': 'سبب الرفض'
                },
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
                    form.action = actionUrl;
                    document.getElementById('rejection_reason_input').value = result.value;
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
