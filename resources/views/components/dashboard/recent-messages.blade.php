@can('view-messages')
    {{-- Recent messages --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-base md:text-lg font-semibold text-slate-800">Recent messages</h3>
            <a href="{{ url('/messages') }}" class="text-sm text-slate-500 hover:text-slate-700">Open inbox</a>
        </div>
        <ul class="mt-4 divide-y divide-slate-100">
            @forelse(($recentMessages ?? []) as $m)
                <li class="py-3">
                    <p class="text-sm font-medium text-slate-800">{{ $m->client_name }}</p>
                    <p class="mt-0.5 text-sm text-slate-500 line-clamp-2">{{ $m->snippet }}</p>
                </li>
            @empty
                <li class="py-3 text-slate-500">No new messages.</li>
            @endforelse
        </ul>
    </section>
@endcan
