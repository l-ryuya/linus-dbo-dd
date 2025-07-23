<?php

namespace App\Mail;

use App\Models\ServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractStatusNotificationsMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public ServiceContract $serviceContract;
    public string $contractStatus;
    public string $contractMessage;

    public function __construct(
        ServiceContract $serviceContract,
        string $contractStatus,
        string $contractMessage,
    ) {
        $this->onConnection('database');
        $this->onQueue('email');

        $this->serviceContract = $serviceContract;
        $this->contractStatus = $contractStatus;
        $this->contractMessage = $contractMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "DBO DD クラウドサイン通知 {$this->serviceContract->contract_name} {$this->contractStatus}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-status-notifications',
        );
    }
}
