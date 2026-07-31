@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Scholarships & Financial Aid</h1>
            <p class="text-sm text-slate-500">HEC Need-Based, Merit Scholarships & UVAS Swat Endowment Fund Portal.</p>
        </div>
        <button onclick="document.getElementById('awardModal').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center space-x-2">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            <span>Award New Scholarship</span>
        </button>
    </div>

    <!-- Scholarships Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Awarded Financial Aid Directory</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ $scholarships->total() }} Records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">Student Details</th>
                        <th class="px-6 py-3.5">Scholarship Title</th>
                        <th class="px-6 py-3.5">Sponsoring Agency</th>
                        <th class="px-6 py-3.5 text-center">Discount %</th>
                        <th class="px-6 py-3.5 text-right">Awarded Amount</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($scholarships as $sch)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $sch->student->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-500">Reg #: {{ $sch->student->registration_number ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $sch->title }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $sch->sponsor_name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-emerald-700">{{ $sch->discount_percentage }}%</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-900">PKR {{ number_format($sch->awarded_amount) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                {{ strtoupper($sch->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">No scholarship records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $scholarships->links() }}
        </div>
    </div>
</div>

<!-- Award Modal -->
<div id="awardModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="font-bold text-slate-800 text-lg">Award Scholarship to Student</h3>
            <button onclick="document.getElementById('awardModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('finance.scholarships.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Select Student *</label>
                <select name="student_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
                    <option value="">Select Student...</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}">{{ $st->user->name ?? 'Student' }} ({{ $st->registration_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Scholarship Title *</label>
                <input type="text" name="title" placeholder="e.g. HEC Need Based Scholarship" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Sponsoring Agency *</label>
                <input type="text" name="sponsor_name" value="HEC Pakistan / UVAS Swat" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Fee Discount % *</label>
                    <input type="number" step="0.01" name="discount_percentage" value="50" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Award Amount (PKR) *</label>
                    <input type="number" step="0.01" name="awarded_amount" value="35000" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
                </div>
            </div>
            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('awardModal').classList.add('hidden')" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow">Award Scholarship</button>
            </div>
        </form>
    </div>
</div>
@endsection
