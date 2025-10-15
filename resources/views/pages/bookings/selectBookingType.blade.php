{{-- resources/views/pages/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Booking')

@push('styles')
<style>
    /* Service card styles */
    .service-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 2px solid #e2e8f0;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        touch-action: manipulation;
        position: relative;
        overflow: hidden;
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .service-card:hover {
        border-color: #0d9488;
        background: #f0fdfa;
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(13, 148, 136, 0.12);
    }
    
    .service-card:hover::before {
        transform: scaleX(1);
    }
    
    .service-card:active {
        transform: translateY(-2px) scale(0.99);
    }
    
    .service-card .icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .service-card:hover .icon-wrapper {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        transform: rotate(5deg) scale(1.1);
    }
    
    .service-card .icon-wrapper i {
        color: #0d9488;
        font-size: 24px;
        transition: all 0.3s ease;
    }
    
    .service-card:hover .icon-wrapper i {
        color: white;
    }
    
    .service-card .arrow {
        transition: all 0.3s ease;
    }
    
    .service-card:hover .arrow {
        transform: translateX(4px);
        color: #0d9488;
    }
    
    /* Category section */
    .category-section {
        margin-bottom: 48px;
    }
    
    .category-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        animation: fadeIn 0.5s ease;
    }
    
    .empty-state-icon {
        width: 96px;
        height: 96px;
        margin: 0 auto 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Badge */
    .count-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        color: #0d9488;
    }
    
    /* Responsive grid */
    @media (min-width: 768px) {
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }
    
    @media (min-width: 1024px) {
        .services-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
    }
    
    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }
</style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="container mx-auto px-4 sm:px-6 md:px-8 lg:px-16 py-6 md:py-10">

            @include('components.pageHeader', [
                'title' => 'Create Booking',
                'subtitle' => 'Choose the type of service you want to book',
                'action' => 'Dashboard', 
                'actionUrl' => route('dashboard')
            ])

            <!-- Services by Category -->
            @forelse($serviceCategories as $category)
                @php
                    $items = $services->where('category_id', $category->id);
                @endphp

                @if($items->count())
                    <div class="category-section">
                        <div class="category-header">
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                                    {{ $category->name }}
                                </h2>
                                <span class="count-badge">
                                    {{ $items->count() }} {{ Str::plural('service', $items->count()) }}
                                </span>
                            </div>
                            @if($category->description)
                                <p class="text-slate-600 mt-2 text-base">{{ $category->description }}</p>
                            @endif
                        </div>

                        <div class="services-grid">
                            @foreach($items as $svc)
                                <a href="{{ route('bookings.create', ['service_id' => $svc->id]) }}" class="service-card block">
                                    <div class="flex items-start gap-4">
                                        <div class="icon-wrapper flex-shrink-0">
                                            <i class="fa-solid fa-calendar-check"></i>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2">
                                                {{ $svc->name }}
                                            </h3>
                                            
                                            @if($svc->description)
                                                <p class="text-slate-600 text-sm line-clamp-2 mb-3">
                                                    {{ $svc->description }}
                                                </p>
                                            @endif
                                            
                                            <div class="flex items-center gap-4 text-sm text-slate-500">
                                                @if($svc->duration)
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="fa-solid fa-clock text-xs"></i>
                                                        {{ $svc->duration }} min
                                                    </span>
                                                @endif
                                                @if($svc->price)
                                                    <span class="flex items-center gap-1.5 font-semibold text-teal-600">
                                                        <i class="fa-solid fa-euro-sign text-xs"></i>
                                                        {{ number_format($svc->price, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="arrow flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                                <i class="fa-solid fa-arrow-right text-slate-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-calendar-xmark text-slate-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">No Services Available</h3>
                    <p class="text-slate-600 text-lg max-w-md mx-auto">
                        There are currently no booking services available. Please check back later or contact support.
                    </p>
                </div>
            @endforelse

        </div>
    </div>
@endsection