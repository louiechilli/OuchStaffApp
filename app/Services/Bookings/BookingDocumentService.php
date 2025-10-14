<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\BookingDocument;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingDocumentService
{
    /**
     * Create document instances for a booking based on service requirements
     */
    public function createDocumentsForBooking(
        Booking $booking,
        Service $service,
        Client $client
    ): void {
        $templates = $service->requiredDocumentTemplates()->active()->get();

        if ($templates->isEmpty()) {
            Log::info('No documents required for service', [
                'booking_id' => $booking->id,
                'service_id' => $service->id,
            ]);
            return;
        }

        foreach ($templates as $template) {
            $renderedContent = $this->renderDocumentContent($template, $booking, $client);

            BookingDocument::create([
                'booking_id' => $booking->id,
                'document_template_id' => $template->id,
                'client_id' => $client->id,
                'content' => $renderedContent,
                'status' => 'pending',
            ]);

            Log::info('Document created for booking', [
                'booking_id' => $booking->id,
                'template_id' => $template->id,
                'template_name' => $template->name,
            ]);
        }
    }

    /**
     * Render document content with booking/client data
     */
    private function renderDocumentContent(
        $template,
        Booking $booking,
        Client $client
    ): string {
        $artist = $booking->assignedArtist;
        
        $data = [
            'client_name' => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->name ?? 'Client'),
            'client_first_name' => $client->first_name ?? '',
            'client_last_name' => $client->last_name ?? '',
            'client_email' => $client->email ?? '',
            'client_phone' => $client->phone ?? '',
            'artist_name' => $artist ? trim(($artist->first_name ?? '') . ' ' . ($artist->last_name ?? '')) : 'Artist',
            'booking_date' => $booking->scheduled_start_at ? $booking->scheduled_start_at->format('l, jS F Y') : '',
            'booking_time' => $booking->scheduled_start_at ? $booking->scheduled_start_at->format('g:i A') : '',
            'service_name' => $booking->services->first()->name ?? 'Service',
            'current_date' => now()->format('jS F Y'),
            'booking_id' => $booking->id,
        ];

        return $template->renderContent($data);
    }

    /**
     * Check if service requires documents
     */
    public function serviceRequiresDocuments(Service $service): bool
    {
        return $service->hasRequiredDocuments();
    }

    /**
     * Get required documents count for service
     */
    public function getRequiredDocumentsCount(Service $service): int
    {
        return $service->getRequiredDocumentsCount();
    }

    /**
     * Sign a document
     */
    public function signDocument(
        BookingDocument $document,
        string $signatureData,
        array $metadata = []
    ): bool {
        Log::info('Signing document', [
            'document_id' => $document->id,
            'booking_id' => $document->booking_id,
        ]);

        return $document->sign($signatureData, $metadata);
    }

    /**
     * Decline a document
     */
    public function declineDocument(BookingDocument $document): bool
    {
        Log::info('Declining document', [
            'document_id' => $document->id,
            'booking_id' => $document->booking_id,
        ]);

        return $document->decline();
    }

    /**
     * Mark document as viewed
     */
    public function markDocumentAsViewed(BookingDocument $document): void
    {
        $document->markAsViewed();
    }
}