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

}