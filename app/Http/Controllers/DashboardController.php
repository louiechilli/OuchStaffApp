<?php

namespace App\Http\Controllers;
use App\Services\GoogleCalendarService;
use App\Services\GoogleGmailService;
use App\Jobs\SendOuchEmail;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $gcal = new GoogleCalendarService(storage_path('app/google/service-account.json'));
        $user = auth()->user();

        if($user->google_calendar_id) {
            // For each artist, use their shared calendar ID
            $events = $gcal->listEvents($user->google_calendar_id);
        }

        $upcomingBookings = $user->bookingsAssigned()->where('scheduled_start_at', '>=', now())->orderBy('scheduled_start_at', 'asc')->limit(5)->get();

        // $gmail = new GoogleGmailService(
        //     storage_path('app/google/service-account.json'),
        //     'no-reply@ouchtattoostudio.co.uk'
        // );

        // return view('emails.layouts.email', [
        //     'title' => 'Ouch Tattoo Studio',
        //     'greeting' => 'Hello!',
        //     'first_line' => 'Thanks for signing up! We\'re excited to have you join our community.',
        //     'html_content' => '<p>This is a sample paragraph in the email body to demonstrate HTML content rendering.</p><p>Feel free to customize this section with any HTML elements you like, such as <strong>bold text</strong>, <em>italic text</em>, or even <a href="https://ouchtattoostudio.co.uk">links</a>.</p>',
        //     'actionUrl' => route('dashboard'),
        //     'actionText' => 'Visit Dashboard',
        //     'footer' => 'If you have any questions, feel free to contact us.',
        //     'headerImageUrl' => 'https://i.ibb.co/LsH8N38/image.png',
        //     'socialLinks' => [
        //         'instagram' => 'https://instagram.com/yourshop',
        //         'facebook' => 'https://facebook.com/yourshop'
        //     ]
        // ]);

        // // Use Blade template for email content
        // $emailHtml = view('emails.layouts.email', [
        //     'title' => 'Ouch Tattoo Studio',
        //     'greeting' => 'Hello!',
        //     'content' => 'Thanks for signing up! We\'re excited to have you join our community.',
        //     'actionUrl' => route('dashboard'),
        //     'actionText' => 'Visit Dashboard',
        //     'footer' => 'If you have any questions, feel free to contact us.',
        //     'headerImageUrl' => 'https://i.ibb.co/LsH8N38/image.png',
        // ])->render();

        // $gmail->sendEmail(
        //     'louie@cleanslateai.co.uk',
        //     'Welcome to Ouch Tattoo Studio',
        //     $emailHtml
        // );

        // $emailHtml = view('emails.ouch.template', [
        //     'title' => 'Ouch Tattoo Studio',
        //     'greeting' => 'Hello!',
        //     'first_line' => 'Thanks for signing up! We\'re excited to have you join our community.',
        //     'html_content' => '<p>This is a sample paragraph in the email body to demonstrate HTML content rendering.</p><p>Feel free to customize this section with any HTML elements you like, such as <strong>bold text</strong>, <em>italic text</em>, or even <a href="https://ouchtattoostudio.co.uk">links</a>.</p>',
        //     'actionUrl' => route('dashboard'),
        //     'actionText' => 'Visit Dashboard',
        //     'footer' => 'If you have any questions, feel free to contact us.',
        //     'headerImageUrl' => 'https://i.ibb.co/LsH8N38/image.png',
        //     'socialLinks' => [
        //         'instagram' => 'https://instagram.com/yourshop',
        //         'facebook' => 'https://facebook.com/yourshop'
        //     ]
        // ])->render();

        // // Queue the email
        // SendOuchEmail::dispatch(
        //     'louie@cleanslateai.co.uk',
        //     'Welcome to Ouch Tattoo Studio',
        //     $emailHtml
        // );

        $stats = [
            'bookings_total' => $user->bookingsAssigned()->count(),
            'bookings_today' => $user->bookingsAssigned()->whereDate('scheduled_start_at', now()->toDateString())->count(),
            'events' => $events ?? [],
        ];

        return view('welcome', compact('user', 'upcomingBookings', 'stats'));
    }
}
