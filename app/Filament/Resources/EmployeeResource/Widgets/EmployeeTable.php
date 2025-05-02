<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Http\Livewire\RecordAttendanceModal;
use App\Models\Employee;
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
            Tables\Columns\ImageColumn::make('photo')->circular(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('nip')->searchable(),
            Tables\Columns\TextColumn::make('position'),
            Tables\Columns\TextColumn::make('department'),
        ])
        ->actions([
            Action::make('recordAttendance')
                ->label('Input Absensi')
                ->icon('heroicon-o-clock')
                ->color('success')
                ->schema([
                    
                ])
                ->action(function (array $data, Employee $record) {
                    // Simpan data absensi
                    \App\Models\Attendance::updateOrCreate(
                        [
                            'employee_id' => $record->id,
                            'date' => now()->toDateString(),
                        ],
                        [
                            'clock_in' => $data['clock_in'],
                            'clock_out' => $data['clock_out'],
                        ]
                    );
                })
                ->modal()
                ->modalHeading(fn (Employee $record) => "Input Absensi - {$record->name}")
                ->modalSubmitActionLabel('Simpan'),
        ]);
}
}
