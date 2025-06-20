<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->nip)) {
                $today = Carbon::now()->format('Ymd');
                $count = self::whereDate('created_at', Carbon::today())->count() + 1;
                $employee->nip = $today . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getRemainingLeavesAttribute()
    {
        $balance = $this->leaveBalance()->first();
        return $balance ? $balance->total_leaves - $balance->used_leaves : 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaveBalance()
    {
        return $this->hasOne(LeaveBalance::class)->where('year', now()->year);
    }
}
