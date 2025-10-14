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

class BookingTimeService
{
    public function computeTimes(string $date, string $time, int $duration): array
    {
        $tz = env('BOOKING_TZ', config('app.timezone', 'Europe/London'));
        $startLocal = Carbon::createFromFormat('Y-m-d H:i', "$date $time", $tz);
        $endLocal   = (clone $startLocal)->addMinutes($duration);
        return [$startLocal->copy()->utc(), $endLocal->copy()->utc(), $startLocal, $endLocal];
    }

    public function assertAvailable(User $artist, Carbon $startUtc, Carbon $endUtc, array $data): void
    {
        // check DB overlaps
        $overlap = Booking::where('assigned_to', $artist->id)
            ->where(function ($q) use ($startUtc, $endUtc) {
                $q->whereBetween('scheduled_start_at', [$startUtc, $endUtc])
                  ->orWhereBetween('scheduled_end_at', [$startUtc, $endUtc]);
            })->exists();

        if ($overlap) {
            throw new BookingUnavailableException('That time overlaps another booking.');
        }

        // check Google Calendar (if configured)
        if ($artist->google_calendar_id) {
            $gcal = new GoogleCalendarService(storage_path('app/google/service-account.json'));
            $slots = $gcal->getAvailableTimeSlots($artist->google_calendar_id, $data['duration'], $data['selected_date']);
            $wanted = [$startUtc->format('H:i'), $endUtc->format('H:i')];
            $isAvailable = collect($slots)->contains(fn ($s) => $s['start'] === $wanted[0] && $s['end'] === $wanted[1]);

            if (!$isAvailable) {
                throw new BookingUnavailableException('That time is no longer available.');
            }
        }
    }
}