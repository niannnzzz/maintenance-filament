<?php

namespace App\Mail;

use App\Models\MaintenanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    // Properti ini akan menyimpan data SEMUA jadwal untuk hari ini
    public $upcomingMaintenances;

    public function __construct($upcomingMaintenances)
    {
        $this->upcomingMaintenances = $upcomingMaintenances;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat: Jadwal Maintenance Hari Ini',
        );
    }

    public function content(): Content
    {
        return new Content(
            // Kita akan buat file view baru untuk email ini
            view: 'emails.daily-reminder',
        );
    }
}