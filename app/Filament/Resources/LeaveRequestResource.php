<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static ?string $navigationLabel = 'Cuti';
    protected static ?string $pluralLabel = 'Cuti';

    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->label('Nama Pegawai'),

                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Tanggal Mulai'),

                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('Tanggal Selesai'),

                Forms\Components\Select::make('type')
                    ->required()
                    ->label('Jenis Cuti')
                    ->options([
                        'Cuti Tahunan' => 'Cuti Tahunan',
                        'Sakit' => 'Sakit',
                        'Melahirkan' => 'Melahirkan',
                    ]),

                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->label('Alasan Cuti'),

                Forms\Components\FileUpload::make('attachment')
                    ->label('Lampiran (jika ada)')
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required()
                    ->visible(fn(): bool => auth()->user()->role === 'hrd' || auth()->user()->role === 'supervisor'),
            ]);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->searchable()
                    ->label('Nama Pegawai'),

                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->label('Jenis Cuti'),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Mulai'),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('Selesai'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Disetujui',
                        'danger' => 'Ditolak',
                    ])
                    ->label('Status')->searchable(),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->getStateUsing(function ($record) {
                        return $record->approved_by ? $record->user->name : '-';
                    }),
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
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
