@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">New Admission Application</h1>
            <p class="text-sm text-slate-500">UVAS Swat Online Admission Form for Undergraduate & Postgraduate Programs.</p>
        </div>
        <a href="{{ route('admissions.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Back to Directory</a>
    </div>

    <form method="POST" action="{{ route('admissions.store') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf

        <!-- Program & Campus Selection -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">1. Select Program & Campus</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Academic Program *</label>
                    <select name="program_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Program...</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Campus *</label>
                    <select name="campus_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($campuses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->city }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div>
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">2. Personal Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Applicant Name *</label>
                    <input type="text" name="applicant_name" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Father's Name *</label>
                    <input type="text" name="father_name" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">CNIC / B-Form Number *</label>
                    <input type="text" name="cnic" placeholder="15602-xxxxxxx-x" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Mobile / Phone *</label>
                    <input type="text" name="phone" placeholder="+92-300-1234567" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
        </div>

        <!-- Academic Record & Live Merit Score Calculation -->
        <div x-data="{
            matric: 950, matricTotal: 1100,
            inter: 980, interTotal: 1100,
            entry: 85, entryTotal: 100,
            get meritScore() {
                let m = (this.matricTotal > 0) ? (this.matric / this.matricTotal) * 100 : 0;
                let i = (this.interTotal > 0) ? (this.inter / this.interTotal) * 100 : 0;
                let e = (this.entryTotal > 0) ? (this.entry / this.entryTotal) * 100 : 0;
                return ((m * 0.20) + (i * 0.50) + (e * 0.30)).toFixed(2);
            }
        }">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">3. Academic Qualifications</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Matric -->
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-2">Matric / SSC (20% Weight)</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-xs text-slate-500">Obtained Marks</label>
                            <input type="number" step="0.01" name="matric_marks" x-model.number="matric" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Total Marks</label>
                            <input type="number" step="0.01" name="matric_total" x-model.number="matricTotal" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                    </div>
                </div>

                <!-- Intermediate -->
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-2">FSc / HSSC (50% Weight)</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-xs text-slate-500">Obtained Marks</label>
                            <input type="number" step="0.01" name="inter_marks" x-model.number="inter" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Total Marks</label>
                            <input type="number" step="0.01" name="inter_total" x-model.number="interTotal" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                    </div>
                </div>

                <!-- Entry Test -->
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-2">Entry Test (30% Weight)</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="text-xs text-slate-500">Obtained Marks</label>
                            <input type="number" step="0.01" name="entry_test_marks" x-model.number="entry" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Total Marks</label>
                            <input type="number" step="0.01" name="entry_test_total" x-model.number="entryTotal" required class="w-full text-sm rounded-md border-slate-300">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Merit Score Badge -->
            <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Calculated Merit Score</span>
                    <p class="text-xs text-emerald-600">Formula: (Matric % × 20%) + (FSc % × 50%) + (Entry Test % × 30%)</p>
                </div>
                <div class="text-3xl font-extrabold text-emerald-700" x-text="meritScore + '%'"></div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
            <a href="{{ route('admissions.index') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-md">Submit Application</button>
        </div>
    </form>
</div>
@endsection
