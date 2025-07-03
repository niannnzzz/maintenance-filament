<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceHistory;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingMaintenanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Jadwal Servis Mendatang (30 Hari ke Depan)';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return MaintenanceHistory::query()
            // Ambil data yang tanggal servis berikutnya kurang dari 30 hari dari sekarang
            ->where('tanggal_servis_berikutnya', '<=', now()->addDays(30))
            // Dan yang belum lewat tanggalnya
            ->where('tanggal_servis_berikutnya', '>=', now())
            // Urutkan dari yang paling dekat
            ->orderBy('tanggal_servis_berikutnya', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('truck.nopol')
                ->label('Nomor Polisi')
                ->searchable(),

            Tables\Columns\TextColumn::make('maintenanceSchedule.nama_servis')
                ->label('Jenis Servis'),

            Tables\Columns\TextColumn::make('tanggal_servis_berikutnya')
                ->label('Jatuh Tempo')
                ->date()
                ->sortable()
                ->badge()
                ->color(function($state) {
                    $daysUntil = Carbon::parse($state)->diffInDays(now());
                    if ($daysUntil <= 7) return 'danger';
                    if ($daysUntil <= 15) return 'warning';
                    return 'success';
                }),
        ];
    }
}