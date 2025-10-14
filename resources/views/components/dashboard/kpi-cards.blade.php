{{-- KPI / Navigation Cards --}}
<div class="mt-8 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 md:gap-6">
    @can('view-bookings')
        <a href="{{ url('/bookings') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Bookings</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['bookings_total'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-regular fa-calendar-check text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Today: {{ $stats['bookings_today'] ?? 0 }}</p>
        </a>
    @endcan

    @can('view-clients')
        <a href="{{ url('/clients') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Clients</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['clients_total'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-regular fa-user text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">New this week: {{ $stats['clients_new_week'] ?? 0 }}</p>
        </a>
    @endcan

    @can('view-messages')
        <a href="{{ url('/messages') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Messages</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['unread_messages'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-regular fa-envelope text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Unread</p>
        </a>
    @endcan


    @can('view-inventory')
        <a href="{{ url('/inventory') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Inventory</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['low_stock'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-solid fa-box-open text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Low stock alerts</p>
        </a>
    @endcan

    @can('view-revenue')
        <a href="{{ url('/reports') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Reports</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['revenue_mtd'] ?? '£0' }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Revenue (MTD)</p>
        </a>
    @endcan

    @can('view-calendar')
        <a href="{{ url('/calendar') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 md:p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Calendar</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-800">{{ $stats['open_slots_today'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 p-3 group-hover:bg-slate-200">
                    <i class="fa-regular fa-clock text-lg"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Open slots today</p>
        </a>
    @endcan
</div>
