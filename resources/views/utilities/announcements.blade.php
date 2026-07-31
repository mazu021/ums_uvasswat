@extends('layouts.app')

@section('title', 'University Announcements')
@section('header_title', 'Campus Announcements & Official Notifications')

@section('content')
@php
    $currentUser = Auth::user();
    $canManage = $currentUser && (
        $currentUser->hasRole('Super Admin') ||
        $currentUser->hasRole('Director IT') ||
        $currentUser->hasRole('Admin') ||
        $currentUser->hasRole('UVAS SWAT') ||
        in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk'])
    );
@endphp

<div class="space-y-6" x-data="{ createModal: false }">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">University Announcements & Notice Board</h3>
            <p class="text-xs text-slate-500">Official notifications, institutional alerts, and campus news updates.</p>
        </div>
        @if($canManage)
            <button @click="createModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg flex items-center space-x-2 transition">
                <i class="fa-solid fa-bullhorn me-1"></i>
                <span>Publish New Announcement</span>
            </button>
        @endif
    </div>

    <!-- Announcements List -->
    <div class="space-y-4">
        @forelse($announcements as $ann)
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Audience: {{ strtoupper($ann->target_role) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase {{ $ann->priority === 'urgent' ? 'bg-rose-100 text-rose-800 border border-rose-200' : ($ann->priority === 'high' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                            <i class="fa-solid fa-bell me-1"></i> {{ ucfirst($ann->priority) }} Priority
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">
                        <i class="fa-regular fa-clock me-1"></i> {{ $ann->published_at ? $ann->published_at->format('M d, Y • h:i A') : 'Published' }}
                    </span>
                </div>
                <h4 class="font-extrabold text-base text-slate-900 leading-snug">{{ $ann->title }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-normal whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">{{ $ann->content }}</p>

                @if($canManage)
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400 font-medium">Published by: <strong class="text-slate-700">{{ $ann->creator->name ?? 'UVAS Administration' }}</strong></span>
                        <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Remove this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 font-bold text-xs hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-trash-can"></i> Remove Notice
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-400 text-xs space-y-2">
                <i class="fa-solid fa-bullhorn text-3xl text-slate-300 block mb-2"></i>
                <p class="font-bold text-slate-600 text-sm">No Active Announcements</p>
                <p>Check back later for new campus notices and official university updates.</p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $announcements->links() }}
        </div>
    </div>

    <!-- Create Modal (For Admins) -->
    @if($canManage)
        <div x-show="createModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createModal = false">
                <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                    <h4 class="font-bold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-emerald-400"></i>
                        <span>Publish Official Campus Announcement</span>
                    </h4>
                    <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1 uppercase text-[10px]">Notice Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Midterm Examination Schedule Announcement" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 uppercase text-[10px]">Target Audience *</label>
                            <select name="target_role" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                <option value="all">Everyone (All Users)</option>
                                <option value="faculty">Faculty / Teachers Only</option>
                                <option value="student">Students Only</option>
                                <option value="staff">Staff Only</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 uppercase text-[10px]">Priority Level *</label>
                            <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                <option value="normal">Normal</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent Alert</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1 uppercase text-[10px]">Announcement Body & Details *</label>
                        <textarea name="content" rows="4" required placeholder="Write full details, guidelines, or instructions..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 font-medium focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    <div class="pt-3 flex justify-end space-x-2 border-t">
                        <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Publish Notice</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
