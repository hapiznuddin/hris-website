<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveRequest) {
            // Jika status approved/rejected dan user login adalah HRD/Manager
            if (in_array($leaveRequest->status, ['Disetujui', 'Ditolak']) && Auth::check()) {
                $leaveRequest->approved_by = Auth::id();
            }
        });
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
