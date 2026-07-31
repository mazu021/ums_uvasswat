@extends('layouts.app')

@section('title', 'Audit Trail Activity Logs')
@section('header_title', 'Security & Audit Activity Trail')

@section('content')
<div class="space-y-6">

    <div>
        <h3 class="text-xl font-bold text-slate-800">System Activity Audit Trail</h3>
        <p class="text-xs text-slate-500">Security event logging tracking user logons, database mutations, fee approvals, and admin actions.</p>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Timestamp</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Action Description</th>
                        <th class="px-6 py-3">Target Entity</th>
                        <th class="px-6 py-3">IP Address</th>
                        <th class="px-6 py-3">Details / Context</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-700 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $log->user->name ?? 'Guest / System' }}
                                @if($log->user)
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $log->user->email }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-700">{{ $log->action }}</td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $log->model_type ?? 'General' }}</td>
                            <td class="px-6 py-4 font-mono text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-[11px]">
                                {{ $log->details ? json_encode($log->details) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-slate-400">No activity logs captured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
