<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateMaintenanceHistory extends CreateRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    // TAMBAHKAN METHOD INI
    protected function afterCreate(): void
    {
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