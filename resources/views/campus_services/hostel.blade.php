@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Hostel Allocation Management</h1>
        <p class="text-sm text-slate-500">Student Residence Hall Allotments & Room Capacity Directory.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($hostels as $h)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $h->name }}</h2>
                    <p class="text-xs text-slate-500">Warden: <strong class="text-slate-700">{{ $h->warden_name ?? 'N/A' }}</strong></p>
                </div>
                <span class="px-3 py-1 text-xs font-extrabold rounded-full {{ $h->type == 'boys' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                    {{ strtoupper($h->type) }} HOSTEL
                </span>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-2 bg-slate-50 rounded-lg">
                    <span class="text-slate-400 font-bold block">Capacity</span>
                    <strong class="text-sm text-slate-800">{{ $h->capacity }} Beds</strong>
                </div>
                <div class="p-2 bg-slate-50 rounded-lg">
                    <span class="text-slate-400 font-bold block">Total Rooms</span>
                    <strong class="text-sm text-slate-800">{{ count($h->rooms) }} Rooms</strong>
                </div>
                <div class="p-2 bg-slate-50 rounded-lg">
                    <span class="text-slate-400 font-bold block">Status</span>
                    <strong class="text-sm text-emerald-600">Active</strong>
                </div>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Rooms Directory</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($h->rooms as $room)
                    <div class="p-2 border border-slate-200 rounded-lg text-center text-xs">
                        <div class="font-bold text-slate-900">Room {{ $room->room_number }}</div>
                        <div class="text-[10px] text-slate-500">{{ $room->occupied }}/{{ $room->capacity }} Occupied</div>
                        <div class="text-[10px] text-emerald-700 font-bold">PKR {{ number_format($room->monthly_fee) }}/mo</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
