{{-- KPI / Navigation Cards --}}

@push('styles')
<style>
    .kpi-card {
        position: relative;
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        cursor: pointer;
        touch-action: manipulation;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .kpi-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    }
    
    .kpi-card:active {
        transform: translateY(-1px) scale(0.99);
    }
    
    .kpi-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    
    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: var(--icon-bg);
    }
    
    .kpi-card:hover .kpi-icon-wrapper {
        transform: scale(1.05);
        background: var(--icon-bg-hover);
    }
    
    .kpi-icon-wrapper i {
        font-size: 20px;
        color: var(--icon-color);
        transition: all 0.2s ease;
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 8px;
    }
    
    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    
    .kpi-subtitle {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .kpi-subtitle strong {
        color: #475569;
        font-weight: 600;
    }
    
    /* Color schemes for different card types */
    .kpi-card.bookings {
        --icon-bg: #f0fdfa;
        --icon-bg-hover: #ccfbf1;
        --icon-color: #0d9488;
    }
    
    .kpi-card.clients {
        --icon-bg: #eff6ff;
        --icon-bg-hover: #dbeafe;
        --icon-color: #3b82f6;
    }
    
    .kpi-card.messages {
        --icon-bg: #f5f3ff;
        --icon-bg-hover: #ede9fe;
        --icon-color: #8b5cf6;
    }
    
    .kpi-card.inventory {
        --icon-bg: #fffbeb;
        --icon-bg-hover: #fef3c7;
        --icon-color: #f59e0b;
    }
    
    .kpi-card.revenue {
        --icon-bg: #f0fdf4;
        --icon-bg-hover: #dcfce7;
        --icon-color: #10b981;
    }
    
    .kpi-card.calendar {
        --icon-bg: #fdf2f8;
        --icon-bg-hover: #fce7f3;
        --icon-color: #ec4899;
    }
    
    /* Responsive grid */
    .kpi-grid {
        display: grid;
        gap: 20px;
        margin-top: 32px;
    }
    
    @media (min-width: 640px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
    }
    
    @media (min-width: 1280px) {
        .kpi-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    
    /* Animation on load */
    .kpi-card {
        animation: slideUp 0.5s ease backwards;
    }
    
    .kpi-card:nth-child(1) { animation-delay: 0.05s; }
    .kpi-card:nth-child(2) { animation-delay: 0.1s; }
    .kpi-card:nth-child(3) { animation-delay: 0.15s; }
    .kpi-card:nth-child(4) { animation-delay: 0.2s; }
    .kpi-card:nth-child(5) { animation-delay: 0.25s; }
    .kpi-card:nth-child(6) { animation-delay: 0.3s; }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

<div class="kpi-grid">
    @can('view-bookings')
        <a href="{{ url('/bookings') }}" class="kpi-card bookings">
            <div class="kpi-card-header">
                <p class="kpi-label">Bookings</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['bookings_total'] ?? 0 }}</p>
            <p class="kpi-subtitle">Today: <strong>{{ $stats['bookings_today'] ?? 0 }}</strong></p>
        </a>
    @endcan

    @can('view-clients')
        <a href="{{ url('/clients') }}" class="kpi-card clients">
            <div class="kpi-card-header">
                <p class="kpi-label">Clients</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['clients_total'] ?? 0 }}</p>
            <p class="kpi-subtitle">New this week: <strong>{{ $stats['clients_new_week'] ?? 0 }}</strong></p>
        </a>
    @endcan

    @can('view-messages')
        <a href="{{ url('/messages') }}" class="kpi-card messages">
            <div class="kpi-card-header">
                <p class="kpi-label">Messages</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['unread_messages'] ?? 0 }}</p>
            <p class="kpi-subtitle">Unread</p>
        </a>
    @endcan

    @can('view-inventory')
        <a href="{{ url('/inventory') }}" class="kpi-card inventory">
            <div class="kpi-card-header">
                <p class="kpi-label">Inventory</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['low_stock'] ?? 0 }}</p>
            <p class="kpi-subtitle">Low stock alerts</p>
        </a>
    @endcan

    @can('view-revenue')
        <a href="{{ url('/reports') }}" class="kpi-card revenue">
            <div class="kpi-card-header">
                <p class="kpi-label">Reports</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['revenue_mtd'] ?? '£0' }}</p>
            <p class="kpi-subtitle">Revenue (MTD)</p>
        </a>
    @endcan

    @can('view-calendar')
        <a href="{{ url('/calendar') }}" class="kpi-card calendar">
            <div class="kpi-card-header">
                <p class="kpi-label">Calendar</p>
                <div class="kpi-icon-wrapper">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <p class="kpi-value">{{ $stats['open_slots_today'] ?? 0 }}</p>
            <p class="kpi-subtitle">Open slots today</p>
        </a>
    @endcan
</div>