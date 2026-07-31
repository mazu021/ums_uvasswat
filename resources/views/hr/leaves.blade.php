@extends('layouts.app')

@section('title', 'Leave Management')
@section('header_title', 'Employee Leave Applications & Workflow')

@section('content')
<div class="space-y-6" x-data="{ applyModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Leave Applications Registry</h3>
            <p class="text-xs text-slate-500">Track leave balances, submit vacation or sick leave requests, and process approvals.</p>
        </div>
        <button @click="applyModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2">
            <i class="fa-solid fa-plane-departure"></i>
            <span>Apply For Leave</span>
        </button>
    </div>

    <!-- Leave Applications Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Applicant</th>
                        <th class="px-6 py-3">Leave Type</th>
                        <th class="px-6 py-3">Start Date</th>
                        <th class="px-6 py-3">End Date</th>
                        <th class="px-6 py-3">Days</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Approval Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $app->employee->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $app->employee->employee_code }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-800">{{ $app->leaveType->name }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $app->start_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $app->end_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $app->total_days }}</td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $app->reason }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $app->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $app->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $app->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @canany(['manage hr', 'manage leaves'])
                                    @if($app->status === 'pending')
                                        <form action="{{ route('hr.leaves.update-status', $app->id) }}" method="POST" class="inline-flex space-x-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="approved" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px]">Approve</button>
                                            <button type="submit" name="status" value="rejected" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-[10px]">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-400">Processed</span>
                                    @endif
                                @endcanany
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6 text-center text-slate-400">No leave applications submitted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div x-show="applyModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="applyModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Submit Leave Application</h4>
                <button @click="applyModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.leaves.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Leave Type</label>
                    <select name="leave_type_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($leaveTypes as $lt)
                            <option value="{{ $lt->id }}">{{ $lt->name }} (Allowed: {{ $lt->days_allowed }} days)</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">End Date</label>
                        <input type="date" name="end_date" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Reason for Leave</label>
                    <textarea name="reason" rows="3" required placeholder="Detailed reason..." class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="applyModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
