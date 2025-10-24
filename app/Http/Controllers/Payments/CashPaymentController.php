<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Bookings\BookingPaymentService;

class CashPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(BookingPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function startPayment(Request $request, Booking $booking)
    {
        Log::info('CashPaymentController@startPayment called', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        return view('pages.payments.cash.start-payment', compact('booking'));
    }

    public function createCashCode(Request $request, Booking $booking)
    {
        Log::info('CashPaymentController@createCashCode called', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        $code = $this->paymentService->generatePaymentCode();

        return $code;
    }

    public function cashConfirm(Request $request, Booking $booking, $paymentId)
    {
        Log::info('CreateBookingController@cashConfirm called', [
            'booking_id' => $booking->id,
            'payment_code' => $paymentId,
            'user_id' => optional(auth()->user())->id,
        ]);

        try {
            $this->paymentService->recordCashPayment($booking, $paymentId);
            return response()->json(['status' => 'success']);
        } catch (\RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

}