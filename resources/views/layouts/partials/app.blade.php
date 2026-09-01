<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('beauty-crm.company_name') }} CRM</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@300;400;500&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js & Plugins --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- Quill Rich Text Editor --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #FAF7F2;
        }

        /* Custom Quill Styling */
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #E5E7EB !important;
            background-color: #FAFAFA;
            font-family: 'DM Sans', sans-serif;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #E5E7EB !important;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            min-height: 140px;
            background-color: #ffffff;
        }
        .ql-editor {
            min-height: 140px;
            color: #1F2937;
            line-height: 1.6;
        }
        .ql-editor.ql-blank::before {
            color: #9CA3AF;
            font-style: normal;
        }

        .font-serif {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }

        .font-mono {
            font-family: 'DM Mono', monospace;
        }

        /* Sidebar transitions */
        .sidebar-transition {
            transition: width 0.25s ease, transform 0.25s ease;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #D1D1D1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #B4B4B4;
        }
    </style>
</head>

<body class="h-full text-charcoal-800 antialiased" 
      x-data="{ sidebarOpen: true, sidebarMobile: false, isMobile: window.innerWidth < 1024 }" 
      @resize.window="isMobile = window.innerWidth < 1024; if(!isMobile) sidebarMobile = false">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarMobile" x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40 z-40 lg:hidden"
        @click="sidebarMobile = false" x-cloak>
    </div>

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main Content --}}
    <div class="lg:pl-64 min-h-full flex flex-col sidebar-transition" :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-20'">

        {{-- Header --}}
        @include('layouts.partials.header')

        {{-- Page Content --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            {{-- Breadcrumb --}}
            @hasSection('breadcrumb')
                <nav class="mb-6" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-charcoal-500">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            {{-- Page Header --}}
            @hasSection('page-header')
                <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="font-serif text-2xl lg:text-3xl font-semibold text-charcoal-900">
                            @yield('page-header')
                        </h1>
                        @hasSection('page-subtitle')
                            <p class="text-charcoal-500 text-sm mt-1">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('page-actions')
                    </div>
                </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-rose-400 hover:text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Content --}}
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer
            class="px-6 py-4 text-center text-xs text-charcoal-400 font-mono border-t border-charcoal-200/50 mt-auto">
            &copy; {{ date('Y') }} {{ config('beauty-crm.company_name') }} — BeautyCRM
            v{{ config('beauty-crm.version') }} by TEAM TEKNO
        </footer>
    </div>

    <script>
        window.initQuillEditor = function(element, placeholderText) {
            if (!element) return null;
            if (element.__quill) return element.__quill;

            function setup() {
                if (!element) return null;
                if (element.__quill) return element.__quill;
                if (typeof Quill === 'undefined') return null;

                try {
                    const prev = element.previousElementSibling;
                    if (prev && prev.classList.contains('ql-toolbar')) {
                        prev.remove();
                    }
                    const quill = new Quill(element, {
                        theme: 'snow',
                        placeholder: placeholderText || 'Tulis pesan di sini...',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'color': [] }, { 'background': [] }],
                                ['clean']
                            ]
                        }
                    });
                    element.__quill = quill;
                    return quill;
                } catch (err) {
                    console.error('Error initializing Quill:', err);
                    return null;
                }
            }

            if (typeof Quill !== 'undefined') {
                return setup();
            } else {
                let count = 0;
                const timer = setInterval(() => {
                    count++;
                    if (typeof Quill !== 'undefined') {
                        clearInterval(timer);
                        setup();
                    } else if (count > 50) {
                        clearInterval(timer);
                    }
                }, 50);
            }
        };
    </script>

    @stack('scripts')
</body>

</html>