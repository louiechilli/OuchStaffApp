<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Services\Bookings\BookingTimeService;
use App\Services\Bookings\BookingAvailabilityService;
use App\Services\Bookings\BookingPersistenceService;
use App\Services\Bookings\GoogleCalendarSyncService;
use App\Services\Bookings\BookingEmailService;
use App\Services\Bookings\BookingPaymentService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SumUpReaderService;

class CreateBookingController extends Controller
{
    public function __construct(
        private BookingTimeService $timeService,
        private BookingAvailabilityService $availabilityService,
        private BookingPersistenceService $persistenceService,
        private GoogleCalendarSyncService $calendarService,
        private BookingEmailService $emailService,
        private BookingPaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        Log::info('CreateBookingController@index', ['user_id' => optional(auth()->user())->id]);

        $bookings = Booking::with(['clients', 'assignedTo', 'services'])
            ->orderBy('scheduled_start_at', 'desc')
            ->paginate(10);

        return view('pages.bookings.index', ['bookings' => $bookings]);
    }

    public function search(Request $request)
    {
        Log::info('CreateBookingController@search', [
            'query' => $request->input('q'),
            'user_id' => optional(auth()->user())->id,
        ]);

        $query = $request->input('q');
        $artistId = $request->input('artist_id');

        $queryBuilder = Booking::with(['clients', 'assignedTo', 'services']);

        if ($artistId) {
            $queryBuilder->where('assigned_to', $artistId);
        }

        if ($query) {
            $queryBuilder->where(function ($qB) use ($query) {
                $qB->whereHas('clients', function ($q) use ($query) {
                    $q->where('first_name', 'like', '%'.$query.'%')
                      ->orWhere('last_name', 'like', '%'.$query.'%');
                })
                ->orWhereHas('services', function ($q) use ($query) {
                    $q->where('name', 'like', '%'.$query.'%');
                });
            });
        }

        return response()->json(['bookings' => $queryBuilder->paginate(10)]);
    }

    public function selectBookingType(Request $request)
    {
        $serviceCategories = ServiceCategory::query()->where('active', true)->distinct('category')->get();
        $services = Service::query()->where('active', true)->get();

        Log::info('CreateBookingController@selectBookingType', [
            'serviceCategories_count' => $serviceCategories->count(),
            'services_count' => $services->count(),
            'user_id' => optional(auth()->user())->id,
        ]);

        return view('pages.bookings.selectBookingType', [
            'serviceCategories' => $serviceCategories,
            'services' => $services,
        ]);
    }
    
    public function create(Request $request)
    {
        $service = Service::query()
            ->where('active', true)
            ->where('id', $request->input('service_id'))
            ->first();

        Log::info('CreateBookingController@create request', [
            'service_id' => $request->input('service_id'),
            'found' => (bool) $service,
            'user_id' => optional(auth()->user())->id,
        ]);

        if (!$service) {
            return redirect()->route('bookings.selectBookingType')
                ->with('error', 'Please select a valid service to book.');
        }

        $clients = Client::all();
        $artists = User::role('artist')->get();

        Log::debug('CreateBookingController@create loaded data', [
            'clients_count' => $clients->count(),
            'artists_count' => $artists->count(),
            'service_id' => optional($service)->id,
        ]);

        return view('pages.bookings.createBooking', [
            'service' => $service,
            'clients' => $clients,
            'artists' => $artists,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('CreateBookingController@store called', [
            'service_id' => $request->input('service_id'),
            'artist_id' => $request->input('artist_id'),
            'client_id' => $request->input('client_id'),
            'selected_date' => $request->input('selected_date'),
            'selected_time' => $request->input('selected_time'),
            'duration' => $request->input('duration'),
            'user_id' => optional(auth()->user())->id,
        ]);

        $data = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')],
            'artist_id' => ['required', Rule::exists('users', 'id')],
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'selected_date' => ['required', 'date_format:Y-m-d'],
            'selected_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:15', 'max:480'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Log::debug('CreateBookingController@store validated', $data);

        $service = Service::findOrFail($data['service_id']);
        $artist = User::findOrFail($data['artist_id']);
        $client = Client::findOrFail((int) $data['client_id']);

        Log::debug('CreateBookingController@store models loaded', [
            'service_id' => $service->id,
            'artist_id' => $artist->id,
            'artist_email' => $artist->email,
            'artist_calendar_id' => $artist->google_calendar_id,
            'client_id' => $client->id,
            'client_email' => $client->email,
        ]);

        ['startLocal' => $startLocal, 'endLocal' => $endLocal, 'startUtc' => $startUtc, 'endUtc' => $endUtc] = 
            $this->timeService->calculateTimes($data['selected_date'], $data['selected_time'], (int) $data['duration']);

        if (!$this->availabilityService->checkGoogleAvailability($artist, $data['selected_date'], (int) $data['duration'], $startLocal, $endLocal)) {
            return back()
                ->withErrors(['selected_time' => 'That time is no longer available. Please pick another slot.'])
                ->withInput();
        }

        if ($this->availabilityService->checkLocalOverlap($artist->id, $startUtc, $endUtc)) {
            return back()
                ->withErrors(['selected_time' => 'That time overlaps another booking. Please choose a different slot.'])
                ->withInput();
        }

        $booking = $this->persistenceService->createBooking($service, $artist, $client, $startUtc, $endUtc, $data);

        Log::info('CreateBookingController@store booking persisted', ['booking_id' => $booking->id]);

        $googleError = $this->calendarService->syncBookingToCalendar(
            $booking,
            $artist,
            $client,
            $service,
            $startLocal,
            $endLocal,
            $this->timeService->getTimezone(),
            $data
        );

        $this->emailService->sendConfirmationEmail(
            $booking,
            $client,
            $service,
            $artist,
            $startLocal,
            $endLocal,
            $data
        );

        Log::info('CreateBookingController@store redirecting', [
            'booking_id' => $booking->id,
            'google_warning' => (bool) $googleError,
        ]);

        $redirect = redirect()->route('bookings.show', $booking)->with('success', 'Booking created successfully.');
        if ($googleError) {
            $redirect->with('warning', $googleError);
        }
        return $redirect;
    }

    public function show(Booking $booking)
    {
        Log::info('CreateBookingController@show', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        $payments = $booking->payments()->orderBy('created_at', 'desc')->get();
        $artist = $booking->assignedArtist;

        return view('pages.bookings.show', compact('booking', 'payments', 'artist'));
    }

    public function payment(Request $request, Booking $booking)
    {
        Log::info('CreateBookingController@payment called', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        return view('pages.payments.select-payment', compact('booking'));
    }

}