<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LeaveBalance extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canView(Model $record): bool
    {
        if (auth()->user()->isAdmin())
            return true;

        return $record->employee_id === auth()->user()->employee->id;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
