@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'University Management System')

@section('content')
<div class="space-y-8">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-3 border border-emerald-500/30">
                Active Role: {{ $primaryRole }}
            </span>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Welcome back, {{ $user->name }}!</h1>
            <p class="text-slate-300 text-sm mt-1 max-w-xl">
                University of Veterinary and Animal Sciences, Swat (UVAS Swat) ERP Control Panel.
            </p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10 flex space-x-3">
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-xl shadow-lg transition">
                <i class="fa-solid fa-file-export me-1.5"></i> Export Report
            </a>
        </div>
        <i class="fa-solid fa-paw absolute right-6 -bottom-6 text-slate-800 text-9xl opacity-40"></i>
    </div>

    <!-- Quick Statistics Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Widget 1: Total Students -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Students</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalStudents) }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-arrow-trend-up me-1"></i> DVM & Bio-Sciences
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- Widget 2: Faculty & Staff -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Faculty & Staff</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalEmployees) }}</h3>
                <p class="text-[11px] text-slate-500 font-medium mt-1">
                    {{ $totalFaculty }} Faculty | {{ $totalStaff }} Staff
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>

        <!-- Widget 3: Today's Attendance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Today's Attendance</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($presentToday) }} <span class="text-xs font-normal text-slate-400">/ {{ $totalEmployees }}</span></h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                    {{ $lateToday }} Late | {{ $absentToday }} Absent
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- Widget 4: Financial Balance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Net Reserve</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">Rs. {{ number_format($netBalance, 0) }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-1">
                    Rs. {{ number_format($unpaidFeeAmount, 0) }} Uncollected Fees
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-vault"></i>
            </div>
        </div>
    </div>

    <!-- Analytics Section: Chart + Announcements -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue vs Expense Financial Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Institutional Financial Overview</h3>
                    <p class="text-xs text-slate-500">Monthly Fee Collection vs Operational Expenses (PKR)</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 rounded-lg">Session 2025-2026</span>
            </div>
            <div class="h-72">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Announcements Panel -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center">
                        <i class="fa-solid fa-bullhorn text-emerald-600 me-2"></i> Announcements
                    </h3>
                    <a href="{{ route('announcements.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($announcements as $ann)
                        <div class="p-3.5 bg-slate-50 rounded-xl border-l-4 border-emerald-500 hover:bg-slate-100/80 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                    {{ $ann->target_role }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $ann->published_at ? $ann->published_at->diffForHumans() : 'Recent' }}</span>
                            </div>
                            <h4 class="font-bold text-xs text-slate-800 mt-1.5">{{ $ann->title }}</h4>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $ann->content }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">No announcements published.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Info Note -->
            <div class="mt-6 p-3 bg-navy-900 rounded-xl text-white text-xs flex items-center justify-between">
                <div>
                    <p class="font-bold text-emerald-400">Swat Campus Helpline</p>
                    <p class="text-slate-300 text-[11px]">+92-946-9240401</p>
                </div>
                <i class="fa-solid fa-phone text-xl text-emerald-500"></i>
            </div>
        </div>
    </div>

    <!-- Role-Specific Custom Sections -->
    @if($studentData)
        <div class="bg-emerald-900 text-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-bold text-emerald-300 flex items-center">
                <i class="fa-solid fa-id-card me-2"></i> Student Portal Summary - {{ $studentData->full_name }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-xs">
                <div class="bg-emerald-800/60 p-3.5 rounded-xl">
                    <p class="text-emerald-300 font-semibold">Registration Number</p>
                    <p class="text-base font-bold mt-1">{{ $studentData->registration_number }}</p>
                </div>
                <div class="bg-emerald-800/60 p-3.5 rounded-xl">
                    <p class="text-emerald-300 font-semibold">Department & Semester</p>
                    <p class="text-base font-bold mt-1">{{ $studentData->department->code }} (Sem {{ $studentData->current_semester }})</p>
                </div>
                <div class="bg-emerald-800/60 p-3.5 rounded-xl">
                    <p class="text-emerald-300 font-semibold">Fee Status</p>
                    <p class="text-base font-bold mt-1">
                        @php $latestChallan = $studentData->feeChallans->first(); @endphp
                        {{ $latestChallan ? ucfirst($latestChallan->status) : 'No Challan Issued' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pending Leave Approvals for HR / Admin -->
    @canany(['manage hr', 'manage leaves'])
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Pending Leave Applications</h3>
            <a href="{{ route('hr.leaves.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Manage All Leaves</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Leave Type</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Days</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingLeaves as $leave)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $leave->employee->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $leave->employee->designation }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-700">{{ $leave->leaveType->name }}</td>
                            <td class="px-4 py-3">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 font-bold">{{ $leave->total_days }} Days</td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $leave->reason }}</td>
                            <td class="px-4 py-3">
                                <form action="{{ route('hr.leaves.update-status', $leave->id) }}" method="POST" class="flex space-x-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="approved" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[11px]">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-[11px]">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">No pending leave applications.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcanany

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('financialChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [
                    {
                        label: 'Fee Revenue (PKR)',
                        data: @json($incomeData),
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    },
                    {
                        label: 'Operational Expenses (PKR)',
                        data: @json($expenseData),
                        backgroundColor: '#64748b',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rs. ' + (value / 1000) + 'k'; }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
