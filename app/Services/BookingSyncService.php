<?php

declare(strict_types=1);

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GEvent;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventReminders;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey;

class BookingSyncService
{
    public function mirrorToGoogle(
        Booking $booking,
        Service $service,
        Client $client,
        User $artist,
        Carbon $startLocal,
        Carbon $endLocal,
        array $data
    ): void {
        if (!$artist->google_calendar_id) {
            return;
        }

        try {
            $gcal = new GoogleCalendarService(storage_path('app/google/service-account.json'));

            $summary = ($service->name ?? 'Service') . ' with ' . ($client->name ?? 'Client');
            $notes = $data['notes'] ?? '';
            $description = "Service: {$service->name}\nClient: {$client->name} ({$client->email})\nNotes: {$notes}";

            $event = $gcal->createEvent(
                $artist->google_calendar_id,
                $summary,
                $startLocal,
                $endLocal,
                $description,
                env('BOOKING_TZ'),
                [$client->email, $artist->email],
                (bool) env('GOOGLE_CREATE_MEET', false),
                30
            );

            if (Schema::hasColumn('bookings', 'google_event_id')) {
                $booking->google_event_id = $event->getId();
            }
            if (Schema::hasColumn('bookings', 'google_event_link')) {
                $booking->google_event_link = $event->getHtmlLink();
            }
            $booking->save();
        } catch (\Throwable $e) {
            \Log::warning('BookingSyncService::mirrorToGoogle failed', ['error' => $e->getMessage()]);
        }
    }
}