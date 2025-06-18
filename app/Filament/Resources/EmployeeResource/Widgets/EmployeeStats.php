<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class EmployeeStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pegawai', Employee::count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Tetap', Employee::where('contract', 'Tetap')->count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Kontrak', Employee::where('contract', 'Kontrak')->count())->icon('heroicon-s-users'),
            Stat::make('Pegawai Magang', Employee::where('contract', 'Magang')->count())->icon('heroicon-s-users'),
        ];
    }

        public static function canView(): bool
    {
        $user = Auth::user();

        // Ganti sesuai field atau role Anda, misalnya:
        return $user && $user->role !== 'karyawan';
    }
}
