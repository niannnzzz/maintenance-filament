<?php

namespace App\Console\Commands;

use App\Models\MaintenanceHistory;
use App\Mail\DailyReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon; 

class SendMaintenanceReminders extends Command
{

    protected $signature = 'app:send-maintenance-reminders';
    protected $description = 'Periksa jadwal servis berkala (H-20, H-10, H-3) dan kirim notifikasi.';

public function handle()
{
    $this->info('Memeriksa jadwal servis untuk pengingat berkala...');

    // GUNAKAN QUERY INI, YANG MENGANDALKAN FUNGSI TANGGAL DARI DATABASE
    $upcomingMaintenances = \App\Models\MaintenanceHistory::query()
        ->where('status', '!=', 'Completed')
        ->where(function ($query) {
            // Cek apakah tanggal servis berikutnya sama dengan:
            // H+3 ATAU H+10 ATAU H+20 dari tanggal DATABASE saat ini
            $query->whereRaw('DATE(tanggal_servis_berikutnya) = DATE_ADD(CURDATE(), INTERVAL 3 DAY)')
                  ->orWhereRaw('DATE(tanggal_servis_berikutnya) = DATE_ADD(CURDATE(), INTERVAL 10 DAY)')
                  ->orWhereRaw('DATE(tanggal_servis_berikutnya) = DATE_ADD(CURDATE(), INTERVAL 20 DAY)');
        })
        ->get();

    if ($upcomingMaintenances->isEmpty()) {
        $this->info('Tidak ada jadwal servis yang perlu diingatkan hari ini.');
        return;
    }

    $this->info("Ditemukan {$upcomingMaintenances->count()} jadwal. Mengirim email...");

    $adminEmail = 'admin@maintenanceTruk.com'; // Pastikan email ini benar

    Mail::to($adminEmail)->send(new \App\Mail\DailyReminderMail($upcomingMaintenances));

    $this->info('Email pengingat berkala berhasil dikirim.');
}
}