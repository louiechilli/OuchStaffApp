@can('view-bookings')
    {{-- Upcoming bookings --}}
    <section class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-base md:text-lg font-semibold text-slate-800">Upcoming bookings</h3>
            <a href="{{ url('/bookings') }}" class="text-sm text-slate-500 hover:text-slate-700">View all</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-500">
                <tr>
                    <th class="py-2 pr-4">When</th>
                    <th class="py-2 pr-4">Client</th>
                    <th class="py-2 pr-4">Service</th>
                    <th class="py-2 pr-4">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse(($upcomingBookings ?? []) as $b)
                    @php
                        $bookingDate = $b->scheduled_start_at->startOfDay();
                        $today = now()->startOfDay();
                        $tomorrow = now()->addDay()->startOfDay();
                        
                        if ($bookingDate->equalTo($today)) {
                            $dateLabel = 'Today';
                        } elseif ($bookingDate->equalTo($tomorrow)) {
                            $dateLabel = 'Tomorrow';
                        } else {
                            $dateLabel = $b->scheduled_start_at->format('D, j M');
                        }
                    @endphp
                    <tr class="align-top">
                        <td class="py-2 pr-4 whitespace-nowrap">{{ $dateLabel }} · {{ $b->scheduled_start_at->format('H:i') }}</td>
                        @php
    $client = $b->clients->first();
@endphp
<td class="py-2 pr-4">
    {{ $client ? "{$client->first_name} {$client->last_name}" : 'Unassigned' }}
</td>
                        <td class="py-2 pr-4 capitalize">{{ $b->type }}</td>
                        <td class="py-2 pr-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs border border-slate-200">
                                            {{ ucfirst($b->status) }}
                                        </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-slate-500">No upcoming bookings.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endcan
