<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $expiringSims;
    public Collection $expiringKirs;
    public Collection $expiringTaxes;

    public function __construct(Collection $expiringSims, Collection $expiringKirs, Collection $expiringTaxes)
    {
        $this->expiringSims = $expiringSims;
        $this->expiringKirs = $expiringKirs;
        $this->expiringTaxes = $expiringTaxes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan: Dokumen Akan Segera Kedaluwarsa',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expiry-reminder',
        );
    }
}