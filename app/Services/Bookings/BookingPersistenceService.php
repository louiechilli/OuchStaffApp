<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Bookings\BookingDocumentService;

class BookingPersistenceService
{
    public function __construct(
        private BookingDocumentService $documentService
    ) {}

    public function createBooking(
        Service $service,
        User $artist,
        Client $client,
        Carbon $startUtc,
        Carbon $endUtc,
        array $data
    ): Booking {
        return DB::transaction(function () use ($data, $artist, $service, $client, $startUtc, $endUtc) {
            Log::info('BookingPersistenceService DB transaction start');

            $booking = Booking::create([
                'type' => $service->category_id,
                'status' => 'scheduled',
                'scheduled_start_at' => $startUtc,
                'scheduled_end_at' => $endUtc,
                'location' => null,
                'total_amount' => $service->base_price ?? ($service->price ?? 0),
                'deposit_required_amount' => null,
                'created_by' => auth()->id(),
                'assigned_to' => $artist->id,
            ]);

            Log::debug('Booking created', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
            ]);

            $booking->clients()->attach($client->id, ['role' => 'primary']);
            Log::debug('Client attached', ['booking_id' => $booking->id, 'client_id' => $client->id]);

            $unitPrice = $service->price ?? ($service->base_price ?? 0);
            $qty = 1;
            $booking->services()->attach($service->id, [
                'unit_price' => $unitPrice,
                'qty' => $qty,
                'line_total' => $unitPrice * $qty,
            ]);

            Log::debug('Service attached', [
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'unit_price' => $unitPrice,
                'qty' => $qty,
            ]);

            if (!empty($data['notes'])) {
                $booking->notes()->create([
                    'author_id' => auth()->id(),
                    'visibility' => 'internal',
                    'body' => $data['notes'],
                ]);

                Log::debug('Note created', ['booking_id' => $booking->id, 'author_id' => auth()->id()]);
            }

             // Create required documents for this booking
            // $this->documentService->createDocumentsForBooking($booking, $service, $client);


            Log::info('BookingPersistenceService DB transaction complete', ['booking_id' => $booking->id]);

            return $booking;
        });
    }
}