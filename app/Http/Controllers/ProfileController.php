<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        $employee = Employee::with('department')
            ->where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $student = Student::with(['department', 'program', 'batch'])
            ->where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        return view('profile.show', compact('user', 'employee', 'student'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'address' => 'nullable|string|max:500',
            'cnic' => 'nullable|string|max:30',
            'father_name' => 'nullable|string|max:255',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Handle Password Update if requested
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account password.'])->withInput();
            }
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();

        // Sync with Employee profile if exists
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        if ($employee) {
            $nameParts = explode(' ', $validated['name'], 2);
            $employee->update([
                'first_name' => $nameParts[0] ?? $validated['name'],
                'last_name' => $nameParts[1] ?? '',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $employee->phone,
                'cnic' => $validated['cnic'] ?? $employee->cnic,
            ]);
        }

        // Sync with Student profile if exists
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        if ($student) {
            $nameParts = explode(' ', $validated['name'], 2);
            $student->update([
                'first_name' => $nameParts[0] ?? $validated['name'],
                'last_name' => $nameParts[1] ?? '',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $student->phone,
                'cnic' => $validated['cnic'] ?? $student->cnic,
                'father_name' => $validated['father_name'] ?? $student->father_name,
                'address' => $validated['address'] ?? $student->address,
            ]);
        }

        AuditService::log('Updated User Profile', 'User', $user->id);

        return back()->with('success', 'Your profile details have been updated successfully.');
    }
}
