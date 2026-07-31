@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Veterinary Clinical & Lab Inventory</h1>
        <p class="text-sm text-slate-500">Diagnostic Equipment, Clinical Tools & Calibration Tracking.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Diagnostic Asset Registry</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ $equipment->total() }} Assets</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">Asset Code</th>
                        <th class="px-6 py-3.5">Equipment Name</th>
                        <th class="px-6 py-3.5">Department</th>
                        <th class="px-6 py-3.5 text-center">Quantity</th>
                        <th class="px-6 py-3.5 text-center">Condition</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($equipment as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-700">{{ $item->asset_code }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $item->department->name ?? 'Clinical Teaching Hospital' }}</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badge = match($item->condition) {
                                    'working' => 'bg-emerald-100 text-emerald-800',
                                    'under_maintenance' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-rose-100 text-rose-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                {{ strtoupper(str_replace('_', ' ', $item->condition)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No laboratory equipment registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $equipment->links() }}
        </div>
    </div>
</div>
@endsection
