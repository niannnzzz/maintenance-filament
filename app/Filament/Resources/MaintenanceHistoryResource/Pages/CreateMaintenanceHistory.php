<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminMaintenanceNotification;
use App\Models\SparePart;

class CreateMaintenanceHistory extends CreateRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function afterCreate(): void
    {
        $maintenanceHistory = $this->record;
        $sparePartsData = $this->data['spareParts'] ?? [];

        // --- 2. Logika untuk menyimpan relasi dan mengurangi stok spare part ---
        if (!empty($sparePartsData)) {
            $pivotData = collect($sparePartsData)->mapWithKeys(function ($item) {
                return [$item['spare_part_id'] => ['jumlah' => $item['jumlah']]];
            })->all();
            $maintenanceHistory->spareParts()->sync($pivotData);

            // Langsung kurangi stok setelah sync
            DB::transaction(function () use ($sparePartsData) {
                foreach ($sparePartsData as $item) {
                    $part = SparePart::find($item['spare_part_id']);
                    if ($part) {
                        $part->decrement('stok', $item['jumlah']);
                    }
                }
            });
        }
        
        // --- 3. LOGIKA UNTUK MENGHITUNG BIAYA (BAGIAN BARU) ---
        $totalCost = 0;
        if (!empty($sparePartsData)) {
            foreach ($sparePartsData as $item) {
                // Ambil harga langsung dari database untuk akurasi
                $sparePart = SparePart::find($item['spare_part_id']);
                if ($sparePart) {
                    // Kalkulasi: harga x jumlah
                    $totalCost += $sparePart->harga * $item['jumlah'];
                }
            }
        }

        // Simpan total biaya yang sudah dihitung ke dalam record
        $maintenanceHistory->total_biaya_spare_part = $totalCost;
        $maintenanceHistory->save();


        // --- 4. Logika untuk mengirim notifikasi email ke admin ---
        $adminEmail = 'admin@maintenanceTruk.com'; // Ganti dengan email admin Anda
        Mail::to($adminEmail)->send(new AdminMaintenanceNotification($maintenanceHistory));
    }
}