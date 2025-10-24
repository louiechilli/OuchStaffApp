<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SumUpReaderService;

class SumUpPaymentController extends Controller
{
    public function startPayment(Request $request, Booking $booking)
    {
        Log::info('SumUpPaymentController@startPayment called', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        // value  integer  required
        // Total amount of the transaction. It must be a positive integer.
        // Example: 1000
        $total = $booking->total_amount;

        $sumUpService = new SumUpReaderService();
        $readersResponse = $sumUpService->triggerPayment('MVE5VVM5', $total, 'GBP');

        // Return to waiting for payment page
        return redirect()->route('payment.sumup.waiting', [
            'booking' => $booking->id,
            'clientTransactionId' => $readersResponse['data']['client_transaction_id'] ?? null,
        ]);
    }

    public function waiting(Request $request, Booking $booking)
    {
        $clientTransactionId = $request->query('clientTransactionId');

        if (!$clientTransactionId) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Missing SumUp transaction identifier.');
        }

        return view('pages.payments.card.waiting-for-payment', [
            'booking' => $booking,
            'clientTransactionId' => $clientTransactionId,
        ]);
    }

    public function status(Request $request, Booking $booking)
    {
        $clientTransactionId = $request->query('clientTransactionId');
        if (!$clientTransactionId) {
            return response()->json([
                'ok' => false,
                'status' => 'error',
                'message' => 'Missing clientTransactionId',
            ], 400);
        }

        $sumUpService = new SumUpReaderService();
        $statusResponse = $sumUpService->getCheckoutStatus('MVE5VVM5', $clientTransactionId);

        if ($statusResponse === null) {
            return response()->json([
                'ok' => false,
                'status' => 'unknown',
                'message' => 'Unable to fetch status from SumUp',
            ], 502);
        }

        // Normalize status
        $rawStatus = $statusResponse['status'] ?? ($statusResponse['data']['status'] ?? 'unknown');
        $normalized = match (strtolower((string) $rawStatus)) {
            'paid', 'successful', 'success' => 'paid',
            'pending', 'initiated', 'processing' => 'pending',
            'failed', 'canceled', 'cancelled', 'error' => 'failed',
            default => 'pending',
        };

        return response()->json([
            'ok' => true,
            'status' => $normalized,
            'raw' => $statusResponse,
        ]);
    }
}