@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-xl mx-auto bg-white shadow rounded p-6">
        <h1 class="text-2xl font-semibold mb-2">Waiting for SumUp Payment</h1>
        <p class="text-gray-600 mb-4">Booking #{{ $booking->id }}</p>

        <div id="statusBox" class="rounded border p-4 mb-4">
            <div class="flex items-center gap-2">
                <span id="statusDot" class="inline-block w-2 h-2 rounded-full bg-yellow-400"></span>
                <span id="statusText" class="font-medium">Pending...</span>
            </div>
        </div>

        <div class="text-sm text-gray-500">
            This page checks payment status every 3 seconds.
        </div>

        <div class="mt-6">
            <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline">Back to booking</a>
        </div>
    </div>
</div>

<script>
    (function() {
        const statusText = document.getElementById('statusText');
        const statusDot = document.getElementById('statusDot');
        const statusBox = document.getElementById('statusBox');

        const query = new URLSearchParams({ clientTransactionId: @json($clientTransactionId) });
        const statusUrl = @json(route('payment.sumup.status', $booking));

        function setStatus(state) {
            if (state === 'paid') {
                statusText.textContent = 'Paid';
                statusDot.className = 'inline-block w-2 h-2 rounded-full bg-green-500';
                statusBox.className = 'rounded border p-4 mb-4 border-green-200 bg-green-50';
            } else if (state === 'failed') {
                statusText.textContent = 'Failed';
                statusDot.className = 'inline-block w-2 h-2 rounded-full bg-red-500';
                statusBox.className = 'rounded border p-4 mb-4 border-red-200 bg-red-50';
            } else {
                statusText.textContent = 'Pending...';
                statusDot.className = 'inline-block w-2 h-2 rounded-full bg-yellow-400';
                statusBox.className = 'rounded border p-4 mb-4 border-yellow-200 bg-yellow-50';
            }
        }

        async function poll() {
            try {
                const res = await fetch(statusUrl + '?' + query.toString(), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data && data.ok) {
                    setStatus(data.status);
                    if (data.status === 'paid') {
                        // Optionally redirect or stop polling
                        clearInterval(timer);
                    }
                }
            } catch (e) {
                // swallow
            }
        }

        const timer = setInterval(poll, 3000);
        poll();
    })();
</script>
@endsection





