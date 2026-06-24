<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة التحكم الرئيسية') }}
        </h2>
    </x-slot>

    <div class="space-y-6">

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-6 flex items-center transition-all hover:shadow-md">
                <div class="p-3.5 rounded-xl bg-blue-50 text-blue-600 ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">المؤسسات المسجلة</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">48 مؤسسة</p>
                    <span class="text-xs text-green-600 font-semibold mt-1 flex items-center">
                        <span class="ml-1">↑ 12%</span> مقارنة بالشهر الماضي
                    </span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-6 flex items-center transition-all hover:shadow-md">
                <div class="p-3.5 rounded-xl bg-amber-50 text-amber-600 ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">طلبات قيد الانتظار</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">5 طلبات جيدة</p>
                    <span class="text-xs text-amber-600 font-medium mt-1 block">تحتاج مراجعة عاجلة</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-6 flex items-center transition-all hover:shadow-md">
                <div class="p-3.5 rounded-xl bg-purple-50 text-purple-600 ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">استقرار ومراقبة النظام</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">99.9%</p>
                    <span class="text-xs text-purple-600 font-medium mt-1 block">جميع خوادم النظام تعمل بكفاءة</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="bg-white p-6 shadow-sm rounded-2xl border border-gray-100 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">معدل الانضمام والنشاط الأسبوعي</h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg">محدث تلقائياً</span>
                </div>
                <div class="h-64 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                    <div class="text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                        سيتم هنا لاحقاً دمج الرسوم البيانية التفاعلية للمنصة
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm rounded-2xl border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">آخر العمليات المباشرة</h3>
                <div class="space-y-4">
                    <div class="flex items-start justify-between text-sm pb-3 border-b border-gray-100">
                        <div>
                            <p class="font-semibold text-gray-800">تسجيل مؤسسة "سبرنتس"</p>
                            <p class="text-xs text-gray-400 mt-0.5">تحقق الإيميل وبانتظار الموافقة</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">منذ 5 دقائق</span>
                    </div>
                    <div class="flex items-start justify-between text-sm pb-3 border-b border-gray-100">
                        <div>
                            <p class="font-semibold text-gray-800">الموافقة على "مستشفى الشفاء"</p>
                            <p class="text-xs text-gray-400 mt-0.5">بواسطة مدير النظام العليا</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">منذ ساعتين</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
