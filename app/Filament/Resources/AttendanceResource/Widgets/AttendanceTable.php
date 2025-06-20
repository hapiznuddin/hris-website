<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AttendanceTable extends BaseWidget
{
    protected static ?string $heading = 'Absensi';
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
                $filters = $this->getTableFilters();
                $bulan = $filters['bulan'] ?? now()->format('m');
                $tahun = $filters['tahun'] ?? now()->format('Y');

                return Attendance::query()
                    ->where('employee_id', $employeeId)
                    ->when($bulan, fn($query) => $query->whereMonth('date', $bulan))
                    ->when($tahun, fn($query) => $query->whereYear('date', $tahun));
            })
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->searchable()
                    ->date('d M Y'),
                TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->time('H:i'),

                TextColumn::make('clock_out')
                    ->label('Jam Pulang')
                    ->time('H:i'),

                TextColumn::make('lembur')
                    ->label('Lembur')
                    ->getStateUsing(function ($record) {
                        try {
                            // Ambil tanggal Y-m-d dari kolom `date`
                            $date = Carbon::parse($record->date)->format('Y-m-d');

                            // Gabungkan tanggal dengan waktu
                            $clockIn = Carbon::parse("{$date} {$record->clock_in}");
                            $clockOut = Carbon::parse("{$date} {$record->clock_out}");

                            // Tangani shift malam
                            if ($clockOut->lessThan($clockIn)) {
                                $clockOut->addDay();
                            }

                            // Hitung durasi kerja
                            $duration = $clockIn->diffInMinutes($clockOut); // ✅ Arah benar: dari clockIn ke clockOut
                            $lemburMinutes = max(0, $duration - 480); // 8 jam kerja
            
                            return $lemburMinutes > 0
                                ? CarbonInterval::minutes($lemburMinutes)->cascade()->format('%h jam %i menit')
                                : '-';
                        } catch (\Exception $e) {
                            return 'Error: ' . $e->getMessage();
                        }
                    }),
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options(array_combine(
                        range(1, 12),
                        array_map(fn($m) => Carbon::createFromFormat('m', $m)->translatedFormat('F'), range(1, 12))
                    ))
                    ->query(fn($query) => $query),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(
                        collect(range(now()->year, now()->year - 5))
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()
                    )
                    ->query(fn($query) => $query),
            ]);
    }
}
