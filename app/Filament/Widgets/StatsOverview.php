<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Karyawans\KaryawanResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\Kriterias\KriteriaResource;
use App\Models\Karyawan;
use App\Models\Kriteria;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Karyawan', Karyawan::count())
                ->description('Total Total Karyawan')
                ->descriptionIcon('heroicon-m-map')
                ->color('info')
                ->url(KaryawanResource::getUrl('index'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Jumlah Kriteria', Kriteria::count())
                ->description('Total Kriteria')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning')
                ->url(KriteriaResource::getUrl('index'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
