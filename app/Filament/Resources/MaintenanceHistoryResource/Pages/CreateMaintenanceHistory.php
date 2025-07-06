<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminMaintenanceNotification;

class CreateMaintenanceHistory extends CreateRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function afterCreate(): void
    {
        $maintenanceHistory = $this->record;

        // --- 1. Logika untuk menyimpan spare part yang digunakan ---
        $sparePartsData = $this->data['spareParts'] ?? [];
        if (!empty($sparePartsData)) {
            $pivotData = collect($sparePartsData)->mapWithKeys(function ($item) {
                return [$item['spare_part_id'] => ['jumlah' => $item['jumlah']]];
            })->all();
            $maintenanceHistory->spareParts()->sync($pivotData);
        }


        // --- 2. Logika untuk mengurangi stok spare part ---
        $usedParts = $maintenanceHistory->spareParts()->get();
        if ($usedParts->isNotEmpty()) {
            DB::transaction(function () use ($usedParts) {
                foreach ($usedParts as $part) {
                    $quantityUsed = $part->pivot->jumlah;
                    $part->decrement('stok', $quantityUsed);
                }
            });
        }


        // --- 3. Logika untuk mengirim notifikasi email ke admin ---
        $adminEmail = 'admin@maintenanceTruk.com';
        Mail::to($adminEmail)->send(new AdminMaintenanceNotification($maintenanceHistory));
    }
}