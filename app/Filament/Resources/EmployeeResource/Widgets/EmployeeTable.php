<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Http\Livewire\RecordAttendanceModal;
use App\Models\Employee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class EmployeeTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Data Pegawai';

    public function table(Table $table): Table
    {
        return $table
            ->query(Employee::query()->latest())
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('photo')->circular()->label('Foto Pegawai')->size(50),
                    Tables\Columns\TextColumn::make('name')->searchable()->label('Nama Pegawai')->weight('bold'),
                    Tables\Columns\TextColumn::make('nip')->searchable()->label('NIP'),
                    Tables\Columns\TextColumn::make('position')->searchable()->label('Jabatan'),
                    Tables\Columns\TextColumn::make('department')->searchable()->label('Departemen'),
                ]),
            ])
            ->actions([
                Action::make('recordAttendance')
                    ->label('Input Absensi')
                    ->icon('heroicon-o-clock')
                    ->color('success')
                    ->action(function (array $data, Employee $record) {
                        \App\Models\Attendance::updateOrCreate(
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
            ])
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 4,
            ]);
    }
}
