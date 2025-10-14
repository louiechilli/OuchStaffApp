{{-- resources/views/pages/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Artist Dashboard')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10">
            @include('components.dashboard.header')

            @include('components.dashboard.kpi-cards')

            @include('components.dashboard.quick-actions')

            {{-- Two-column content: Upcoming / Messages / Inventory Alerts --}}
            <div class="mt-10 grid grid-cols-1 xl:grid-cols-3 gap-6">

                @include('components.dashboard.all-bookings')

                {{-- Right rail --}}
                <div class="space-y-6">

                    @include('components.dashboard.recent-messages')

                    @include('components.dashboard.inventory-alerts')
                </div>
            </div>
        </div>
    </div>
@endsection
