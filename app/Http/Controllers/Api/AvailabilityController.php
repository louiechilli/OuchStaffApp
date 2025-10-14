<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use DateTimeImmutable;
use DateTimeZone;

class AvailabilityController extends Controller
{
    public function getArtistAvailability(Request $request)
    {
        $data = $request->validate([
            'artist_id' => 'required|exists:users,id',
            'duration' => 'required|integer|min:15',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        $artist = User::findOrFail($data['artist_id']);
        if (!$artist->google_calendar_id) {
            return response()->json(['error' => 'Artist has no calendar configured'], 422);
        }

        $credentialsPath = storage_path('app/google/service-account.json');
        $gcal = new \App\Services\GoogleCalendarService($credentialsPath);

        $tz = new DateTimeZone(env('BOOKING_TZ', 'Europe/London'));
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $data['year'], $data['month']), $tz);
        $end   = $start->modify('last day of this month')->setTime(23, 59, 59);

        $timeMin = $start->format('Y-m-d\TH:i:sP'); // e.g. +01:00 in Sep
        $timeMax = $end->format('Y-m-d\TH:i:sP');

        $days = $gcal->getAvailableDays($artist->google_calendar_id, $data['duration'], $timeMin, $timeMax);

        return response()->json([
            'artist_id' => $artist->id,
            'month' => $data['month'],
            'year' => $data['year'],
            'available_days' => $days,
        ]);
    }

    public function getArtistTimeSlots(Request $request)
    {
        $data = $request->validate([
            'artist_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'duration' => 'required|integer|min:15',
        ]);

        $artist = User::findOrFail($data['artist_id']);
        if (!$artist->google_calendar_id) {
            return response()->json(['error' => 'Artist has no calendar configured'], 422);
        }

        $credentialsPath = storage_path('app/google/service-account.json');
        $gcal = new \App\Services\GoogleCalendarService($credentialsPath);

        $slots = $gcal->getAvailableTimeSlots($artist->google_calendar_id, $data['duration'], $data['date']);

        return response()->json([
            'artist_id' => $artist->id,
            'date' => $data['date'],
            'duration' => $data['duration'],
            'time_slots' => $slots,
        ]);
    }
}