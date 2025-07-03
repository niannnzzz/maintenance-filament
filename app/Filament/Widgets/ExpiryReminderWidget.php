<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\Truck;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiryReminderWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung jumlah SIM yang akan habis dalam 30 hari ke depan
        $simExpiresCount = Driver::where('sim_tanggal_kadaluarsa', '<=', now()->addDays(30))->count();

        // Hitung jumlah KIR yang akan habis dalam 30 hari ke depan
        $kirExpiresCount = Truck::where('kir_tanggal_kadaluarsa', '<=', now()->addDays(30))->count();
        
        // Hitung jumlah Pajak yang akan habis dalam 30 hari ke depan
        $taxExpiresCount = Truck::where('pajak_tanggal_kadaluarsa', '<=', now()->addDays(30))->count();

        return [
            Stat::make('SIM Segera Habis', $simExpiresCount)
                ->description('30 hari ke depan')
                ->color($simExpiresCount > 0 ? 'danger' : 'success'),

            Stat::make('KIR Segera Habis', $kirExpiresCount)
                ->description('30 hari ke depan')
                ->color($kirExpiresCount > 0 ? 'danger' : 'success'),

            Stat::make('Pajak Segera Habis', $taxExpiresCount)
                ->description('30 hari ke depan')
                ->color($taxExpiresCount > 0 ? 'danger' : 'success'),
        ];
    }
}