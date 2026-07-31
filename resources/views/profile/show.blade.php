@extends('layouts.app')

@section('title', 'My User Profile')
@section('header_title', 'User Account & Profile Settings')

@section('content')
<div class="space-y-6">

    <!-- Single Profile Form Wrapper -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile_update_form">
        @csrf
        @method('PUT')

        <!-- Profile Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 rounded-3xl p-6 lg:p-8 text-white shadow-xl relative overflow-hidden mb-6">
            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
                
                <!-- Avatar Display & Live Preview Uploader -->
                <div class="relative group">
                    <div id="avatar_header_container">
                        @if($user->avatar)
                            <img id="avatar_header_img" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 lg:w-28 lg:h-28 rounded-full object-cover ring-4 ring-white/20 shadow-2xl">
                        @else
                            <div id="avatar_header_initials" class="w-24 h-24 lg:w-28 lg:h-28 rounded-full bg-emerald-600 ring-4 ring-white/20 shadow-2xl flex items-center justify-center text-3xl font-extrabold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <label for="avatar_file_input" class="absolute bottom-0 right-0 p-2.5 bg-emerald-500 hover:bg-emerald-400 text-white rounded-full shadow-lg cursor-pointer transition transform hover:scale-110" title="Upload New Profile Photo">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </label>
                </div>

                <!-- Basic Info Header -->
                <div class="text-center md:text-left flex-1 space-y-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">{{ $user->name }}</h1>
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs rounded-full">
                            {{ $user->getRoleNames()->first() ?? 'ERP User' }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 flex items-center justify-center md:justify-start gap-2">
                        <i class="fa-regular fa-envelope text-emerald-400"></i>
                        <span>{{ $user->email }}</span>
                        @if($user->phone)
                            <span class="mx-1">•</span>
                            <i class="fa-solid fa-phone text-emerald-400"></i>
                            <span>{{ $user->phone }}</span>
                        @endif
                    </p>

                    <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3 text-[11px] text-slate-400">
                        <span class="bg-white/10 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-university text-indigo-300 me-1"></i>
                            UVAS Swat ERP System
                        </span>
                        @if($employee)
                            <span class="bg-white/10 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-id-badge text-indigo-300 me-1"></i>
                                Emp Code: {{ $employee->employee_code }}
                            </span>
                            <span class="bg-white/10 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-sitemap text-indigo-300 me-1"></i>
                                {{ $employee->department->name ?? 'General Dept' }}
                            </span>
                        @elseif($student)
                            <span class="bg-white/10 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-id-card text-indigo-300 me-1"></i>
                                Reg: {{ $student->registration_number }}
                            </span>
                            <span class="bg-white/10 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-graduation-cap text-indigo-300 me-1"></i>
                                {{ $student->program->name ?? ($student->department->name ?? 'Degree Program') }}
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            <i class="fa-solid fa-user-gear absolute -right-6 -bottom-6 text-white text-9xl opacity-5"></i>
        </div>

        <!-- Main Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Sidebar: Account Summary Cards -->
            <div class="space-y-6">

                <!-- System Role Card -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Account Profile Overview</span>
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Account Status:</span>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-md text-[10px]">Active</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Primary Role:</span>
                            <span class="font-bold text-slate-800">{{ $user->getRoleNames()->first() ?? 'ERP User' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Member Since:</span>
                            <span class="font-medium text-slate-700">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Profile Photo:</span>
                            <span class="font-bold {{ $user->avatar ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $user->avatar ? 'Uploaded & Active' : 'Default Badge' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Employee or Student Details Card -->
                @if($employee)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 space-y-4">
                        <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span>Staff / Faculty Details</span>
                            <i class="fa-solid fa-user-tie text-blue-600"></i>
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Designation:</span>
                                <span class="font-bold text-slate-800">{{ $employee->designation ?? 'Faculty Member' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Department:</span>
                                <span class="font-semibold text-slate-700">{{ $employee->department->name ?? 'N/A' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Employee Code:</span>
                                <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">{{ $employee->employee_code }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">CNIC:</span>
                                <span class="font-medium text-slate-700">{{ $employee->cnic ?? 'Not Recorded' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Joining Date:</span>
                                <span class="font-medium text-slate-700">{{ $employee->joining_date ? $employee->joining_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($student)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 space-y-4">
                        <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span>Academic Registry Details</span>
                            <i class="fa-solid fa-graduation-cap text-purple-600"></i>
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Registration No:</span>
                                <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">{{ $student->registration_number }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Roll Number:</span>
                                <span class="font-bold text-slate-800">{{ $student->roll_number ?? 'N/A' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Degree Program:</span>
                                <span class="font-semibold text-slate-700 text-right max-w-[160px] truncate">{{ $student->program->name ?? ($student->department->name ?? 'N/A') }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Current Semester:</span>
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 font-bold rounded">Semester {{ $student->current_semester }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Father's Name:</span>
                                <span class="font-medium text-slate-700">{{ $student->father_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Main Column: Editable Profile & Password Form -->
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-6">

                    <!-- Section 1: Basic Profile Info -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-user-pen text-emerald-600"></i>
                            Personal & Contact Details
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">Update your personal contact details and profile picture.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            
                            <!-- Full Name -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs font-semibold transition">
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs font-semibold transition">
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Phone / Mobile Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? ($employee->phone ?? ($student->phone ?? ''))) }}" placeholder="+92 300 1234567" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">
                            </div>

                            <!-- CNIC Number -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">CNIC Number</label>
                                <input type="text" name="cnic" value="{{ old('cnic', $employee->cnic ?? ($student->cnic ?? '')) }}" placeholder="15602-1234567-1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">
                            </div>

                            @if($student)
                                <!-- Father's Name -->
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Father's Name</label>
                                    <input type="text" name="father_name" value="{{ old('father_name', $student->father_name ?? '') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">
                                </div>
                            @endif

                            <!-- Profile Photo Upload -->
                            <div class="@if(!$student) md:col-span-2 @endif">
                                <label class="block font-bold text-slate-700 mb-1">Upload Profile Avatar (JPG, PNG, WEBP, GIF)</label>
                                <input type="file" id="avatar_file_input" name="avatar" accept="image/*" onchange="previewAvatarAndSubmit(this)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 transition cursor-pointer">
                            </div>

                            <!-- Residential Address -->
                            <div class="md:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Residential Address</label>
                                <textarea name="address" rows="2" placeholder="Enter complete home / hostel address..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">{{ old('address', $student->address ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>

                    <!-- Section 2: Security & Password Update -->
                    <div class="pt-6 border-t border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-lock text-amber-600"></i>
                            Security & Password Settings
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">Leave blank if you do not wish to change your account password.</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Current Password</label>
                                <input type="password" name="current_password" placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:bg-white text-xs transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">New Password</label>
                                <input type="password" name="new_password" placeholder="Min 8 characters" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:bg-white text-xs transition">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" placeholder="Repeat new password" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:bg-white text-xs transition">
                            </div>

                        </div>
                    </div>

                    <!-- Form Submit Action -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                        <button type="reset" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Reset Changes
                        </button>
                        <button type="submit" id="profile_form_submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Save Profile Changes</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    function previewAvatarAndSubmit(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var container = document.getElementById('avatar_header_container');
                if (container) {
                    container.innerHTML = '<img id="avatar_header_img" src="' + e.target.result + '" alt="Profile Preview" class="w-24 h-24 lg:w-28 lg:h-28 rounded-full object-cover ring-4 ring-emerald-400/50 shadow-2xl animate-fade-in">';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
