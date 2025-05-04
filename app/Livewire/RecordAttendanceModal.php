<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use Livewire\Component;

class RecordAttendanceModal extends Component
{
    public Employee $employee;
    public $clock_in;
    public $clock_out;

    protected $rules = [
        'clock_in' => 'required|date_format:H:i',
        'clock_out' => 'nullable|date_format:H:i',
    ];

    public function mount(Employee $employee)
    {
        $this->employee = $employee;
        $this->clock_in = now()->format('H:i');
        $this->clock_out = null;
    }

    public function submit()
    {
        $this->validate();

        Attendance::updateOrCreate(
            [
                'employee_id' => $this->employee->id,
                'date' => now()->toDateString(),
            ],
            [
                'clock_in' => $this->clock_in,
                'clock_out' => $this->clock_out,
            ]
        );

        session()->flash('message', 'Absensi berhasil dicatat.');

        $this->dispatchBrowserEvent('close-modal');
    }


    public function render()
    {
        return view('livewire.record-attendance-modal');
    }
}
