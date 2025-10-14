@extends('layouts.app')

@php
    $tz = env('BOOKING_TZ', config('app.timezone', 'Europe/London'));
    $startLocal = optional($booking->scheduled_start_at)->setTimezone($tz);
    $endLocal   = optional($booking->scheduled_end_at)->setTimezone($tz);
    $duration   = $booking->duration ?? optional($startLocal)->diffInMinutes($endLocal) ?? null;

    $artist = $booking->artist ?? (isset($booking->assigned_to) ? \App\Models\User::find($booking->assigned_to) : null);
    $primaryClient = optional($booking->clients)->firstWhere('pivot.role', 'primary') ?? optional($booking->clients)->first();

    $serviceLines = collect($booking->services ?? [])->map(function($s){
        return [
            'id' => $s->id,
            'name' => $s->name ?? 'Service',
            'unit_price' => $s->pivot->unit_price ?? 0,
            'qty' => $s->pivot->qty ?? 1,
            'line_total' => $s->pivot->line_total ?? (($s->pivot->unit_price ?? 0) * ($s->pivot->qty ?? 1)),
        ];
    });
    $totalAmount = $serviceLines->sum('line_total');
@endphp

@section('content')
<div class="max-w-5xl mx-auto p-6">
    @include('components.pageHeader', [
        'title' => 'Booking #'.$booking->id,
        'subtitle' => $booking->type ? ucfirst($booking->type).' booking' : 'Booking details',
        'action' => 'Back to Bookings', 
        'actionUrl' => route('bookings.index')
    ])

    {{-- Payment Status Indicators --}}
    @if($payments->isNotEmpty())
        @php
            $latestPayment = $payments->first();
        @endphp

        @if($latestPayment->status === 'succeeded' || $latestPayment->status === 'completed' || $latestPayment->status === 'paid')
            <div class="w-full bg-green-100 rounded-lg p-4 mb-6 text-center text-green-800 font-bold text-2xl">
                PAID
            </div>
        @elseif($latestPayment->status === 'failed' || $latestPayment->status === 'pending' || $latestPayment->status === 'unpaid')
            <div class="w-full bg-red-100 rounded-lg p-4 mb-6 text-center text-red-800 font-bold text-2xl">
                UNPAID
            </div>
        @elseif($latestPayment->status === 'refunded')
            <div class="w-full bg-gray-100 rounded-lg p-4 mb-6 text-center text-gray-800 font-bold text-2xl">
                REFUNDED
            </div>
        @endif
    @else
        <div class="w-full bg-red-100 rounded-lg p-4 mb-6 text-center text-red-800 font-bold text-2xl">
            UNPAID
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: booking details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 capitalize">{{ $booking->type }}</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Date</dt>
                        <dd class="text-base font-medium text-gray-900">
                            @if($startLocal)
                                {{ $startLocal->format('l, j F Y') }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Time</dt>
                        <dd class="text-base font-medium text-gray-900">
                            @if($startLocal && $endLocal)
                                {{ $startLocal->format('H:i') }} &ndash; {{ $endLocal->format('H:i') }} <span class="text-gray-500">({{ $tz }})</span>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Duration</dt>
                        <dd class="text-base text-gray-900">{{ $duration ? $duration.' mins' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Artist</dt>
                        <dd class="text-base text-gray-900">{{ $artist?->first_name . ' ' . $artist?->last_name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Client</h2>
                @if($primaryClient)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-medium">
                            {{ strtoupper(substr($primaryClient->name ?? ($primaryClient->first_name ?? '?'), 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $primaryClient->name ?? trim(($primaryClient->first_name ?? '').' '.($primaryClient->last_name ?? '')) }}</div>
                            <div class="text-sm text-gray-600">{{ $primaryClient->email ?? '' }}</div>
                            <div class="text-sm text-gray-600">{{ $primaryClient->phone ?? '' }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-600">No client linked.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Services</h2>
                @if($serviceLines->isEmpty())
                    <p class="text-sm text-gray-600">No services attached.</p>
                @else
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($serviceLines as $line)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $line['name'] }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">£{{ number_format($line['unit_price'], 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $line['qty'] }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900">£{{ number_format($line['line_total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <th colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">£{{ number_format($totalAmount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Notes</h2>
                    {{-- <a href="{{ route('bookings.notes.create', $booking) }}" class="px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50">Add note</a> --}}
                </div>
                <div class="mt-4 space-y-4">
                    @forelse(($booking->notes ?? []) as $note)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="text-sm text-gray-600 mb-1">By {{ $note->author?->name ?? 'System' }} · {{ $note->created_at?->setTimezone($tz)->format('j M Y H:i') }}</div>
                            <div class="text-gray-900 whitespace-pre-line">{{ $note->body }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No notes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: quick info / actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Summary</h3>
                <dl class="space-y-2">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Artist</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $artist?->first_name . ' ' . $artist?->last_name ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Client</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $primaryClient?->name ?? trim(($primaryClient->first_name ?? '').' '.($primaryClient->last_name ?? '')) ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Date</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $startLocal?->format('j M Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Time</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $startLocal?->format('H:i') }} &ndash; {{ $endLocal?->format('H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Total</dt>
                        <dd class="text-sm font-semibold text-gray-900">£{{ number_format($totalAmount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('payment.index', $booking) }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200">Take Payment</a>
                    {{-- <a href="{{ route('bookings.reschedule', $booking) }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200">Reschedule</a> --}}
                    {{-- <a href="{{ route('bookings.cancel', $booking) }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200">Cancel</a> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
