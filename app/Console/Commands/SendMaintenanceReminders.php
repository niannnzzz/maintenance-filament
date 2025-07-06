<?php

namespace App\Console\Commands;

use App\Models\MaintenanceHistory;
use App\Mail\DailyReminderMail; // <-- Gunakan Mailable baru kita
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMaintenanceReminders extends Command
{
    protected $signature = 'app:send-maintenance-reminders';
    protected $description = 'Periksa jadwal maintenance hari ini dan kirim notifikasi email.';

    public function handle()
{
    $this->info('Memeriksa jadwal maintenance untuk hari ini...');

    // GUNAKAN QUERY INI, YANG MENGANDALKAN FUNGSI TANGGAL DARI DATABASE
    $upcomingMaintenances = MaintenanceHistory::query()
        ->whereRaw('DATE(tanggal_servis) = CURDATE()') // Membandingkan tanggal langsung di DB
        ->where('status', 'Scheduled')
        ->get();

    if ($upcomingMaintenances->isEmpty()) {
        $this->info('Tidak ada jadwal maintenance untuk hari ini.');
        return;
    }

    $this->info("Ditemukan {$upcomingMaintenances->count()} jadwal. Mengirim email...");

    $adminEmail = 'admin@maintenanceTruk.com'; // GANTI DENGAN EMAIL ADMIN ANDA

    // Kirim SATU email yang berisi SEMUA jadwal hari ini
    Mail::to($adminEmail)->send(new \App\Mail\DailyReminderMail($upcomingMaintenances));

    $this->info('Email pengingat berhasil dikirim.');
}
}