<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $perPage = $request->get('per_page', 100);
        $attendances = Attendance::with(['employee.department'])
            ->where('date', $date)
            ->paginate($perPage);

        $employeesWithoutAttendance = Employee::where('status', 'active')
            ->whereDoesntHave('attendances', function ($query) use ($date) {
                $query->where('date', $date);
            })
            ->get();

        return view('hr.attendance', compact('attendances', 'employeesWithoutAttendance', 'date'));
    }

    public function mark(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
            'notes' => 'nullable|string',
        ]);

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            [
                'check_in' => $validated['check_in'] ?? null,
                'check_out' => $validated['check_out'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        AuditService::log('Marked Attendance', 'Attendance', $attendance->id, ['status' => $attendance->status]);

        return back()->with('success', 'Attendance record updated successfully.');
    }
}
