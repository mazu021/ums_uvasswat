@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Transport & Bus Routes</h1>
        <p class="text-sm text-slate-500">UVAS Swat Shuttle Fleet & Regional Route Schedule.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Fleet Route Schedule</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ count($routes) }} Active Routes</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">Route Name</th>
                        <th class="px-6 py-3.5">Bus / Vehicle No</th>
                        <th class="px-6 py-3.5">Driver Contact</th>
                        <th class="px-6 py-3.5 text-center">Monthly Fare</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($routes as $r)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $r->route_name }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-emerald-700">{{ $r->vehicle_number }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $r->driver_name }}</div>
                            <div class="text-xs text-slate-500">{{ $r->driver_phone }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-mono font-bold text-slate-900">PKR {{ number_format($r->monthly_fee) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">No transport routes configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
