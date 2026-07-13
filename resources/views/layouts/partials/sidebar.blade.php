<!-- Safelist: w-64 w-20 -->
<aside class="fixed inset-y-0 left-0 z-50 bg-charcoal-900 text-white sidebar-transition overflow-hidden flex flex-col transform"
    :class="{
        'w-64': sidebarOpen || sidebarMobile,
        'w-20': !sidebarOpen && !sidebarMobile
    }"
    :style="isMobile && !sidebarMobile ? 'transform: translateX(-100%)' : 'transform: translateX(0)'">

    {{-- Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-charcoal-700/50 flex-shrink-0">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 flex-shrink-0 bg-white rounded-lg shadow-sm p-1 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Beauty Clinic Logo" 
                    class="w-full h-full object-contain" 
                    onerror="this.src='https://ui-avatars.com/api/?name=Beauty+Clinic&background=F43F5E&color=fff'">
            </div>
            <span
                class="font-serif font-semibold text-lg tracking-tight whitespace-nowrap transition-opacity duration-200"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                Beauty Clinic
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">

        {{-- MAIN MENU --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-mono tracking-[0.2em] uppercase text-charcoal-500 whitespace-nowrap transition-opacity duration-200"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                Menu Utama
            </p>
            <div class="space-y-1">

                {{-- Dashboard --}}
                @php
                    $dashboardRoute = match (true) {
                        auth()->user()->isAdmin() => route('admin.dashboard'),
                        auth()->user()->isSales() => route('sales.dashboard'),
                        auth()->user()->isCS() => route('cs.dashboard'),
                        auth()->user()->isManager() => route('manager.dashboard'),
                        default => '#',
                    };
                @endphp
                <a href="{{ $dashboardRoute }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                          {{ request()->routeIs('*.dashboard') ? 'bg-rose-500/10 text-rose-400 border-l-3 border-rose-500' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                    <span class="whitespace-nowrap transition-opacity duration-200"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                        Dashboard
                    </span>
                </a>

                {{-- Leads --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isSales())
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.leads.index') : route('sales.leads.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('*leads*') ? 'bg-rose-500/10 text-rose-400 border-l-3 border-rose-500' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                            Leads
                        </span>
                    </a>
                @endif

                {{-- Pipeline / Deals --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isSales() || auth()->user()->isManager())
                    <a href="{{ auth()->user()->isSales() ? route('sales.deals.pipeline') : (auth()->user()->isAdmin() ? route('admin.deals.index') : route('manager.pipeline.index')) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('*deal*', '*pipeline*') ? 'bg-rose-500/10 text-rose-400 border-l-3 border-rose-500' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                            Pipeline
                        </span>
                    </a>
                @endif

                {{-- Customers --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isCS())
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.customers.index') : route('cs.customers.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('*customer*') ? 'bg-rose-500/10 text-rose-400 border-l-3 border-rose-500' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                            Customers
                        </span>
                    </a>
                @endif


            </div>
        </div>

        {{-- ADMIN SECTION --}}
        @if(auth()->user()->isAdmin())
            <div>
                <p class="px-3 mb-2 text-[10px] font-mono tracking-[0.2em] uppercase text-charcoal-500 whitespace-nowrap transition-opacity duration-200"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    Administrasi
                </p>
                <div class="space-y-1">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('admin.users*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Users</span>
                    </a>
                    <a href="{{ route('admin.lead-sources.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('admin.lead-sources*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Lead Sources</span>
                    </a>
                    <a href="{{ route('admin.pipeline-stages.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('admin.pipeline-stages*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pipeline Settings</span>
                    </a>
                    <a href="{{ route('admin.lost-reasons.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('admin.lost-reasons*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Lost Reasons</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- ANALYTICS --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <div>
                <p class="px-3 mb-2 text-[10px] font-mono tracking-[0.2em] uppercase text-charcoal-500 whitespace-nowrap transition-opacity duration-200"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    Analytics
                </p>
                <div class="space-y-1">
                    @if(auth()->user()->isManager())
                        <a href="{{ route('manager.reports.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                  {{ request()->routeIs('manager.reports*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="whitespace-nowrap transition-opacity duration-200"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Reports</span>
                        </a>
                    @endif
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.audit-logs.index') : route('manager.audit-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('*audit*') ? 'bg-rose-500/10 text-rose-400' : 'text-charcoal-300 hover:text-white hover:bg-charcoal-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Audit Log</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    {{-- Sidebar Toggle --}}
    <div class="px-3 py-3 border-t border-charcoal-700/50 flex-shrink-0">
        <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex items-center justify-center w-full py-2 rounded-xl text-charcoal-400 hover:text-white hover:bg-charcoal-800 transition-all">
            <svg class="w-5 h-5 transition-transform duration-200" :class="sidebarOpen ? '' : 'rotate-180'" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>
</aside>