<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class EmployeeViewTable extends BaseWidget
{
    protected static ?string $heading = 'Data Pegawai';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isKaryawan();
    }

    public function table(Table $table): Table
    {

        return $table
            ->query(function () {
                $employeeId = optional(auth()->user()->employee)->id;
                $month = $this->tableFilters['bulan']['value'] ?? now()->format('m');
                $year = $this->tableFilters['tahun']['value'] ?? now()->format('Y');
                return Employee::query()->where('id', $employeeId)
                    ->withCount([
                        'attendances as total_kehadiran' => function ($query) use ($month, $year) {
                            $query->where('status', 'Hadir');
                            if ($month) {
                                $query->whereMonth('date', $month);
                            }
                            if ($year) {
                                $query->whereYear('date', $year);
                            }
                        },
                        'attendances as total_izin' => function ($query) use ($month, $year) {
                            $query->where('status', 'Izin');
                            if ($month) {
                                $query->whereMonth('date', $month);
                            }
                            if ($year) {
                                $query->whereYear('date', $year);
                            }
                        },
                        'attendances as total_alpha' => function ($query) use ($month, $year) {
                            $query->where('status', 'Alpha');
                            if ($month) {
                                $query->whereMonth('date', $month);
                            }
                            if ($year) {
                                $query->whereYear('date', $year);
                            }
                        },
                    ]);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->circular()->label('Foto Pegawai')->size(50),
                Tables\Columns\TextColumn::make('name')->label('Nama Pegawai')->weight('bold')->size('lg'),
                Tables\Columns\TextColumn::make('nip')->label('NIP'),
                Tables\Columns\TextColumn::make('position')->label('Jabatan')->size('xs'),
                Tables\Columns\TextColumn::make('department')->label('Departemen')->size('xs'),

                TextColumn::make('total_kehadiran')
                    ->label('Kehadiran')
                    ->numeric(),

                TextColumn::make('total_izin')
                    ->label('Izin')
                    ->numeric(),

                TextColumn::make('total_alpha')
                    ->label('Alpha')
                    ->numeric(),
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->form([
                        Select::make('value')
                            ->options(array_combine(
                                range(1, 12),
                                array_map(fn($m) => Carbon::createFromFormat('m', $m)->translatedFormat('F'), range(1, 12))
                            )),
                    ])
                    ->query(fn($query) => $query),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->form([
                        Select::make('value')
                            ->options(
                                collect(range(2022, now()->year))
                                    ->mapWithKeys(fn($y) => [$y => $y])
                                    ->toArray()
                            )
                    ])
                    ->query(fn($query) => $query),
            ]);
    }
}
