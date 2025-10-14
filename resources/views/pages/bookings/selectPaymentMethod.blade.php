{{-- resources/views/pages/bookings/selectPaymentMethod.blade.php --}}
@extends('layouts.app')

@section('title', 'Select Payment Method')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <div class="container mx-auto px-6 md:px-10 lg:px-16 py-8 md:py-10 flex flex-col gap-6">

            {{-- Title --}}
            <div class="flex items-end justify-between gap-4">
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">
                    Select Payment Method
                </h1>
                <div class="hidden md:flex items-center gap-2 text-slate-500">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ now()->format('D, j M Y') }}</span>
                </div>
            </div>

            {{-- Options --}}
            <div class="flex flex-col lg:flex-row items-center justify-center gap-8 mt-10">
                <a href="{{ route('bookings.create', ['payment_method' => 'card']) }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all w-80 h-64 flex flex-col items-center justify-center gap-6">
                    <img src="https://img.icons8.com/?size=200&id=x9U4HcNp4Tv2&format=png" alt="" class="w-32 h-32">
                    <span class="text-xl font-semibold text-slate-800">Credit/Debit Card</span>
                </a>
                <a href="{{ route('payment.cash', ['booking' => $booking->id]) }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all w-80 h-64 flex flex-col items-center justify-center gap-6">
                    <img src="https://img.icons8.com/?size=200&id=111611&format=png" alt="" class="w-32 h-32">
                    <span class="text-xl font-semibold text-slate-800">Cash</span>
                </a>
            </div>
        </div>
    </div>
@endsection
