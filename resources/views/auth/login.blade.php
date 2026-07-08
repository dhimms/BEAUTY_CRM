<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — BeautyCRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        .font-serif {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }
    </style>
</head>

<body class="h-full bg-cream">
    <div class="min-h-full flex">
        {{-- Left: Hero --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-charcoal-900 items-center justify-center p-12 overflow-hidden">
            <div class="absolute inset-0"
                style="background: linear-gradient(135deg, #1A1A1A 0%, #2D1B2E 50%, #1A1A1A 100%);"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="relative z-10 max-w-md">
                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center mb-8">
                    <span class="text-white font-serif font-bold text-2xl">B</span>
                </div>
                <h1 class="font-serif text-5xl font-light text-white leading-tight mb-4">
                    Beauty<span class="text-rose-400">CRM</span>
                </h1>
                <p class="text-charcoal-400 text-lg leading-relaxed">
                    Customer Relationship Management platform designed for the beauty and skincare industry.
                </p>
                <div class="mt-12 flex items-center gap-4 text-charcoal-500 text-sm">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-rose-400/20 border-2 border-charcoal-800"></div>
                        <div class="w-8 h-8 rounded-full bg-blue-400/20 border-2 border-charcoal-800"></div>
                        <div class="w-8 h-8 rounded-full bg-emerald-400/20 border-2 border-charcoal-800"></div>
                    </div>
                    <span>Admin, Sales, CS & Manager roles</span>
                </div>
            </div>
        </div>

        {{-- Right: Login Form --}}
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                {{-- Mobile Logo --}}
                <div class="lg:hidden flex items-center gap-3 mb-10">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center">
                        <span class="text-white font-serif font-bold text-lg">B</span>
                    </div>
                    <span class="font-serif text-2xl font-semibold">BeautyCRM</span>
                </div>

                <h2 class="font-serif text-3xl font-semibold text-charcoal-900 mb-2">Welcome Back</h2>
                <p class="text-charcoal-500 text-sm mb-8">Sign in to your account to continue</p>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-charcoal-700 mb-1.5">Email</label>
                        <div class="relative">
                            <svg class="w-5 h-5 text-charcoal-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full pl-11 pr-4 py-3 bg-white border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all @error('email') border-rose-300 @enderror"
                                placeholder="you@example.com" required autofocus>
                        </div>
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password"
                            class="block text-sm font-medium text-charcoal-700 mb-1.5">Password</label>
                        <div class="relative">
                            <svg class="w-5 h-5 text-charcoal-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input :type="show ? 'text' : 'password'" id="password" name="password"
                                class="w-full pl-11 pr-12 py-3 bg-white border border-charcoal-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all"
                                placeholder="••••••••" required>
                            <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-charcoal-400 hover:text-charcoal-600">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-charcoal-300 text-rose-500 focus:ring-rose-500/20">
                            <span class="text-sm text-charcoal-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-rose-500 hover:text-rose-700 font-medium">Forgot password?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 rounded-xl transition-all hover:shadow-lg hover:shadow-rose-500/20 active:scale-[0.98]">
                        Sign In
                    </button>
                </form>

                <p class="text-center text-xs text-charcoal-400 mt-10 font-mono">
                    &copy; {{ date('Y') }} TEAM TEKNO — BeautyCRM v{{ config('beauty-crm.version') }}
                </p>
            </div>
        </div>
    </div>
</body>

</html>