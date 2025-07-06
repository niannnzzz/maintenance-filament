<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Models\Truck;
use App\Mail\ExpiryReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExpiryReminders extends Command
{
    protected $signature = 'app:send-expiry-reminders';
    protected $description = 'Periksa masa berlaku SIM, KIR, dan Pajak yang habis hari ini dan kirim notifikasi.';

    public function handle()
{
    $this->info('Memeriksa dokumen yang kedaluwarsa hari ini...');

    // 1. Cari SIM Driver yang habis masa berlakunya HARI INI
    $expiringSims = \App\Models\Driver::whereRaw('DATE(sim_tanggal_kadaluarsa) = CURDATE()')->get();

    // 2. Cari KIR Truk yang habis masa berlakunya HARI INI
    $expiringKirs = \App\Models\Truck::whereRaw('DATE(kir_tanggal_kadaluarsa) = CURDATE()')->get();

    // 3. Cari Pajak Truk yang habis masa berlakunya HARI INI
    $expiringTaxes = \App\Models\Truck::whereRaw('DATE(pajak_tanggal_kadaluarsa) = CURDATE()')->get();


    if ($expiringSims->isEmpty() && $expiringKirs->isEmpty() && $expiringTaxes->isEmpty()) {
        $this->info('Tidak ada dokumen yang kedaluwarsa hari ini.');
        return;
    }

    $this->info('Ditemukan dokumen yang kedaluwarsa. Mengirim email ringkasan...');

    $adminEmail = 'admin@proyekanda.com'; // GANTI DENGAN EMAIL ADMIN ANDA

    // Kirim email notifikasi
    Mail::to($adminEmail)->send(new \App\Mail\ExpiryReminderMail($expiringSims, $expiringKirs, $expiringTaxes));

    $this->info('Email pengingat kedaluwarsa berhasil dikirim.');
}
}
