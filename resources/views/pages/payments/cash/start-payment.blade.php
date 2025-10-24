{{-- resources/views/pages/bookings/cashPayment.blade.php --}}
@extends('layouts.app')

@section('title', 'Cash Payment')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10 flex flex-col gap-6">

            {{-- Title --}}
            <div class="flex items-end justify-between gap-4">
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">
                    Cash Payment
                </h1>
                <div class="hidden md:flex items-center gap-2 text-slate-500">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ now()->format('D, j M Y') }}</span>
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Booking Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-600">Booking #:</span>
                        <span class="font-medium">{{ $booking->id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Amount:</span>
                        <span class="font-medium text-green-600">£{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Client:</span>
                        <span class="font-medium">{{ $booking->clients->first()?->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Date:</span>
                        <span class="font-medium">{{ $booking->scheduled_start_at->format('D, j M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Process --}}
            <div id="payment-container" class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm">
                
                {{-- Step 1: Start Payment --}}
                <div id="step-1" class="text-center">
                    <div class="mb-6">
                        <i class="fa-solid fa-money-bills text-6xl text-green-500 mb-4"></i>
                        <h2 class="text-2xl font-semibold text-slate-800 mb-2">Start Cash Payment</h2>
                        <p class="text-slate-600">Click below to generate a payment reference</p>
                    </div>
                    
                    <button id="start-payment-btn" 
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Generate Payment Reference
                    </button>
                </div>

                {{-- Step 2: Payment Instructions --}}
                <div id="step-2" class="hidden text-center">
                    <div class="mb-6">
                        <i class="fa-solid fa-envelope text-6xl text-blue-500 mb-4"></i>
                        <h2 class="text-2xl font-semibold text-slate-800 mb-4">Payment Instructions</h2>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                            <p class="text-blue-800 font-medium mb-4">Please write the following on an envelope:</p>
                            <div class="bg-white border-2 border-dashed border-blue-300 rounded-lg p-4 mb-4">
                                <div class="text-2xl font-bold text-blue-600" id="payment-reference">
                                    <!-- Payment reference will be inserted here -->
                                </div>
                            </div>
                            <p class="text-blue-700">
                                Put <strong>£{{ number_format($booking->total_amount, 2) }}</strong> cash inside the envelope
                            </p>
                        </div>
                    </div>
                    
                    <button id="confirm-payment-btn" 
                            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Confirm Payment Completed
                    </button>
                </div>

                {{-- Step 3: Success --}}
                <div id="step-3" class="hidden text-center">
                    <div class="mb-6">
                        <i class="fa-solid fa-check-circle text-6xl text-green-500 mb-4"></i>
                        <h2 class="text-2xl font-semibold text-slate-800 mb-2">Payment Recorded</h2>
                        <p class="text-slate-600">Cash payment has been successfully recorded</p>
                    </div>
                    
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors inline-block">
                        View Booking
                    </a>
                </div>

                {{-- Loading State --}}
                <div id="loading" class="hidden text-center">
                    <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                    <p class="text-slate-600">Processing...</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startBtn = document.getElementById('start-payment-btn');
            const confirmBtn = document.getElementById('confirm-payment-btn');
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const step3 = document.getElementById('step-3');
            const loading = document.getElementById('loading');
            const paymentReference = document.getElementById('payment-reference');
            
            let paymentId = null;

            // Start payment
            startBtn.addEventListener('click', async function() {
                showLoading();
                
                try {
                    const response = await fetch('{{ route("payment.cash.get-cash-code", ["booking" => $booking->id]) }}', {
                        method: 'GET'
                    });
                    
                    const data = await response.text();
                    paymentId = data;
                    paymentReference.textContent = data;
                    showStep2();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error occurred');
                    showStep1();
                }
            });

            // Confirm payment
            confirmBtn.addEventListener('click', async function() {
                if (!paymentId) {
                    alert('No payment reference found');
                    return;
                }
                
                showLoading();
                
                try {
                    const response = await fetch('{{ route("payment.cash.confirm", ["booking" => $booking->id, "paymentId" => "paymentId"]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        showStep3();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to confirm payment'));
                        showStep2();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error occurred');
                    showStep2();
                }
            });

            function showLoading() {
                step1.classList.add('hidden');
                step2.classList.add('hidden');
                step3.classList.add('hidden');
                loading.classList.remove('hidden');
            }

            function showStep1() {
                loading.classList.add('hidden');
                step2.classList.add('hidden');
                step3.classList.add('hidden');
                step1.classList.remove('hidden');
            }

            function showStep2() {
                loading.classList.add('hidden');
                step1.classList.add('hidden');
                step3.classList.add('hidden');
                step2.classList.remove('hidden');
            }

            function showStep3() {
                loading.classList.add('hidden');
                step1.classList.add('hidden');
                step2.classList.add('hidden');
                step3.classList.remove('hidden');
            }
        });
    </script>
@endsection
