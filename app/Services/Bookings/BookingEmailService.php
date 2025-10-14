<?php

namespace App\Services\Bookings;

use App\Jobs\SendOuchEmail;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class BookingEmailService
{
    public function sendConfirmationEmail(
        Booking $booking,
        Client $client,
        Service $service,
        User $artist,
        Carbon $startLocal,
        Carbon $endLocal,
        array $data
    ): void {
        $emailData = [
            'title' => 'Booking Confirmation',
            'greeting' => 'Dear ' . trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) . ',',
            'first_line' => 'We are delighted to confirm your appointment at Ouch Tattoo Studio. Your booking has been successfully secured.',
            'html_content' => $this->buildEmailContent($booking, $service, $artist, $startLocal, $endLocal, $data),
            'actionUrl' => route('bookings.show', $booking),
            'actionText' => 'View Booking Details',
            'footer' => 'If you need to make changes to your appointment or have any questions, please contact us at your earliest convenience.',
            'headerImageUrl' => 'https://i.ibb.co/LsH8N38/image.png',
            'socialLinks' => [
                'instagram' => 'https://instagram.com/yourshop',
                'facebook' => 'https://facebook.com/yourshop'
            ]
        ];

        $emailHtml = view('emails.ouch.template', $emailData)->render();

        SendOuchEmail::dispatch(
            $client->email,
            'Booking Confirmation - ' . $startLocal->format('jS F Y'),
            $emailHtml
        );
    }

    private function buildEmailContent(
        Booking $booking,
        Service $service,
        User $artist,
        Carbon $startLocal,
        Carbon $endLocal,
        array $data
    ): string {
        return '
        <div style="background-color: #0f1913; padding: 25px; border-radius: 4px; border: 1px solid #2d4032; margin: 20px 0;">
            <h3 style="margin: 0 0 20px 0; color: #c9a962; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Appointment Details</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #9ba599; font-weight: 600;">Service</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #d4d0c4; text-align: right;">' . e($service->name ?? $service->title ?? 'Tattoo Session') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #9ba599; font-weight: 600;">Artist</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #d4d0c4; text-align: right;">' . e($artist->first_name . ' ' . $artist->last_name ?? 'Our Artist') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #9ba599; font-weight: 600;">Date</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #d4d0c4; text-align: right;">' . $startLocal->format('l, jS F Y') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #9ba599; font-weight: 600;">Time</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #d4d0c4; text-align: right;">' . $startLocal->format('g:i A') . ' - ' . $endLocal->format('g:i A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #9ba599; font-weight: 600;">Duration</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #2d4032; color: #d4d0c4; text-align: right;">' . (int)$data['duration'] . ' minutes</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #9ba599; font-weight: 600;">Price</td>
                    <td style="padding: 12px 0; color: #c9a962; text-align: right; font-size: 18px; font-weight: 600;">£' . number_format($booking->total_amount, 2) . '</td>
                </tr>
            </table>
        </div>
        
        <div style="margin: 25px 0; padding: 20px; background-color: rgba(201, 169, 98, 0.05); border-radius: 2px;">
            <p style="margin: 0 0 12px 0; color: #c9a962; font-weight: 600;">Important Reminders:</p>
            <ul style="margin: 0; padding-left: 20px; color: #d4d0c4; line-height: 1.8;">
                <li>Please arrive 15 minutes early to complete any necessary paperwork</li>
                <li>Ensure you have eaten a proper meal before your appointment</li>
                <li>Bring a valid form of identification</li>
                <li>If you need to reschedule, please give us at least 24 hours</li>
            </ul>
        </div>
        
        <p style="margin: 25px 0 0 0; color: #d4d0c4;">We look forward to welcoming you to our studio. Should you have any questions or require any adjustments to your booking, please do not hesitate to contact us.</p>
    ';
    }
}