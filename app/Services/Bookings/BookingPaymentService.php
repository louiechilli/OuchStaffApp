<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class BookingPaymentService
{
    public function generatePaymentCode(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
    }

    public function recordCashPayment(Booking $booking, string $paymentId): Payment
    {
        $booking = $booking->fresh();

        if (!$booking) {
            throw new \RuntimeException('Booking not found');
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'currency' => 'GBP',
            'purpose' => 'final',
            'method' => 'cash',
            'status' => 'succeeded',
            'provider' => 'none',
            'provider_reference' => $paymentId,
            'raw_payload' => ['user_id' => auth()->id()],
            'captured_at' => now(),
        ]);

        Log::info('Cash payment recorded', [
            'booking_id' => $booking->id,
            'payment_code' => $paymentId,
            'amount' => $booking->total_amount,
        ]);

        return $payment;
    }
}