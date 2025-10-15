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
    
    // Determine payment status
    $paymentStatus = 'unpaid';
    $paymentLabel = 'Unpaid';
    if($payments->isNotEmpty()) {
        $latestPayment = $payments->first();
        if(in_array($latestPayment->status, ['succeeded', 'completed', 'paid'])) {
            $paymentStatus = 'paid';
            $paymentLabel = 'Paid';
        } elseif($latestPayment->status === 'refunded') {
            $paymentStatus = 'refunded';
            $paymentLabel = 'Refunded';
        }
    }
@endphp

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-6 py-8">
        @include('components.pageHeader', [
            'title' => 'Booking #'.$booking->id,
            'subtitle' => $booking->type ? ucfirst($booking->type).' booking' : 'Booking details',
            'action' => 'Back to Bookings', 
            'actionUrl' => route('bookings.index')
        ])

        {{-- Payment Status --}}
        <div class="mb-8">
            @if($paymentStatus === 'paid')
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="text-sm font-medium text-emerald-900">{{ $paymentLabel }}</span>
                </div>
            @elseif($paymentStatus === 'refunded')
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="w-2 h-2 bg-slate-500 rounded-full"></div>
                    <span class="text-sm font-medium text-slate-900">{{ $paymentLabel }}</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                    <span class="text-sm font-medium text-amber-900">{{ $paymentLabel }}</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Appointment Details --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Appointment</h2>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">Date</dt>
                                <dd class="text-base text-slate-900">
                                    @if($startLocal)
                                        {{ $startLocal->format('l, j F Y') }}
                                    @else
                                        <span class="text-slate-400">Not scheduled</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">Time</dt>
                                <dd class="text-base text-slate-900">
                                    @if($startLocal && $endLocal)
                                        {{ $startLocal->format('H:i') }} – {{ $endLocal->format('H:i') }}
                                        <span class="text-sm text-slate-400 ml-1">({{ $tz }})</span>
                                    @else
                                        <span class="text-slate-400">TBD</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">Duration</dt>
                                <dd class="text-base text-slate-900">
                                    {{ $duration ? $duration.' minutes' : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">Artist</dt>
                                <dd class="text-base text-slate-900">
                                    {{ $artist?->first_name . ' ' . $artist?->last_name ?? '—' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Client --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Client</h2>
                    </div>
                    <div class="px-6 py-6">
                        @if($primaryClient)
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-medium flex-shrink-0">
                                    {{ strtoupper(substr($primaryClient->name ?? ($primaryClient->first_name ?? '?'), 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-slate-900 mb-2">
                                        {{ $primaryClient->name ?? trim(($primaryClient->first_name ?? '').' '.($primaryClient->last_name ?? '')) }}
                                    </div>
                                    @if($primaryClient->email)
                                        <div class="text-sm text-slate-600 mb-1">{{ $primaryClient->email }}</div>
                                    @endif
                                    @if($primaryClient->phone)
                                        <div class="text-sm text-slate-600">{{ $primaryClient->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-slate-500">No client linked to this booking.</p>
                        @endif
                    </div>
                </div>

                {{-- Services --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Services</h2>
                    </div>
                    <div class="px-6 py-6">
                        @if($serviceLines->isEmpty())
                            <p class="text-sm text-slate-500">No services attached to this booking.</p>
                        @else
                            <div class="space-y-3 mb-6">
                                @foreach($serviceLines as $line)
                                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                                        <div class="flex-1">
                                            <div class="font-medium text-slate-900">{{ $line['name'] }}</div>
                                            <div class="text-sm text-slate-500 mt-0.5">
                                                £{{ number_format($line['unit_price'], 2) }} × {{ $line['qty'] }}
                                            </div>
                                        </div>
                                        <div class="text-base font-medium text-slate-900 ml-4">
                                            £{{ number_format($line['line_total'], 2) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                <span class="text-base font-semibold text-slate-900">Total</span>
                                <span class="text-xl font-semibold text-slate-900">£{{ number_format($totalAmount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Notes</h2>
                    </div>
                    <div class="px-6 py-6">
                        @forelse(($booking->notes ?? []) as $note)
                            <div class="mb-4 last:mb-0 pb-4 last:pb-0 border-b border-slate-100 last:border-0">
                                <div class="text-sm text-slate-500 mb-2">
                                    {{ $note->author?->name ?? 'System' }} · {{ $note->created_at?->setTimezone($tz)->format('j M Y H:i') }}
                                </div>
                                <div class="text-slate-900 whitespace-pre-line">{{ $note->body }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No notes yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Summary --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h3 class="text-base font-semibold text-slate-900">Summary</h3>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Artist</dt>
                                <dd class="text-sm font-medium text-slate-900 text-right">
                                    {{ $artist?->first_name . ' ' . $artist?->last_name ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Client</dt>
                                <dd class="text-sm font-medium text-slate-900 text-right">
                                    {{ $primaryClient?->name ?? trim(($primaryClient->first_name ?? '').' '.($primaryClient->last_name ?? '')) ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Date</dt>
                                <dd class="text-sm font-medium text-slate-900">
                                    {{ $startLocal?->format('j M Y') ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Time</dt>
                                <dd class="text-sm font-medium text-slate-900">
                                    {{ $startLocal?->format('H:i') ?? '—' }} – {{ $endLocal?->format('H:i') ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                <dt class="text-sm font-semibold text-slate-900">Total</dt>
                                <dd class="text-base font-semibold text-slate-900">£{{ number_format($totalAmount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-lg border border-slate-200 divide-y divide-slate-100">
                    <div class="px-6 py-4">
                        <h3 class="text-base font-semibold text-slate-900">Actions</h3>
                    </div>
                    <div class="px-6 py-6 space-y-3">
                        <a href="{{ route('payment.index', $booking) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            Take Payment
                        </a>
                        {{-- Uncomment when routes are ready
                        <a href="{{ route('bookings.reschedule', $booking) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors border border-slate-200">
                            Reschedule
                        </a>
                        <a href="{{ route('bookings.cancel', $booking) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200">
                            Cancel Booking
                        </a>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection