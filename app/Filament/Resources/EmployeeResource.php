<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = 'Data Pegawai';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->label('NIP')
                    ->default(fn() => 'Akan diisi otomatis')
                    ->disabled()
                    ->dehydrated(false), // Nilainya tetap dikirim ke server
                Forms\Components\TextInput::make('name')->required()->label('Nama Pegawai'),
                Forms\Components\TextInput::make('nik')->required()->label('Nomor Induk Kependudukan'),
                Forms\Components\TextInput::make('phone')->required()->label('Nomor Telepon')->numeric()->inputMode('decimal'),
                Forms\Components\Select::make('gender')->required()->label('Jenis Kelamin')->options([
                    'Laki-laki' => 'Laki-laki',
                    'Perempuan' => 'Perempuan',
                ]),
                Forms\Components\Select::make('religion')->required()->label('Agama')->options([
                    'Islam' => 'Islam',
                    'Kristen' => 'Kristen',
                    'Hindu' => 'Hindu',
                    'Budha' => 'Budha',
                    'Konghucu' => 'Konghucu',
                ]),
                Forms\Components\TextInput::make('place_of_birth')->required()->label('Tempat Lahir'),
                Forms\Components\DatePicker::make('birth_date')->required()->label('Tanggal Lahir'),
                Forms\Components\TextInput::make('email')->required()->email()->label('Email'),
                Forms\Components\TextInput::make('position')->required()->label('Jabatan'),
                Forms\Components\TextInput::make('department')->required()->label('Departemen'),
                Forms\Components\Select::make('marital_status')->required()->label('Status Perkawinan')->options([
                    'Menikah' => 'Menikah',
                    'Belum Menikah' => 'Belum Menikah',
                ]),
                Forms\Components\TextArea::make('address')->required()->label('Alamat'),
                Forms\Components\Select::make('status')->required()->label('Status Pegawai')->options([
                    'Aktif' => 'Aktif',
                    'Tidak Aktif' => 'Tidak Aktif',
                ]),
                Forms\Components\DatePicker::make('join_date')->required()->label('Tanggal Bergabung'),
                Forms\Components\FileUpload::make('photo')->required()->label('Foto Pegawai')->image()->imageEditor()->imageEditorMode(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->label('NIP')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('photo')->label('Foto Pegawai'),
                Tables\Columns\TextColumn::make('name')->label('Nama Pegawai')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('position')->label('Jabatan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department')->label('Departemen')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('join_date')->label('Tanggal Bergabung')->sortable()->searchable(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
