<?php

namespace App\Mail;

use App\Models\MaintenanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMaintenanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public MaintenanceHistory $maintenanceHistory;

    public function __construct(MaintenanceHistory $maintenanceHistory)
    {
        $this->maintenanceHistory = $maintenanceHistory;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi: Jadwal Maintenance Baru Dibuat',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-notification', // Nama file tampilan email kita
        );
    }
}