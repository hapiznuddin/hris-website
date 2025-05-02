<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataUsing(array $data): array
{
    // Jika status disetujui atau ditolak, isi approved_by dengan ID user login
    if (in_array($data['status'], ['approved', 'rejected'])) {
        $data['approved_by'] = Auth::id();
    }

    return $data;
}
}
