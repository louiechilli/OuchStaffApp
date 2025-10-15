{{-- Quick Actions --}}

@push('styles')
<style>
    .quick-actions-section {
        margin-top: 48px;
    }
    
    .quick-actions-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
    }
    
    .quick-actions-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
    
    @media (min-width: 640px) {
        .quick-actions-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }
    }
    
    @media (min-width: 768px) {
        .quick-actions-grid {
            gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
    
    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        touch-action: manipulation;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        min-height: 56px;
    }
    
    .quick-action-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .quick-action-btn:active {
        transform: translateY(-1px) scale(0.98);
    }
    
    .quick-action-btn i {
        font-size: 18px;
        color: #64748b;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }
    
    .quick-action-btn:hover i {
        color: #0d9488;
    }
    
    /* Primary action styling */
    .quick-action-btn.primary {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        border-color: #0d9488;
        color: white;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.2);
    }
    
    .quick-action-btn.primary i {
        color: white;
    }
    
    .quick-action-btn.primary:hover {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        border-color: #0f766e;
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.3);
    }
    
    .quick-action-btn.primary:hover i {
        color: white;
    }
    
    /* Admin action styling */
    .quick-action-btn.admin {
        border-color: #ec4899;
        color: #ec4899;
    }
    
    .quick-action-btn.admin i {
        color: #ec4899;
    }
    
    .quick-action-btn.admin:hover {
        background: #fdf2f8;
        border-color: #db2777;
    }
    
    .quick-action-btn.admin:hover i {
        color: #db2777;
    }
</style>
@endpush

<div class="quick-actions-section">
    <h2 class="quick-actions-title">Quick Actions</h2>
    
    <div class="quick-actions-grid">
        @can('create-booking')
            <a href="{{ route('bookings.selectBookingType') }}" class="quick-action-btn primary">
                <i class="fa-solid fa-plus"></i>
                <span>New Booking</span>
            </a>
        @endcan

        @can('create-sale')
            <a href="{{ url('/bookings/create') }}" class="quick-action-btn">
                <i class="fa-solid fa-shopping-cart"></i>
                <span>New Sale</span>
            </a>
        @endcan

        @can('create-client')
            <a href="{{ url('/clients/create') }}" class="quick-action-btn">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Client</span>
            </a>
        @endcan

        @can('send-message')
            <a href="{{ url('/messages/compose') }}" class="quick-action-btn">
                <i class="fa-regular fa-message"></i>
                <span>Send Message</span>
            </a>
        @endcan

        @can('view-inventory')
            <a href="{{ url('/inventory') }}" class="quick-action-btn">
                <i class="fa-solid fa-box"></i>
                <span>Inventory</span>
            </a>
        @endcan

        @can('view-reports')
            <a href="{{ url('/reports') }}" class="quick-action-btn">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Reports</span>
            </a>
        @endcan

        @can('admin')
            <a href="{{ url('/settings') }}" class="quick-action-btn admin">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
        @endcan
    </div>
</div>