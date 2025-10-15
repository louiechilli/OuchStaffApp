{{-- components/pageHeader.blade.php --}}

@push('styles')
<style>
    .page-header {
        margin-bottom: 48px;
        animation: slideDown 0.4s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 12px;
        background: white;
        border: 2px solid #e2e8f0;
        color: #334155;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.2s ease;
        cursor: pointer;
        touch-action: manipulation;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        min-height: 48px;
    }
    
    .back-button:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateX(-4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .back-button:active {
        transform: translateX(-2px) scale(0.98);
    }
    
    .back-button svg {
        transition: transform 0.2s ease;
    }
    
    .back-button:hover svg {
        transform: translateX(-2px);
    }
    
    @media (min-width: 768px) {
        .back-button {
            padding: 14px 28px;
            font-size: 16px;
            min-height: 52px;
        }
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin: 0;
    }
    
    .page-subtitle {
        font-size: 1.125rem;
        color: #64748b;
        line-height: 1.6;
        margin-top: 12px;
    }
    
    @media (min-width: 768px) {
        .page-title {
            font-size: 2.5rem;
        }
        
        .page-subtitle {
            font-size: 1.25rem;
            margin-top: 16px;
        }
    }
    
    @media (min-width: 1024px) {
        .page-title {
            font-size: 3rem;
        }
    }
    
    /* Breadcrumb alternative styling */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    
    .breadcrumb-item {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    
    .breadcrumb-item:hover {
        color: #0d9488;
    }
    
    .breadcrumb-separator {
        color: #cbd5e1;
        font-size: 12px;
    }
    
    .breadcrumb-current {
        color: #0f172a;
        font-weight: 600;
    }
</style>
@endpush

<div class="page-header">
    <div class="flex flex-col gap-6">
        <!-- Back Button -->
        @if(isset($action, $actionUrl))
            <div>
                <a href="{{ $actionUrl }}" class="back-button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ $action }}</span>
                </a>
            </div>
        @endif
        
        <!-- Title Section -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
            <div class="flex-1">
                <!-- Optional Breadcrumb -->
                @if(isset($breadcrumbs) && is_array($breadcrumbs))
                    <nav class="breadcrumb">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if($loop->last)
                                <span class="breadcrumb-item breadcrumb-current">{{ $crumb['label'] }}</span>
                            @else
                                <a href="{{ $crumb['url'] }}" class="breadcrumb-item">{{ $crumb['label'] }}</a>
                                <span class="breadcrumb-separator">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                            @endif
                        @endforeach
                    </nav>
                @endif
                
                <h1 class="page-title">{{ $title ?? '' }}</h1>
                
                @if(isset($subtitle))
                    <p class="page-subtitle">{{ $subtitle }}</p>
                @endif
                
                <!-- Optional metadata badges -->
                @if(isset($badges) && is_array($badges))
                    <div class="flex items-center gap-3 mt-4 flex-wrap">
                        @foreach($badges as $badge)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold
                                {{ $badge['color'] ?? 'bg-slate-100 text-slate-700' }}">
                                @if(isset($badge['icon']))
                                    <i class="{{ $badge['icon'] }} text-xs"></i>
                                @endif
                                {{ $badge['label'] }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Optional action buttons on the right -->
            @if(isset($actions) && is_array($actions))
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                    @foreach($actions as $actionBtn)
                        <a href="{{ $actionBtn['url'] }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold text-sm transition-all touch-manipulation
                                  {{ $actionBtn['primary'] ?? false 
                                     ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white hover:from-teal-600 hover:to-teal-700 shadow-lg shadow-teal-500/30' 
                                     : 'bg-white text-slate-700 border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}
                                  min-h-[48px] whitespace-nowrap">
                            @if(isset($actionBtn['icon']))
                                <i class="{{ $actionBtn['icon'] }}"></i>
                            @endif
                            {{ $actionBtn['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <!-- Optional divider -->
    @if(isset($showDivider) && $showDivider)
        <hr class="mt-8 border-slate-200">
    @endif
</div>