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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')->required()
                    ->unique(),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('nik')->required(),
                Forms\Components\TextInput::make('phone')->required(),
                Forms\Components\TextInput::make('gender')->required(),
                Forms\Components\TextInput::make('religion')->required(),
                Forms\Components\TextInput::make('place_of_birth')->required(),
                Forms\Components\DatePicker::make('birth_date')->required(),
                Forms\Components\TextInput::make('email')->required(),
                Forms\Components\TextInput::make('position')->required(),
                Forms\Components\TextInput::make('department')->required(),
                Forms\Components\TextInput::make('marital_status')->required(),
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\TextInput::make('photo')->required(),
                Forms\Components\TextInput::make('url_photo')->required(),
                Forms\Components\TextInput::make('status')->required(),
                Forms\Components\DatePicker::make('join_date')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('photo')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('position')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('join_date')->sortable()->searchable(),
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
