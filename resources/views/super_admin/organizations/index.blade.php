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
                ({{ ['pending' => 'قيد الانتظار', 'approved' => 'المقبولة', 'rejected' => 'المرفوضة'][$status] ?? $status }})
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
                        <th class="px-4 py-3">الهاتف</th>
                        <th class="px-4 py-3">تاريخ التقديم</th>
                        <th class="px-4 py-3 text-center">الإجراءات والعمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($organizations as $org)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $org->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $org->email }}</td>
                            <td class="px-4 py-3 dir-ltr text-right text-slate-500">{{ $org->phone ?? '---' }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $org->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($org->status === 'pending')
                                        <form method="POST"
                                            action="{{ route('super-admin.organizations.approve', $org) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1 bg-blue-600 text-white hover:bg-blue-700 rounded text-[11px] font-medium transition-colors shadow-sm">
                                                موافقة
                                            </button>
                                        </form>

                                        <form method="POST"
                                            action="{{ route('super-admin.organizations.reject', $org) }}" x-data
                                            class="inline-block">
                                            @csrf
                                            <input type="hidden" name="rejection_reason"
                                                value="البيانات المدخلة غير مستوفية للشروط الفنية المعتمدة لدينا.">
                                            <button type="submit"
                                                @click="if(!confirm('هل أنت متأكد من رفض هذه المؤسسة؟')){event.preventDefault()}"
                                                class="px-2.5 py-1 bg-red-500 text-white hover:bg-red-600 rounded text-[11px] font-medium transition-colors shadow-sm">
                                                رفض
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $org->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $org->status === 'approved' ? 'مقبولة' : 'مرفوضة' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400 bg-slate-50/20">
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
</x-app-layout>
