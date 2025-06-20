<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use DB;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
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
    //     use InteractsWithTable;
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Data Pegawai';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin();
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $month = $this->tableFilters['bulan']['value'] ?? now()->format('m');
                $year = $this->tableFilters['tahun']['value'] ?? now()->format('Y');
                return Employee::query()
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
                        'attendances as izin_attendances' => function ($query) use ($month, $year) {
                            $query->where('status', 'Izin');
                            if ($month) {
                                $query->whereMonth('date', $month);
                            }
                            if ($year) {
                                $query->whereYear('date', $year);
                            }
                        },
                        'attendances as alpha_attendances' => function ($query) use ($month, $year) {
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
                Tables\Columns\TextColumn::make('name')->searchable()->label('Nama Pegawai')->weight('bold')->size('lg'),
                Tables\Columns\TextColumn::make('nip')->searchable()->label('NIP'),
                Tables\Columns\TextColumn::make('position')->searchable()->label('Jabatan')->size('xs'),
                Tables\Columns\TextColumn::make('department')->searchable()->label('Departemen')->size('xs'),
                Tables\Columns\TextColumn::make('total_kehadiran')->label('Jumlah Hadir')->numeric(),
                Tables\Columns\TextColumn::make('izin_attendances')->label('Jumlah Izin')->numeric(),
                Tables\Columns\TextColumn::make('alpha_attendances')->label('Jumlah Alpha')->numeric(),
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
    }
}
