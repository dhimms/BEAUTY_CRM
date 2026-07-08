<header class="sticky top-0 z-30 bg-cream/80 backdrop-blur-xl border-b border-charcoal-200/50">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

        {{-- Left: Mobile menu + Search --}}
        <div class="flex items-center gap-4">
            {{-- Mobile hamburger --}}
            <button @click="sidebarMobile = !sidebarMobile" class="lg:hidden text-charcoal-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Search --}}
            <div class="hidden sm:block relative">
                <svg class="w-4 h-4 text-charcoal-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Search..."
                    class="pl-10 pr-4 py-2 w-64 bg-white border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-300 transition-all">
            </div>
        </div>

        {{-- Right: Notifications + Profile --}}
        <div class="flex items-center gap-3">

            {{-- Notifications --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="relative p-2 text-charcoal-500 hover:text-charcoal-700 hover:bg-white rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" @click.outside="open = false" x-cloak
                    class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-charcoal-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-charcoal-100 flex items-center justify-between">
                        <h3 class="font-semibold text-sm text-charcoal-800">Notifications</h3>
                        <button class="text-xs text-rose-500 hover:text-rose-700">Mark all read</button>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <div class="px-4 py-3 hover:bg-charcoal-50 transition-colors border-l-3 border-rose-500">
                            <p class="text-sm font-medium text-charcoal-800">New lead assigned</p>
                            <p class="text-xs text-charcoal-500 mt-0.5">2 minutes ago</p>
                        </div>
                        <div class="px-4 py-3 hover:bg-charcoal-50 transition-colors">
                            <p class="text-sm font-medium text-charcoal-800">Follow-up reminder</p>
                            <p class="text-xs text-charcoal-500 mt-0.5">15 minutes ago</p>
                        </div>
                    </div>
                    <div class="px-4 py-2.5 border-t border-charcoal-100 text-center">
                        <a href="#" class="text-xs text-rose-500 hover:text-rose-700 font-medium">View All</a>
                    </div>
                </div>
            </div>

            {{-- User Profile --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-3 hover:bg-white px-3 py-2 rounded-xl transition-all">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
                        class="w-8 h-8 rounded-full object-cover ring-2 ring-charcoal-200">
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-charcoal-800 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-mono text-charcoal-400 uppercase tracking-wider">
                            {{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>
                    <svg class="w-4 h-4 text-charcoal-400 hidden sm:block" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-charcoal-200 overflow-hidden">
                    <a href="#"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-charcoal-700 hover:bg-charcoal-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Profile
                    </a>
                    <div class="border-t border-charcoal-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>