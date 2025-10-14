{{-- Quick Actions --}}
<div class="mt-10">
    <h2 class="text-lg font-semibold text-slate-800">Quick actions</h2>
    <div class="mt-4 flex flex-wrap gap-3">
        @can('create-booking')
            <a href="{{ route('bookings.selectBookingType') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-plus"></i> New booking
            </a>
        @endcan

        @can('create-sale')
            <a href="{{ url('/bookings/create') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-shopping-cart"></i> New Physical Sale
            </a>
        @endcan

        @can('create-client')
            <a href="{{ url('/clients/create') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-user-plus"></i> Add client
            </a>
        @endcan

        @can('send-message')
            <a href="{{ url('/messages/compose') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-regular fa-message"></i> Message client
            </a>
        @endcan

        @can('view-inventory')
            <a href="{{ url('/inventory') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-box"></i> Manage inventory
            </a>
        @endcan

        @can('view-reports')
            <a href="{{ url('/reports') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-chart-pie"></i> View reports
            </a>
        @endcan

        @can('admin')
            <a href="{{ url('/reports') }}" class="inline-flex items-center gap-2 rounded-xl border border-pink-500 bg-white px-4 py-2 text-sm shadow-sm hover:shadow">
                <i class="fa-solid fa-toolbox"></i> Admin Settings
            </a>
        @endcan
    </div>
</div>
