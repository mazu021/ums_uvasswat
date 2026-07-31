@extends('layouts.app')

@section('title', 'System Settings')
@section('header_title', 'Institutional Profile & System Settings')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <h3 class="text-xl font-bold text-slate-800">UVAS Swat Institutional Configuration</h3>
            <p class="text-xs text-slate-500">Configure university profile, vice chancellor information, contact info, and active academic session.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: General Institutional Profile -->
            <div class="space-y-4 border-t pt-4">
                <h4 class="font-bold text-sm text-slate-900 flex items-center">
                    <i class="fa-solid fa-university me-2 text-emerald-600"></i> Institutional Profile
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">University Full Name</label>
                        <input type="text" name="university_name" value="{{ $settings['university_name'] ?? 'The University of Veterinary and Animal Sciences, Swat (UVAS Swat)' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Short Code / Abbreviation</label>
                        <input type="text" name="university_code" value="{{ $settings['university_code'] ?? 'UVAS Swat' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Institutional Motto / Tagline</label>
                    <input type="text" name="tagline" value="{{ $settings['tagline'] ?? 'Excellence in Veterinary Education, Research & Animal Healthcare' }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vice Chancellor Name</label>
                        <input type="text" name="vice_chancellor" value="{{ $settings['vice_chancellor'] ?? 'Prof. Dr. Shakirullah' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Current Academic Session</label>
                        <input type="text" name="current_session" value="{{ $settings['current_session'] ?? '2025-2026' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact Information -->
            <div class="space-y-4 border-t pt-4">
                <h4 class="font-bold text-sm text-slate-900 flex items-center">
                    <i class="fa-solid fa-address-book me-2 text-emerald-600"></i> Campus Address & Contact Info
                </h4>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Campus Physical Address</label>
                    <input type="text" name="address" value="{{ $settings['address'] ?? 'Kabal Road, Saidu Sharif, Swat, KP, Pakistan' }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Official Contact Email</label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'info@uvasswat.edu.pk' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Official Helpline Phone</label>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+92-946-9240401' }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
