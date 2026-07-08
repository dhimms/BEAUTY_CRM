@extends('layouts.partials.app')

@section('title', 'Admin Dashboard')

@section('page-header', 'Dashboard')
@section('page-subtitle', 'Selamat datang di BeautyCRM Admin Panel')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Card: Total Users --}}
    <div class="bg-white rounded-2xl p-6 border border-charcoal-200/50 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-semibold text-charcoal-900">-</p>
        <p class="text-sm text-charcoal-500 mt-1">Total Users</p>
    </div>

    {{-- Card: Total Leads --}}
    <div class="bg-white rounded-2xl p-6 border border-charcoal-200/50 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-semibold text-charcoal-900">-</p>
        <p class="text-sm text-charcoal-500 mt-1">Total Leads</p>
    </div>

    {{-- Card: Total Deals --}}
    <div class="bg-white rounded-2xl p-6 border border-charcoal-200/50 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-semibold text-charcoal-900">-</p>
        <p class="text-sm text-charcoal-500 mt-1">Total Deals</p>
    </div>

    {{-- Card: Total Customers --}}
    <div class="bg-white rounded-2xl p-6 border border-charcoal-200/50 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-semibold text-charcoal-900">-</p>
        <p class="text-sm text-charcoal-500 mt-1">Total Customers</p>
    </div>
</div>

{{-- Placeholder Content --}}
<div class="bg-white rounded-2xl p-8 border border-charcoal-200/50 shadow-sm text-center">
    <svg class="w-16 h-16 text-charcoal-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <h3 class="font-serif text-xl text-charcoal-700 mb-2">Dashboard sedang dalam pengembangan</h3>
    <p class="text-charcoal-500 text-sm">Charts dan statistik akan segera ditampilkan di sini.</p>
</div>
@endsection
