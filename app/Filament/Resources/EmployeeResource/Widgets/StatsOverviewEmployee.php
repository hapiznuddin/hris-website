<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\LeaveBalance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewEmployee extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isKaryawan();
    }
    protected function getStats(): array
    {
        $user = auth()->user();

        // Ambil employee ID dari relasi user->employee
        $employeeId = optional($user->employee)->id;

        // Ambil data cuti tahun ini
        $leaveBalance = LeaveBalance::where('employee_id', $employeeId)
            ->where('year', now()->year)
            ->first();

        $totalLeaves = $leaveBalance?->total_leaves ?? 0;
        $usedLeaves = $leaveBalance?->used_leaves ?? 0;
        $remainingLeaves = $totalLeaves - $usedLeaves;

        return [
            Stat::make('Total Cuti', $totalLeaves)
                ->icon('heroicon-s-calendar'),

            Stat::make('Cuti Terpakai', $usedLeaves)
                ->icon('heroicon-s-minus-circle'),

            Stat::make('Sisa Cuti', $remainingLeaves)
                ->icon('heroicon-s-check-circle'),
        ];
    }
}
