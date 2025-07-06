<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail; // <-- Tambahkan ini
use App\Mail\AdminMaintenanceNotification; // <-- Tambahkan ini

class CreateMaintenanceHistory extends CreateRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function afterCreate(): void
    {
        // Ambil data history yang baru dibuat
        $maintenanceHistory = $this->record;

        // Tentukan email admin
        $adminEmail = 'admin@maintenanceTruk.com';

        // Kirim email notifikasi ke admin
        Mail::to($adminEmail)->send(new AdminMaintenanceNotification($maintenanceHistory));


        // Ambil data dari repeater 'spareParts'
        $spareParts = $this->data['spareParts'];

        // Siapkan data untuk di-sync ke pivot table
        $pivotData = collect($spareParts)->mapWithKeys(function ($item) {
            return [$item['spare_part_id'] => ['jumlah' => $item['jumlah']]];
        })->all();

        // Lakukan sync ke pivot table
        $this->record->spareParts()->sync($pivotData);
    }
}