<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function mutateFormDataUsing(array $data): array
    {
        if (empty($data['nip'])) {
            $today = Carbon::now()->format('Ymd');
            $count = \App\Models\Employee::whereDate('created_at', Carbon::today())->count() + 1;
            $data['nip'] = $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }
    
        return $data;
    }
}
