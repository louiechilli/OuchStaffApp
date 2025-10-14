<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class GoogleGmailService
{
    private Gmail $gmail;

    public function __construct(string $credentialsPath, string $impersonateEmail)
    {
        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([
            Gmail::GMAIL_SEND,
        ]);
        $client->setSubject($impersonateEmail); // impersonate no-reply@yourdomain.com

        $this->gmail = new Gmail($client);
    }

    public function sendEmail(string $to, string $subject, string $bodyHtml, string $fromName = 'No Reply')
    {
        $rawMessageString = "From: {$fromName} <no-reply@yourdomain.com>\r\n";
        $rawMessageString .= "To: {$to}\r\n";
        $rawMessageString .= "Subject: {$subject}\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $rawMessageString .= $bodyHtml;

        $rawMessage = rtrim(strtr(base64_encode($rawMessageString), '+/', '-_'), '=');

        $message = new \Google\Service\Gmail\Message();
        $message->setRaw($rawMessage);

        return $this->gmail->users_messages->send('me', $message);
    }
}