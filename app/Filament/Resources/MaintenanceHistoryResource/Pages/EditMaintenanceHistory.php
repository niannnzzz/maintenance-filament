<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Enums\MaintenanceStatus; // <-- Tambahkan ini

class EditMaintenanceHistory extends EditRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Method ini akan berjalan secara otomatis
     * SETELAH data berhasil disimpan.
     */
    protected function afterSave(): void
    {
        // Ambil data yang baru saja disimpan
        $maintenanceHistory = $this->record;

        // Cek apakah statusnya adalah "Completed"
        if ($maintenanceHistory->status === MaintenanceStatus::Completed) {

            // Jika ya, ambil truk yang terhubung
            $truck = $maintenanceHistory->truck;

            // Jika truknya ada, ubah statusnya menjadi 'operasional'
            if ($truck) {
                $truck->status = 'operasional';
                $truck->save();
            }
        }
    }
}