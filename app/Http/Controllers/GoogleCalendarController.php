<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class GoogleCalendarController extends Controller
{
    public function connect()
    {
        $gcal = new GoogleCalendarService(
            config('services.google.client_id'),
            config('services.google.client_secret'),
            config('services.google.redirect'),
            session('google_token') // if already authenticated
        );

        if (!session()->has('google_token')) {
            return redirect()->away($gcal->getAuthUrl());
        }

        return redirect()->route('google.events');
    }

    public function callback(Request $request)
    {
        $gcal = new GoogleCalendarService(
            config('services.google.client_id'),
            config('services.google.client_secret'),
            config('services.google.redirect')
        );

        if ($request->has('error')) {
            return response()->json(['error' => $request->get('error')], 400);
        }

        $token = $gcal->fetchAccessToken($request->get('code'));

        // Save token to session (you can save to DB per artist later)
        session(['google_token' => $token]);

        return redirect()->route('google.events');
    }

    public function events()
    {
        $gcal = new GoogleCalendarService(
            config('services.google.client_id'),
            config('services.google.client_secret'),
            config('services.google.redirect'),
            session('google_token')
        );

        $events = $gcal->listEvents('primary', 5);

        return response()->json($events);
    }
}