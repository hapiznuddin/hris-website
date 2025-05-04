<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        $currentUser = Auth::user();
        $record = $this->getRecord();

        // Jika user login adalah supervisor dan bukan diri sendiri
        $isSupervisorEditingOther = $currentUser->role === 'supervisor' && $currentUser->id !== $record->id;

        return $form
            ->schema([
                // Info Profil - Disabled jika supervisor
                Forms\Components\TextInput::make('name')
                    ->disabled($isSupervisorEditingOther),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->disabled($isSupervisorEditingOther),

                // Field yang bisa diedit oleh supervisor
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'supervisor' => 'Supervisor',
                        'hrd' => 'HRD',
                        'dev' => 'Developer',
                        'karyawan' => 'Karyawan',
                    ])
                    ->required()
                    ->disabled(fn(): bool => Auth::user()->role !== 'supervisor'),

                Forms\Components\Actions::make([
                    Action::make('resetPassword')
                        ->label('Reset Password')
                        ->color('danger')
                        ->action(function (array $data) {
                            $this->record->update([
                                'password' => Hash::make('12345678'),
                            ]);

                            // Flash message
                            session()->flash('password_reset_success', 'Password berhasil direset ke 12345678');
                        })
                        ->requiresConfirmation()
                        // ->visible(fn(): bool => Auth::user()->role === 'hrd' || Auth::user()->role === 'supervisor'),
                ]),
            ]);
    }
}
