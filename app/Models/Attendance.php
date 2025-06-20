<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $guarded = [];

protected $casts = [
    'date' => 'date',
    'clock_in' => 'string',
    'clock_out' => 'string',
];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
