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
    // 1. Ubah judul agar sesuai dengan fungsinya
    protected static ?string $heading = 'Jadwal Service (30 Hari Kedepan)';
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = false;


    protected function getTableQuery(): Builder
    {
        // Query ini sudah benar, mengambil riwayat 30 hari terakhir
        // yang statusnya belum 'Completed'
        return MaintenanceHistory::query()
            ->with(['truck', 'maintenanceSchedule']) // <-- Tambahkan ini
            ->where('status', '!=', 'Completed')
            ->whereDate('tanggal_servis', '>=', now()->subDays(30))
            ->orderBy('tanggal_servis', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('truck.nopol')
                ->label('Nomor Polisi')
                ->searchable(),

            Tables\Columns\TextColumn::make('maintenanceSchedule.nama_servis')
                ->label('Jenis Servis'),

            // 2. UBAH BAGIAN INI
            Tables\Columns\TextColumn::make('tanggal_servis') // Ganti ke 'tanggal_servis'
                ->label('Tanggal Servis') // Ganti labelnya
                ->date()
                ->sortable()
                ->badge()
                ->color('primary'), // Beri warna netral karena ini bukan pengingat

            Tables\Columns\TextColumn::make('status')
                ->badge(),
        ];
    }
}