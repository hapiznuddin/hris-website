<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Filament\Resources\LeaveBalanceResource\RelationManagers;
use App\Models\LeaveBalance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model = LeaveBalance::class;
    protected static ?string $navigationLabel = 'Jatah Cuti';
    protected static ?string $pluralLabel = 'Jatah Cuti';
    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isKaryawan();
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->isAdmin() || $record->employee_id === auth()->user()->employee->id;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->isKaryawan()) {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Nama Pegawai')
                    ->relationship('employee', 'name')
                    ->required()
                    ->searchable()
                    ->disabled(fn() => auth()->user()->isKaryawan()),

                Forms\Components\TextInput::make('total_leaves')
                    ->label('Total Cuti')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->disabled(fn() => auth()->user()->isKaryawan()),

                Forms\Components\TextInput::make('used_leaves')
                    ->label('Sudah Diambil')
                    // ->required()
                    ->numeric()
                    ->minValue(0)
                    ->disabled(), // hanya ditampilkan

                Forms\Components\TextInput::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->required()
                    ->default(now()->year)
                    ->disabled(fn() => auth()->user()->isKaryawan()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->searchable()
                    ->label('Nama Pegawai'),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun'),

                Tables\Columns\TextColumn::make('employee.leaveBalance.total_leaves')
                    ->label('Total Cuti'),

                Tables\Columns\TextColumn::make('employee.leaveBalance.used_leaves')
                    ->label('Cuti Terpakai'),

                Tables\Columns\TextColumn::make('employee.leaveBalance')
                    ->label('Sisa Cuti')
                    ->getStateUsing(
                        fn($record) =>
                        optional($record->employee->leaveBalance)->total_leaves
                        - optional($record->employee->leaveBalance)->used_leaves
                    ),
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
            'index' => Pages\ListLeaveBalances::route('/'),
            'create' => Pages\CreateLeaveBalance::route('/create'),
            'edit' => Pages\EditLeaveBalance::route('/{record}/edit'),
        ];
    }
}
