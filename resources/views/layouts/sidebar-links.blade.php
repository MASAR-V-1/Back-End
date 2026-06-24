<a href="{{ route('dashboard') }}"
    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-all duration-200 group">
    <svg class="w-5 h-5 ml-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-500' }}"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    الرئيسية وملخص النظام
</a>

@role('super_admin')
    <div class="pt-5 pb-1">
        <p class="px-4 text-xxs font-bold text-slate-500 uppercase tracking-wider">إدارة النظام العليا</p>
    </div>
    <a href="{{ route('super-admin.organizations.index') }}"
        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('super-admin.organizations.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-all duration-200 group">
        <svg class="w-5 h-5 ml-3 {{ request()->routeIs('super-admin.organizations.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-500' }}"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        المؤسسات والطلبات
    </a>
@endrole

@role('org_admin')
    <div class="pt-5 pb-1">
        <p class="px-4 text-xxs font-bold text-slate-500 uppercase tracking-wider">إدارة المؤسسة</p>
    </div>
    <a href="/employees"
        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all duration-200 group">
        <svg class="w-5 h-5 ml-3 text-slate-400 group-hover:text-blue-500" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        إدارة الموظفين
    </a>
@endrole
