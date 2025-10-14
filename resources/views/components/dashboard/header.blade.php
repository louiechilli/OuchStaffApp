{{-- Header --}}
<div class="flex items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">
            @if(!empty($user->first_name))
                Welcome back, {{ trim(($user->first_name ?? '')) }}.
            @else
                Welcome back.
            @endif
        </h1>
        <p class="mt-1 text-slate-500">Everything you need at a glance: bookings, clients, messages, inventory & reports.</p>
    </div>
    <div class="hidden md:flex items-center gap-2 text-slate-500">
        <i class="fa-regular fa-calendar"></i>
        <span>{{ now()->format('D, j M Y') }}</span>
    </div>
</div>
