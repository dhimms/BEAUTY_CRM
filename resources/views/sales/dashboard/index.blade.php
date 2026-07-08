<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard — BeautyCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FAF7F2] min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm max-w-md w-full p-8 text-center">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-3xl font-bold mx-auto mb-6">
            B
        </div>
        <h2 class="text-2xl font-bold text-stone-900 mb-2">Sales Dashboard</h2>
        <p class="text-stone-500 mb-6 text-sm">Dashboard untuk role Sales sedang dalam tahap pengembangan (Work in Progress).</p>
        
        <div class="space-y-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout & Kembali ke Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
