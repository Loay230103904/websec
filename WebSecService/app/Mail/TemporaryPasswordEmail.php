<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TemporaryPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $tempPassword;

    public function __construct($name, $tempPassword)
    {
        $this->name = $name;
        $this->tempPassword = $tempPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Temporary Password'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temporary_password',
            with: [
                'name' => $this->name,
                'tempPassword' => $this->tempPassword,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
