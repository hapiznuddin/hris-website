<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Auth;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\FilterState;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $pluralLabel = 'Absensi';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->role === 'supervisor' || Auth::user()->role === 'dev' || Auth::user()->role === 'hrd';
    }

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
                    ->default(now())
                    ->label('Tanggal')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),

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
                    ->visible(fn($get) => $get('status') === 'izin'),
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

                Tables\Columns\TextColumn::make('total_attendances')
                    ->label('Kehadiran')
                    ->getStateUsing(function ($record) {
                        $filters = request('tableFilters', []);
                        $month = $filters['month'] ?? now()->format('m');
                        $year = $filters['year'] ?? now()->format('Y');

                        return $record->employee->attendances()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->where('status', 'Hadir')
                            ->count();
                    }),

                Tables\Columns\TextColumn::make('izin_attendances')
                    ->label('Izin')
                    ->getStateUsing(function ($record) {
                        $filters = request('tableFilters', []);
                        $month = $filters['month'] ?? now()->format('m');
                        $year = $filters['year'] ?? now()->format('Y');

                        return $record->employee->attendances()
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->where('status', 'Izin')
                            ->count();
                    }),

                Tables\Columns\TextColumn::make('alpha_attendances')
                    ->label('Alpha')
                    ->getStateUsing(function ($record) {
                        $filters = request('tableFilters', []);
                        $month = $filters['month'] ?? now()->format('m');
                        $year = $filters['year'] ?? now()->format('Y');

                        return $record->employee->attendances()
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
                    ->query(function (Builder $query, Tables\Filters\SelectFilter $filter) {
                        $month = $filter->getState();
                        if ($month) {
                            $query->whereMonth('date', $month);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(array_combine(range(now()->year - 5, now()->year), range(now()->year - 5, now()->year)))
                    ->default(now()->format('Y'))
                    ->query(function (Builder $query, Tables\Filters\SelectFilter $filter) {
                        $year = $filter->getState();
                        if ($year) {
                            $query->whereYear('date', $year);
                        }
                        return $query;
                    }),
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
