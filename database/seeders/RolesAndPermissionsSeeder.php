<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Comprehensive Permissions List
        $permissions = [
            // Core System & RBAC
            'manage system',
            'manage settings',
            'manage users',
            'manage roles',
            'view audit logs',
            'view reports',

            // Campus & Hierarchy
            'manage campuses',
            'manage faculties',
            'manage departments',
            'manage programs',
            'manage batches',
            'manage semesters',
            'manage sections',

            // Academic & SIS
            'manage admissions',
            'manage students',
            'manage courses',
            'manage timetables',
            'manage attendance',
            'manage lms',
            'manage assignments',
            'manage quizzes',

            // Exams & Grading
            'manage exams',
            'submit grades',
            'approve grades',
            'issue transcripts',
            'manage graduation',

            // Finance & Accounts
            'manage fee structures',
            'manage fee challans',
            'verify fee proofs',
            'manage accounts',
            'manage scholarships',

            // HR & Payroll
            'manage hr',
            'manage employees',
            'manage leaves',
            'manage payroll',
            'approve payroll',

            // Campus Services
            'manage library',
            'manage hostel',
            'manage transport',
            'manage inventory',
            'manage procurement',
            'manage assets',
            'manage lab equipment',

            // Research & Clinical
            'manage research',
            'manage thesis',
            'manage clinical rotations',

            // Student Services & Auxiliaries
            'manage placement',
            'manage alumni',
            'manage complaints',
            'manage quality assurance',
            'manage visitors',
            'manage security logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. The 35 Enterprise Roles
        $allRoles = [
            'Super Admin',
            'University Admin',
            'Campus Admin',
            'Registrar',
            'Admission Officer',
            'Finance Officer',
            'Accountant',
            'HR Manager',
            'HR Officer',
            'Payroll Officer',
            'Controller of Examination',
            'Dean',
            'Head of Department (HOD)',
            'HOD',
            'Program Coordinator',
            'Semester Coordinator',
            'Course Coordinator',
            'Teacher / Lecturer',
            'Faculty',
            'Lab Instructor',
            'Clinical Supervisor',
            'Research Director',
            'Research Supervisor',
            'Librarian',
            'Hostel Warden',
            'Transport Manager',
            'Store Keeper',
            'Procurement Officer',
            'Quality Assurance Officer',
            'IT Support',
            'Student',
            'Parent / Guardian',
            'Alumni',
            'Visitor',
            'Security Officer',
            'Placement Officer',
            'System Auditor',
        ];

        foreach ($allRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Super Admin receives all permissions
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->syncPermissions(Permission::all());

        // University Admin receives all management permissions
        $uniAdmin = Role::findByName('University Admin');
        $uniAdmin->syncPermissions(Permission::all());

        // Assign Specific Permissions to Key Roles
        $rolePermissionsMap = [
            'Campus Admin' => ['manage campuses', 'manage users', 'manage faculties', 'manage departments', 'manage programs', 'view reports'],
            'Registrar' => ['manage admissions', 'manage students', 'manage programs', 'manage batches', 'manage semesters', 'issue transcripts', 'view reports'],
            'Admission Officer' => ['manage admissions', 'manage students', 'view reports'],
            'Finance Officer' => ['manage fee structures', 'manage fee challans', 'verify fee proofs', 'manage accounts', 'manage scholarships', 'view reports'],
            'Accountant' => ['manage fee challans', 'verify fee proofs', 'manage accounts'],
            'HR Manager' => ['manage hr', 'manage employees', 'manage leaves', 'manage payroll', 'approve payroll', 'view reports'],
            'HR Officer' => ['manage employees', 'manage leaves'],
            'Payroll Officer' => ['manage payroll', 'approve payroll'],
            'Controller of Examination' => ['manage exams', 'approve grades', 'issue transcripts', 'manage graduation', 'view reports'],
            'Dean' => ['manage faculties', 'manage departments', 'manage programs', 'approve grades', 'view reports'],
            'Head of Department (HOD)' => ['manage departments', 'manage programs', 'manage courses', 'manage attendance', 'approve grades', 'view reports'],
            'HOD' => ['manage departments', 'manage programs', 'manage courses', 'manage attendance', 'approve grades', 'view reports'],
            'Program Coordinator' => ['manage programs', 'manage batches', 'manage courses', 'view reports'],
            'Semester Coordinator' => ['manage semesters', 'manage sections', 'manage timetables'],
            'Course Coordinator' => ['manage courses', 'manage assignments', 'manage quizzes'],
            'Teacher / Lecturer' => ['manage attendance', 'manage lms', 'manage assignments', 'manage quizzes', 'submit grades'],
            'Faculty' => ['manage attendance', 'manage lms', 'manage assignments', 'manage quizzes', 'submit grades'],
            'Lab Instructor' => ['manage attendance', 'manage lab equipment'],
            'Clinical Supervisor' => ['manage clinical rotations', 'submit grades'],
            'Research Director' => ['manage research', 'manage thesis', 'view reports'],
            'Research Supervisor' => ['manage thesis'],
            'Librarian' => ['manage library'],
            'Hostel Warden' => ['manage hostel'],
            'Transport Manager' => ['manage transport'],
            'Store Keeper' => ['manage inventory', 'manage lab equipment'],
            'Procurement Officer' => ['manage procurement', 'manage assets'],
            'Quality Assurance Officer' => ['manage quality assurance', 'view reports'],
            'IT Support' => ['manage settings', 'manage users', 'view audit logs'],
            'Security Officer' => ['manage visitors', 'manage security logs'],
            'Placement Officer' => ['manage placement', 'manage alumni'],
            'System Auditor' => ['view audit logs', 'view reports'],
        ];

        foreach ($rolePermissionsMap as $roleName => $perms) {
            $role = Role::findByName($roleName);
            if ($role) {
                $role->syncPermissions($perms);
            }
        }
    }
}
