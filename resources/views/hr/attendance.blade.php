@extends('layouts.app')

@section('title', 'Attendance Tracker')
@section('header_title', 'Employee & Faculty Daily Attendance')

@section('content')
<div class="space-y-6" x-data="{ markModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Daily Attendance Log</h3>
            <p class="text-xs text-slate-500">Track employee check-in, check-out, delays, and absent records.</p>
        </div>
        <button @click="markModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2">
            <i class="fa-solid fa-clock"></i>
            <span>Mark Attendance</span>
        </button>
    </div>

    <!-- Date Picker Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <form method="GET" action="{{ route('hr.attendance.index') }}" class="flex items-center space-x-3">
            <label class="text-xs font-bold text-slate-700">Select Date:</label>
            <input type="date" name="date" value="{{ $date }}" class="px-3 py-1.5 border rounded-lg text-xs font-bold text-slate-800">
            <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white font-bold text-xs rounded-lg">Filter Log</button>
        </form>
        <span class="text-xs font-bold text-slate-500">Log Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Check In</th>
                        <th class="px-6 py-3">Check Out</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $att->employee->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $att->employee->employee_code }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $att->employee->department->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $att->check_in ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $att->check_out ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $att->status === 'late' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $att->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $att->status === 'on_leave' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $att->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-slate-400">No attendance marked for this date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $attendances->links() }}
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div x-show="markModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="markModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Mark Employee Attendance</h4>
                <button @click="markModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.attendance.mark') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Employee</label>
                    <select name="employee_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach(\App\Models\Employee::where('status', 'active')->get() as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Check In Time</label>
                        <input type="time" name="check_in" value="08:00" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Check Out Time</label>
                        <input type="time" name="check_out" value="16:00" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Notes / Remarks</label>
                    <input type="text" name="notes" placeholder="Optional notes" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="markModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Save Record</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
