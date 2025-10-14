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
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingService
{
    public function __construct(
        private BookingTimeService $timeService,
        private BookingSyncService $syncService
    ) {}

    public function validateRequest(array $data): array
    {
        return validator($data, [
            'service_id'     => ['required', Rule::exists('services', 'id')],
            'artist_id'      => ['required', Rule::exists('users', 'id')],
            'client_id'      => ['required', Rule::exists('clients', 'id')],
            'selected_date'  => ['required', 'date_format:Y-m-d'],
            'selected_time'  => ['required', 'date_format:H:i'],
            'duration'       => ['required', 'integer', 'min:15', 'max:480'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ])->validate();
    }

    public function createBooking(array $data): Booking
    {
        [$service, $artist, $client] = $this->loadModels($data);

        [$startUtc, $endUtc, $startLocal, $endLocal] =
            $this->timeService->computeTimes($data['selected_date'], $data['selected_time'], $data['duration']);

        $this->timeService->assertAvailable($artist, $startUtc, $endUtc, $data);

        $booking = $this->persistBooking($service, $artist, $client, $data, $startUtc, $endUtc);

        $this->syncService->mirrorToGoogle($booking, $service, $client, $artist, $startLocal, $endLocal, $data);

        return $booking;
    }

    private function loadModels(array $data): array
    {
        return [
            Service::findOrFail($data['service_id']),
            User::findOrFail($data['artist_id']),
            Client::findOrFail((int) $data['client_id']),
        ];
    }

    private function persistBooking(Service $service, User $artist, Client $client, array $data, $startUtc, $endUtc): Booking
    {
        return DB::transaction(function () use ($service, $artist, $client, $data, $startUtc, $endUtc) {
            $booking = Booking::create([
                'type'               => $service->category_id,
                'status'             => 'scheduled',
                'scheduled_start_at' => $startUtc,
                'scheduled_end_at'   => $endUtc,
                'created_by'         => auth()->id(),
                'assigned_to'        => $artist->id,
            ]);

            $booking->clients()->attach($client->id, ['role' => 'primary']);
            $booking->services()->attach($service->id, [
                'unit_price' => $service->price,
                'qty'        => 1,
                'line_total' => $service->price,
            ]);

            if (!empty($data['notes'])) {
                $booking->notes()->create([
                    'author_id'  => auth()->id(),
                    'visibility' => 'internal',
                    'body'       => $data['notes'],
                ]);
            }

            return $booking;
        });
    }
}