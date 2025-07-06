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

        // --- 1. UBAH STATUS TRUK MENJADI "PERBAIKAN" ---
        $truck = $maintenanceHistory->truck;
        if ($truck) {
            $truck->status = 'perbaikan'; // Pastikan 'perbaikan' adalah nilai yang valid di enum Anda
            $truck->save();
        }

        // --- 2. Logika untuk menyimpan dan mengurangi stok spare part ---
        $sparePartsData = $this->data['spareParts'] ?? [];
        if (!empty($sparePartsData)) {
            $pivotData = collect($sparePartsData)->mapWithKeys(function ($item) {
                return [$item['spare_part_id'] => ['jumlah' => $item['jumlah']]];
            })->all();
            $maintenanceHistory->spareParts()->sync($pivotData);

            // Langsung kurangi stok setelah sync
            $usedParts = $maintenanceHistory->spareParts()->get();
            DB::transaction(function () use ($usedParts) {
                foreach ($usedParts as $part) {
                    $quantityUsed = $part->pivot->jumlah;
                    $part->decrement('stok', $quantityUsed);
                }
            });
        }

        // --- 3. Logika untuk mengirim notifikasi email ke admin ---
        $adminEmail = 'admin@proyekanda.com'; // Ganti dengan email admin Anda
        Mail::to($adminEmail)->send(new AdminMaintenanceNotification($maintenanceHistory));
    }
}