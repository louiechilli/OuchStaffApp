<?php

use App\Http\Controllers\Artists\ArtistController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bookings\CreateBookingController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\LockScreenController;
use App\Http\Middleware\CheckLockScreen;
use App\Http\Controllers\Bookings\BookingDocumentsController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/google/connect', [GoogleCalendarController::class, 'connect'])->name('google.connect');
    Route::get('/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
    Route::get('/google/events', [GoogleCalendarController::class, 'events'])->name('google.events');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', CheckLockScreen::class])->group(function () {

    // Booking documents routes
    Route::prefix('bookings/{booking}/documents')->name('bookings.documents.')->group(function () {
        Route::get('/', [BookingDocumentsController::class, 'index'])->name('index');
        Route::get('/{document}', [BookingDocumentsController::class, 'show'])->name('show');
        Route::post('/{document}/sign', [BookingDocumentsController::class, 'sign'])->name('sign');
        Route::post('/{document}/decline', [BookingDocumentsController::class, 'decline'])->name('decline');
        Route::get('/{document}/download', [BookingDocumentsController::class, 'download'])->name('download');
    });

    // Lock Screen Routes
    Route::get('/lock', [LockScreenController::class, 'show'])->name('lock.screen');
    Route::post('/lock', [LockScreenController::class, 'lock'])->name('lock.do');
    Route::post('/unlock', [LockScreenController::class, 'unlock'])->name('unlock');
    Route::get('/check-lock-status', [LockScreenController::class, 'checkStatus'])->name('lock.status');
    Route::post('/set-pin', [LockScreenController::class, 'setPin'])->name('set.pin');
    Route::post('/update-activity', [LockScreenController::class, 'updateActivity'])->name('update.update-activity');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('bookings')->group(function () {
        Route::get('/', [CreateBookingController::class, 'index'])->name('bookings.index');
        Route::post('/', [CreateBookingController::class, 'store'])->name('bookings.store');
        Route::get('/search', [CreateBookingController::class, 'search'])->name('bookings.search');
        Route::get('/today', [CreateBookingController::class, 'today'])->name('bookings.today');
        Route::get('/select-booking-type', [CreateBookingController::class, 'selectBookingType'])->name('bookings.selectBookingType');
        Route::get('/create-booking', [CreateBookingController::class, 'create'])->name('bookings.create');
        Route::get('/create/details', [CreateBookingController::class, 'details'])->name('bookings.create.details');

        Route::prefix('{booking}')->group(function () {
            Route::get('/', [CreateBookingController::class, 'show'])->name('bookings.show');

            Route::prefix('payment')->group(function () {
                Route::get('/', [CreateBookingController::class, 'payment'])->name('payment.index');
                Route::get('/cash', [CreateBookingController::class, 'cash'])->name('payment.cash');
                Route::post('/cash/start', [CreateBookingController::class, 'cashStart'])->name('payment.cash.start');
                Route::post('/cash/{paymentId}/confirm', [CreateBookingController::class, 'cashConfirm'])->name('payment.cash.confirm');
            });
        });
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/test', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/search', [ClientController::class, 'search'])->name('clients.search');
        Route::get('/searchs', [ClientController::class, 'search'])->name('clients.create');
        Route::get('/activity', [ClientController::class, 'search'])->name('clients.activity');
        Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show');
    });

    Route::prefix('artists')->group(function () {
        Route::get('/search', [ArtistController::class, 'search'])->name('artists.search');
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix('availability')->group(function () {
            Route::post('/artist', [AvailabilityController::class, 'getArtistAvailability'])->name('availability.artist');
            Route::post('/timeslots', [AvailabilityController::class, 'getArtistTimeSlots'])->name('availability.timeslots');
        });
    });

});
