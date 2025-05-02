<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $pluralLabel = 'Absensi';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->required()
                ->label('Nama Pegawai'),

            Forms\Components\DatePicker::make('date')
                ->required()
                ->default(now()),

            Forms\Components\TimePicker::make('clock_in')
                ->label('Waktu Masuk'),

            Forms\Components\TimePicker::make('clock_out')
                ->label('Waktu Pulang'),

            Forms\Components\Select::make('status')
                ->options([
                    'Hadir' => 'Hadir',
                    'Terlambat' => 'Terlambat',
                    'Pulang Cepat' => 'Pulang Cepat',
                    'Alpha' => 'Alpha',
                    'Izin' => 'Izin',
                ])
                ->required(),

            Forms\Components\Textarea::make('reason')
                ->label('Alasan Izin')
                ->visible(fn ($get) => $get('status') === 'izin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                ->searchable()
                ->label('Nama Pegawai'),

            Tables\Columns\TextColumn::make('date')
                ->date()
                ->label('Tanggal'),

            Tables\Columns\TextColumn::make('clock_in')
                ->time()
                ->label('Masuk'),

            Tables\Columns\TextColumn::make('clock_out')
                ->time()
                ->label('Pulang'),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'Hadir',
                    'warning' => 'Terlambat',
                    'danger' => 'Alpha',
                    'info' => 'Izin',
                    'primary' => 'Pulang Cepat',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
