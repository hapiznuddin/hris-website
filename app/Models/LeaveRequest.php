<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
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

    protected static function booted(): void
    {
        static::updated(function ($leaveRequest) {
            // Cek apakah status berubah menjadi 'Disetujui' DAN sebelumnya bukan 'Disetujui'
            if (
                $leaveRequest->isDirty('status') &&
                $leaveRequest->getOriginal('status') !== 'Disetujui' &&
                $leaveRequest->status === 'Disetujui'
            ) {
                $employee = $leaveRequest->employee;

                if (!$employee) {
                    return;
                }

                // Ambil data leave balance berdasarkan tahun ini
                $leaveBalance = $employee->leaveBalance()->first();

                if ($leaveBalance) {
                    // Hitung jumlah hari cuti (termasuk awal & akhir)
                    $days = $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1;

                    $leaveBalance->used_leaves += $days;

                    // (Opsional) kalau kamu juga ingin mengurangi total_leaves:
                    // $leaveBalance->total_leaves -= $days;

                    $leaveBalance->save();
                }
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
