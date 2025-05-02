<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pegawai', Employee::count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Tetap', Employee::where('contract', 'tetap')->count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Kontrak', Employee::where('contract', 'kontrak')->count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Magang', Employee::where('contract', 'magang')->count())->icon('heroicon-s-users'),
        ];
    }
}
