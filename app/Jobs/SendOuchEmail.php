<?php

namespace App\Jobs;

use App\Services\GoogleGmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOuchEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $to;
    protected string $subject;
    protected string $html;

    public function __construct(string $to, string $subject, string $html)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->html = $html;
    }

    public function handle()
    {
        $gmail = new GoogleGmailService(
            storage_path('app/google/service-account.json'),
            'no-reply@ouchtattoostudio.co.uk'
        );

        $gmail->sendEmail($this->to, $this->subject, $this->html);
    }
}