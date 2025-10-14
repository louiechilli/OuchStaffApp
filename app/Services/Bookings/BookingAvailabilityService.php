<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingAvailabilityService
{
    public function checkGoogleAvailability(User $artist, string $date, int $duration, Carbon $startLocal, Carbon $endLocal): bool
    {
        if (empty($artist->google_calendar_id)) {
            return true;
        }

        Log::info('Checking Google availability', [
            'calendar_id' => $artist->google_calendar_id,
            'date' => $date,
            'duration' => $duration,
        ]);

        $impersonate = env('GOOGLE_IMPERSONATE');
        $gcal = new GoogleCalendarService(
            storage_path('app/google/service-account.json'),
            $impersonate ?: null
        );

        $slots = $gcal->getAvailableTimeSlots($artist->google_calendar_id, $duration, $date);

        Log::debug('Slots fetched', [
            'count' => is_array($slots) ? count($slots) : 0,
            'wanted' => [
                'start' => $startLocal->format('H:i'),
                'end' => $endLocal->format('H:i'),
            ],
        ]);

        $wanted = ['start' => $startLocal->format('H:i'), 'end' => $endLocal->format('H:i')];
        $isAvailable = collect($slots)->contains(fn($s) => 
            ($s['start'] ?? null) === $wanted['start'] && ($s['end'] ?? null) === $wanted['end']
        );

        Log::info('Google availability result', ['is_available' => $isAvailable]);

        return $isAvailable;
    }

    public function checkLocalOverlap(int $artistId, Carbon $startUtc, Carbon $endUtc): bool
    {
        Log::info('Checking local overlap', [
            'artist_id' => $artistId,
            'start_utc' => $startUtc->toIso8601String(),
            'end_utc' => $endUtc->toIso8601String(),
        ]);

        $overlap = Booking::where('assigned_to', $artistId)
            ->where(function ($q) use ($startUtc, $endUtc) {
                $q->whereBetween('scheduled_start_at', [$startUtc, $endUtc->copy()->subSecond()])
                  ->orWhereBetween('scheduled_end_at', [$startUtc->copy()->addSecond(), $endUtc])
                  ->orWhere(function ($q) use ($startUtc, $endUtc) {
                      $q->where('scheduled_start_at', '<=', $startUtc)
                        ->where('scheduled_end_at', '>=', $endUtc);
                  });
            })
            ->exists();

        Log::info('Local overlap result', ['overlap' => $overlap]);

        return $overlap;
    }
}