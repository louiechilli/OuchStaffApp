<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GoogleCalendarSyncService
{
    public function syncBookingToCalendar(
        Booking $booking,
        User $artist,
        Client $client,
        Service $service,
        Carbon $startLocal,
        Carbon $endLocal,
        string $timezone,
        array $data
    ): ?string {
        if (empty($artist->google_calendar_id)) {
            return null;
        }

        try {
            $attendees = array_values(array_filter([
                $client->email ?? null,
                $artist->email ?? null,
            ]));

            Log::info('Mirroring to Google', [
                'calendar_id' => $artist->google_calendar_id,
                'booking_id' => $booking->id,
                'attendees' => $attendees,
                'add_meet' => true,
                'tz' => $timezone,
            ]);

            $impersonate = env('GOOGLE_IMPERSONATE');
            $gcal = new GoogleCalendarService(
                storage_path('app/google/service-account.json'),
                $impersonate ?: null
            );

            $summary = $this->buildEventSummary($service, $client);
            $description = $this->buildEventDescription($service, $client, $data);

            $event = $gcal->createEvent(
                calendarId: $artist->google_calendar_id,
                summary: $summary,
                start: $startLocal->format('Y-m-d H:i:s'),
                end: $endLocal->format('Y-m-d H:i:s'),
                description: $description,
                timezone: $timezone,
                attendeeEmails: $attendees,
                addMeetLink: (bool) env('GOOGLE_CREATE_MEET', false),
                popupReminderMins: 30
            );

            Log::info('Google event created', [
                'booking_id' => $booking->id,
                'event_id' => $event->getId(),
                'html_link' => $event->getHtmlLink(),
            ]);

            $this->storeGoogleReferences($booking, $event);

            return null;

        } catch (\Throwable $e) {
            \Log::error('Google event creation failed', [
                'booking_id' => $booking->id,
                'artist_id' => $artist->id,
                'calendarId' => $artist->google_calendar_id,
                'error' => $e->getMessage(),
            ]);
            Log::warning('Google mirror failed', ['message' => $e->getMessage()]);
            
            return 'Booking saved, but Google Calendar could not be updated.';
        }
    }

    private function buildEventSummary(Service $service, Client $client): string
    {
        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->name ?? 'Client');
        $serviceName = $service->name ?? $service->title ?? optional($service->category)->name ?? 'Service';
        
        return $serviceName . ' with ' . $clientName;
    }

    private function buildEventDescription(Service $service, Client $client, array $data): string
    {
        $serviceName = $service->name ?? $service->title ?? optional($service->category)->name ?? 'Service';
        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->name ?? 'Client');

        $descParts = [];
        $descParts[] = 'Service: ' . e($serviceName);
        $descParts[] = 'Client: ' . e($clientName) . ($client->email ? ' (' . e($client->email) . ')' : '');
        if (!empty($data['notes'])) {
            $descParts[] = 'Notes: ' . e($data['notes']);
        }
        
        return implode("\n", $descParts);
    }

    private function storeGoogleReferences(Booking $booking, $event): void
    {
        $dirty = false;
        
        if (Schema::hasColumn('bookings', 'google_event_id')) {
            $booking->google_event_id = $event->getId();
            $dirty = true;
        }
        
        if (Schema::hasColumn('bookings', 'google_event_link')) {
            $booking->google_event_link = $event->getHtmlLink();
            $dirty = true;
        }
        
        if ($dirty) {
            $booking->save();
        }

        Log::debug('Stored Google refs', [
            'booking_id' => $booking->id,
            'saved' => $dirty,
        ]);
    }
}