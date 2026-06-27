<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - لوحة التحكم</title>

    <!-- Fonts: إضافة خطوط معاصرة تدعم المظهر الاحترافي للغتين -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=alexandria:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Alexandria', 'Figtree', sans-serif;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="antialiased bg-slate-100 text-slate-800 text-sm"
    x-data="{ sidebarOpen: false }">

    <!-- الحاوية الرئيسية للشاشة بالكامل لمنع الـ Scroll العشوائي وتثبيت الأقسام -->
    <div class="h-screen flex overflow-hidden">

        <!-- ========================================== -->
        <!-- 1. الـ SIDEBAR الجانبي الداكن (Admin One Style) -->
        <!-- ========================================== -->
        <aside style='border-left: 1px solid #00000036; background-color: #f1f5f9;'
            :class="sidebarOpen ? 'translate-x-0' : (document.documentElement.dir === 'rtl' ? 'translate-x-full' :
                '-translate-x-full')"
            class="fixed inset-y-0  {{ app()->getLocale() === 'ar' ? 'right-0 border-l' : 'left-0 border-r' }}  lg:bg-transparent  z-50 w-60 text-gray-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static flex flex-col justify-between flex-shrink-0 border-slate-800">

            <div>
                <!-- هيدر السايدبار (Logo) -->
                <div class="h-14 flex items-center px-6 border-b border-l border-gray-300 bg-white justify-between">
                    <div class="flex items-center gap-2">
                        <div class="font-black text-white text-sm tracking-wider uppercase">
                            <div>
                                <img width="100" height="200" src="{{ asset('img/logo/logoMasar.png') }}" alt="Masar Logo">
                            </div>
                        </div>
                    </div>
                    <!-- زر إغلاق السايدبار في شاشات الموبايل -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- قائمة الروابط والنقاط الفرعية للتحكم -->
                <nav class="p-3 space-y-1">
                    <p class="text-[10px] font-bold text-slate-500 px-3 uppercase tracking-wider mb-2">تصفح النظام</p>

                    <!-- رابط الرئيسية -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-800 text-white font-semibold shadow-sm' : 'hover:bg-green-800 hover:text-white' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                        لوحة التحكم الرئيسية
                    </a>

                    <!-- يمكنك هنا إضافة بقية روابط الـ Spatie Roles الخاصة بك بكل سهولة -->
                    @if (Auth::user() && method_exists(Auth::user(), 'isSuperAdmin') && Auth::user()->isSuperAdmin())
                        <p class="text-[10px] font-bold text-slate-500 px-3 uppercase tracking-wider pt-4 mb-2">الإدارة
                            العليا</p>
                        <a href="{{ route('super-admin.organizations.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-colors {{ request()->routeIs('super-admin.organizations.*') ? 'bg-green-800 text-white font-semibold' : 'hover:bg-green-800 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            طلبات المؤسسات
                        </a>
                    @endif
                </nav>
            </div>

            <!-- زر تسجيل الخروج الثابت أسفل السايدبار تماماً كالتصميم الأصلي -->
            <div class="p-3 border-t border-slate-800 bg-slate-950">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 text-xs font-medium text-white hover:bg-slate-800 hover:text-red-300 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <!-- خلفية داكنة شفافة تظهر للموبايل عند فتح القائمة -->
        <div @click="sidebarOpen = false" x-show="sidebarOpen" x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"></div>

        <!-- ========================================== -->
        <!-- 2. الجزء الأيمن/الأيسر: يشمل الـ Navbar والـ Main Content -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- شريط التنقل العلوي الثابت (Top Navbar) -->
            <header
                class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-12 flex-shrink-0 z-30 shadow-sm/50">
                <div class="flex items-center gap-4">
                    <!-- زر فتح السايدبار للموبايل -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden p-1.5 text-slate-500 hover:bg-slate-100 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- محرك بحث وهمي أو نص توضيحي خفيف لملء الفراغ مثل ديمو Admin One -->
                    <div class="text-xs text-slate-400 font-medium hidden sm:block">
                        <span>{{ __('البحث ذكي (Ctrl + K)') }}</span>
                    </div>
                </div>

                <!-- اليمين: حساب المستخدم المنسدل -->
                <div class="flex items-center gap-4">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2 p-1 hover:bg-slate-50 rounded transition-colors text-xs font-medium text-slate-700">
                            <div
                                class="w-7 h-7 bg-slate-800 text-white rounded-full flex items-center justify-center font-bold">
                                {{ mb_substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="hidden sm:inline">{{ Auth::user()->name ?? 'المستخدم' }}</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- قائمة منسدلة للملف الشخصي -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-1 w-44 bg-white border border-slate-200 rounded shadow-md py-1 z-50">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 hover:bg-slate-50 text-slate-600">{{ __('الملف الشخصي') }}</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ========================================== -->
            <!-- 3. منطقة المحتوى والـ Header الخاص بالصفحات (Main Content) -->
            <!-- ========================================== -->
            <div class="flex-1 overflow-y-auto bg-slate-50">

                <!-- وسم الـ Header المخصص لكل صفحة بأسلوب بريزي مع ترقية المظهر -->
                @isset($header)
                    <div class="p-6 pb-0">
                        <div class="max-w-7xl mx-auto">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <!-- الـ Content Slot الرئيسي الذي يحقن داخله كود الصفحات الفرعية -->
                <main class="p-6">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

            </div>

        </div>
    </div>

</body>

</html>
