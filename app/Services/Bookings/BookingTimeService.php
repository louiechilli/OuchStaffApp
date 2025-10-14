<?php

namespace App\Services\Bookings;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingTimeService
{
    public function __construct(
        private ?string $timezone = null
    ) {
        $this->timezone = $timezone ?? env('BOOKING_TZ', config('app.timezone', 'Europe/London'));
    }

    public function calculateTimes(string $date, string $time, int $duration): array
    {
        $startLocal = Carbon::createFromFormat('Y-m-d H:i', "$date $time", $this->timezone);
        $endLocal = (clone $startLocal)->addMinutes($duration);
        $startUtc = $startLocal->copy()->utc();
        $endUtc = $endLocal->copy()->utc();

        Log::debug('BookingTimeService times computed', [
            'tz' => $this->timezone,
            'start_local' => $startLocal->format('Y-m-d H:i:s'),
            'end_local' => $endLocal->format('Y-m-d H:i:s'),
            'start_utc' => $startUtc->toIso8601String(),
            'end_utc' => $endUtc->toIso8601String(),
        ]);

        return compact('startLocal', 'endLocal', 'startUtc', 'endUtc');
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }
}