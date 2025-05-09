<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Http\Livewire\RecordAttendanceModal;
use App\Models\Employee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Data Pegawai';

    public function table(Table $table): Table
    {
        return $table
            ->query(

                Employee::query()->with('attendances')
                    ->whereHas('attendances', function (Builder $query) {
                        $month = request('tableFilters')['month'] ?? null;
                        $year = request('tableFilters')['year'] ?? null;

                        if ($month) {
                            $query->whereMonth('date', $month);
                        }

                        if ($year) {
                            $query->whereYear('date', $year);
                        }
                    })
            )
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->circular()->label('Foto Pegawai')->size(50),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Nama Pegawai')->weight('bold')->size('lg'),
                Tables\Columns\TextColumn::make('nip')->searchable()->label('NIP'),
                Tables\Columns\TextColumn::make('position')->searchable()->label('Jabatan')->size('xs'),
                Tables\Columns\TextColumn::make('department')->searchable()->label('Departemen')->size('xs'),
                Tables\Columns\TextColumn::make('total_attendances')
                    ->label('Kehadiran')
                    ->getStateUsing(function (Employee $record) {
                        $month = request('tableFilters')['month'] ?? now()->format('m');
                        $year = request('tableFilters')['year'] ?? now()->format('Y');

                        return $record->attendances()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->where('status', 'Hadir')
                            ->count();
                    }),
                Tables\Columns\TextColumn::make('izin_attendances')
                    ->label('Izin')
                    ->getStateUsing(function (Employee $record) {
                        $month = request('tableFilters')['month'] ?? now()->format('m');
                        $year = request('tableFilters')['year'] ?? now()->format('Y');

                        return $record->attendances()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->where('status', 'Izin')
                            ->count();
                    }),
                Tables\Columns\TextColumn::make('alpha_attendances')
                    ->label('Alpha')
                    ->getStateUsing(function (Employee $record) {
                        $month = request('tableFilters')['month'] ?? now()->format('m');
                        $year = request('tableFilters')['year'] ?? now()->format('Y');

                        return $record->attendances()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->where('status', 'Alpha')
                            ->count();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->label('Bulan')
                    ->options(array_combine(range(1, 12), array_map(fn($m) => Carbon::createFromFormat('m', $m)->translatedFormat('F'), range(1, 12))))
                    ->default(now()->format('m'))
                    ->query(function (Builder $query, $data) {
                        $query->whereHas('attendances', function (Builder $query) use ($data) {
                            $query->whereRaw('EXTRACT(MONTH FROM date) = ?', $data);
                        });
                    }),

                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        $firstYear = Attendance::selectRaw('MIN(EXTRACT(YEAR FROM date)) as year')->value('year');
                        $currentYear = now()->year;

                        // Jika tidak ada data, mulai dari tahun saat ini
                        $startYear = $firstYear ?? $currentYear;

                        // Buat rentang tahun dari tahun pertama hingga 5 tahun ke depan
                        $years = range($startYear, $currentYear + 5);

                        return array_combine($years, $years);
                    })
                    ->default(now()->format('Y'))
                    ->query(function (Builder $query, $data) {
                        $query->whereHas('attendances', function (Builder $query) use ($data) {
                            $query->whereRaw('EXTRACT(YEAR FROM date) = ?', $data);
                        });
                    }),
            ])
            ->actions([
                Action::make('recordAttendance')
                    ->label('Input Absensi')
                    ->icon('heroicon-o-clock')
                    ->color('success')
                    ->action(function (array $data, Employee $record) {
                        Attendance::updateOrCreate(
                            [
                                'employee_id' => $record->id,
                                'date' => now()->toDateString(),
                            ],
                            [
                                'clock_in' => $data['clock_in'],
                                'clock_out' => $data['clock_out'],
                                'status' => $data['status'],
                            ]
                        );
                    })->form([
                            TimePicker::make('clock_in')->label('Jam Masuk')->required(),
                            TimePicker::make('clock_out')->label('Jam Keluar')->required(),
                            Select::make('status')->options([
                                'Hadir' => 'Hadir',
                                'Terlambat' => 'Terlambat',
                                'Pulang Cepat' => 'Pulang Cepat',
                                'Alpha' => 'Alpha',
                                'Izin' => 'Izin',
                            ])
                                ->required(),
                        ])
                    ->modal()
                    ->modalHeading(fn(Employee $record) => "Input Absensi - {$record->name}")
                    ->modalSubmitActionLabel('Simpan'),
            ]);
        // ->contentGrid([
        //     'sm' => 1,
        //     'md' => 2,
        //     'lg' => 4,
        // ]);
    }
}
