@can('view-inventory')
    {{-- Inventory alerts --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-base md:text-lg font-semibold text-slate-800">Inventory alerts</h3>
            <a href="{{ url('/inventory') }}" class="text-sm text-slate-500 hover:text-slate-700">Manage</a>
        </div>
        <ul class="mt-4 space-y-2">
            @forelse(($inventoryAlerts ?? []) as $item)
                <li class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $item->name }}</p>
                        <p class="text-xs text-slate-500">Stock: {{ $item->stock }}</p>
                    </div>
                    <span class="mt-1 inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs text-amber-700">Low</span>
                </li>
            @empty
                <li class="text-slate-500">All good — no low stock.</li>
            @endforelse
        </ul>
    </section>
@endcan
