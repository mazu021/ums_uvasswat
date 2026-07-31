<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Faculty;
use App\Models\FeeChallan;
use App\Models\FeeStructure;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LedgerEntry;
use App\Models\Payroll;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UVASSwatHierarchySeeder::class);

        $password = Hash::make('Password123!');

        // 1. Create Core Users
        $adminUser = User::firstOrCreate(['email' => 'admin@uvasswat.edu.pk'], [
            'name' => 'Super Admin (UVAS Swat)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-946-9240401',
            'last_login_at' => now(),
        ]);
        if (!$adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole('Super Admin');
        }

        $hrUser = User::firstOrCreate(['email' => 'hr@uvasswat.edu.pk'], [
            'name' => 'Ms. Ayesha Khan (HR Manager)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-946-9240402',
            'last_login_at' => now()->subHours(3),
        ]);
        if (!$hrUser->hasRole('HR Manager')) {
            $hrUser->assignRole('HR Manager');
        }

        $financeUser = User::firstOrCreate(['email' => 'finance@uvasswat.edu.pk'], [
            'name' => 'Mr. Shahabuddin (Finance Officer)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-946-9240403',
            'last_login_at' => now()->subHours(1),
        ]);
        if (!$financeUser->hasRole('Finance Officer')) {
            $financeUser->assignRole('Finance Officer');
        }

        $hodUser = User::firstOrCreate(['email' => 'hod@uvasswat.edu.pk'], [
            'name' => 'Dr. Tariq Ahmad (HOD DVM)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-946-9240404',
            'last_login_at' => now()->subHours(5),
        ]);
        if (!$hodUser->hasRole('HOD')) {
            $hodUser->assignRole('HOD');
        }

        $facultyUser = User::firstOrCreate(['email' => 'faculty@uvasswat.edu.pk'], [
            'name' => 'Dr. Yasir Ali (Assistant Professor)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-333-9876543',
            'last_login_at' => now()->subDay(),
        ]);
        if (!$facultyUser->hasRole('Faculty')) {
            $facultyUser->assignRole('Faculty');
        }

        $etisamsUser = User::firstOrCreate(['name' => 'Dr. Etisam Khan'], [
            'email' => 'etisams@uvasswat.edu.pk',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-333-1122334',
            'last_login_at' => now(),
        ]);
        if (!$etisamsUser->hasRole('Faculty')) {
            $etisamsUser->assignRole('Faculty');
        }

        $studentUser = User::firstOrCreate(['email' => 'student@uvasswat.edu.pk'], [
            'name' => 'Muhammad Zubair (DVM Student)',
            'password' => $password,
            'status' => 'active',
            'phone' => '+92-300-1234567',
            'last_login_at' => now()->subHours(2),
        ]);
        if (!$studentUser->hasRole('Student')) {
            $studentUser->assignRole('Student');
        }

        // 2. Faculties & Departments
        $fvs = Faculty::firstOrCreate(['code' => 'FVS'], [
            'name' => 'Faculty of Veterinary Sciences',
            'description' => 'Leading faculty for clinical veterinary studies and animal health.',
            'dean_name' => 'Prof. Dr. Muhammad Shah',
        ]);

        $fahs = Faculty::firstOrCreate(['code' => 'FAHS'], [
            'name' => 'Faculty of Allied Health Sciences',
            'description' => 'Faculty dedicated to physical therapy, medical lab technology, dental technology, and imaging.',
            'dean_name' => 'Prof. Dr. Jan Alam',
        ]);

        $fs = Faculty::firstOrCreate(['code' => 'FS'], [
            'name' => 'Faculty of Sciences',
            'description' => 'Advanced studies in artificial intelligence, computer science, biochemistry, and zoology.',
            'dean_name' => 'Prof. Dr. Shaheen Begum',
        ]);

        $fass = Faculty::firstOrCreate(['code' => 'FASS'], [
            'name' => 'Faculty of Arts and Social Science',
            'description' => 'Humanities, management sciences, psychology, sociology, and English literature.',
            'dean_name' => 'Prof. Dr. Tariq Ahmad',
        ]);

        $deptDvm = Department::firstOrCreate(['code' => 'DCS'], [
            'faculty_id' => $fvs->id,
            'name' => 'Department of Clinical Studies',
            'hod_name' => 'Dr. Tariq Ahmad',
            'description' => 'Clinical diagnosis, treatment, and surgery of animals.',
        ]);

        $deptAbg = Department::firstOrCreate(['code' => 'LPM'], [
            'faculty_id' => $fvs->id,
            'name' => 'Department of Livestock Production and Management',
            'hod_name' => 'Dr. Ihsanullah Khan',
            'description' => 'Livestock breeding and management.',
        ]);

        $deptPs = Department::firstOrCreate(['code' => 'PB'], [
            'faculty_id' => $fvs->id,
            'name' => 'Department of Pathobiology',
            'hod_name' => 'Dr. Yasir Ali',
            'description' => 'Pathobiology, microbiology, and disease diagnosis.',
        ]);

        // 3. Staff & Faculty Employees
        $emp1 = Employee::create([
            'user_id' => $hodUser->id,
            'department_id' => $deptDvm->id,
            'employee_code' => 'EMP-UVAS-001',
            'first_name' => 'Tariq',
            'last_name' => 'Ahmad',
            'email' => 'hod@uvasswat.edu.pk',
            'phone' => '+92-946-9240404',
            'cnic' => '15602-1234567-1',
            'designation' => 'Associate Professor & HOD',
            'type' => 'faculty',
            'basic_salary' => 185000.00,
            'joining_date' => '2021-03-15',
            'status' => 'active',
        ]);

        $emp2 = Employee::create([
            'user_id' => $facultyUser->id,
            'department_id' => $deptPs->id,
            'employee_code' => 'EMP-UVAS-002',
            'first_name' => 'Yasir',
            'last_name' => 'Ali',
            'email' => 'faculty@uvasswat.edu.pk',
            'phone' => '+92-333-9876543',
            'cnic' => '15602-7654321-3',
            'designation' => 'Assistant Professor',
            'type' => 'faculty',
            'basic_salary' => 140000.00,
            'joining_date' => '2022-08-01',
            'status' => 'active',
        ]);

        $emp3 = Employee::create([
            'user_id' => $hrUser->id,
            'department_id' => $deptDvm->id,
            'employee_code' => 'EMP-UVAS-003',
            'first_name' => 'Ayesha',
            'last_name' => 'Khan',
            'email' => 'hr@uvasswat.edu.pk',
            'phone' => '+92-946-9240402',
            'cnic' => '15602-1122334-5',
            'designation' => 'HR Manager',
            'type' => 'administration',
            'basic_salary' => 110000.00,
            'joining_date' => '2022-01-10',
            'status' => 'active',
        ]);

        $emp4 = Employee::create([
            'user_id' => $financeUser->id,
            'department_id' => $deptDvm->id,
            'employee_code' => 'EMP-UVAS-004',
            'first_name' => 'Shahab',
            'last_name' => 'Uddin',
            'email' => 'finance@uvasswat.edu.pk',
            'phone' => '+92-946-9240403',
            'cnic' => '15602-5566778-9',
            'designation' => 'Finance Officer',
            'type' => 'administration',
            'basic_salary' => 115000.00,
            'joining_date' => '2021-11-20',
            'status' => 'active',
        ]);

        $empEtisam = Employee::create([
            'user_id' => $etisamsUser->id,
            'department_id' => $deptDvm->id,
            'employee_code' => 'EMP-UVAS-005',
            'first_name' => 'Etisam',
            'last_name' => 'Khan',
            'email' => 'etisams@uvasswat.edu.pk',
            'phone' => '+92-333-1122334',
            'cnic' => '15602-9988776-5',
            'designation' => 'Assistant Professor',
            'type' => 'faculty',
            'basic_salary' => 150000.00,
            'joining_date' => '2022-01-01',
            'status' => 'active',
        ]);

        // 4. Attendance Records (Past 5 days)
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            Attendance::create([
                'employee_id' => $emp1->id,
                'date' => $date,
                'check_in' => '08:00:00',
                'check_out' => '16:00:00',
                'status' => 'present',
                'notes' => 'On time',
            ]);
            Attendance::create([
                'employee_id' => $emp2->id,
                'date' => $date,
                'check_in' => $i % 2 == 0 ? '08:15:00' : '08:02:00',
                'check_out' => '16:00:00',
                'status' => $i % 2 == 0 ? 'late' : 'present',
                'notes' => $i % 2 == 0 ? 'Traffic delay in Mingora' : 'Regular',
            ]);
            Attendance::create([
                'employee_id' => $emp3->id,
                'date' => $date,
                'check_in' => '08:05:00',
                'check_out' => '16:00:00',
                'status' => 'present',
                'notes' => 'Regular',
            ]);
        }

        // 5. Leave Types & Applications
        $leaveAnnual = LeaveType::create(['name' => 'Annual Leave', 'days_allowed' => 14, 'description' => 'Paid yearly vacation leave']);
        $leaveSick = LeaveType::create(['name' => 'Sick Leave', 'days_allowed' => 10, 'description' => 'Medical emergency leave']);
        $leaveCasual = LeaveType::create(['name' => 'Casual Leave', 'days_allowed' => 8, 'description' => 'Short personal leave']);

        LeaveApplication::create([
            'employee_id' => $emp2->id,
            'leave_type_id' => $leaveSick->id,
            'start_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
            'total_days' => 3,
            'reason' => 'Attending veterinary medical conference in Peshawar.',
            'status' => 'approved',
            'approved_by' => $adminUser->id,
        ]);

        LeaveApplication::create([
            'employee_id' => $emp1->id,
            'leave_type_id' => $leaveAnnual->id,
            'start_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'total_days' => 5,
            'reason' => 'Family event in Swat valley.',
            'status' => 'pending',
        ]);

        // 6. Payroll Records
        Payroll::create([
            'employee_id' => $emp1->id,
            'payslip_number' => 'PAY-202607-001',
            'month' => 7,
            'year' => 2026,
            'basic_salary' => 185000.00,
            'allowances' => 25000.00,
            'deductions' => 12000.00,
            'net_salary' => 198000.00,
            'payment_status' => 'paid',
            'payment_date' => '2026-07-01',
        ]);

        Payroll::create([
            'employee_id' => $emp2->id,
            'payslip_number' => 'PAY-202607-002',
            'month' => 7,
            'year' => 2026,
            'basic_salary' => 140000.00,
            'allowances' => 18000.00,
            'deductions' => 8000.00,
            'net_salary' => 150000.00,
            'payment_status' => 'paid',
            'payment_date' => '2026-07-01',
        ]);

        // 7. Students Registry
        $std1 = Student::create([
            'user_id' => $studentUser->id,
            'department_id' => $deptDvm->id,
            'registration_number' => '2024-UVAS-DVM-001',
            'roll_number' => 'DVM-24-01',
            'first_name' => 'Muhammad',
            'last_name' => 'Zubair',
            'father_name' => 'Akhtar Ali',
            'email' => 'student@uvasswat.edu.pk',
            'phone' => '+92-300-1234567',
            'cnic' => '15602-9988776-1',
            'gender' => 'male',
            'dob' => '2003-05-14',
            'address' => 'Main Bazaar, Saidu Sharif, Swat',
            'admission_date' => '2024-09-01',
            'current_semester' => 4,
            'status' => 'active',
        ]);

        $std2 = Student::create([
            'department_id' => $deptDvm->id,
            'registration_number' => '2024-UVAS-DVM-002',
            'roll_number' => 'DVM-24-02',
            'first_name' => 'Saima',
            'last_name' => 'Rehman',
            'father_name' => 'Abdur Rehman',
            'email' => 'saima.dvm@uvasswat.edu.pk',
            'phone' => '+92-312-3456789',
            'cnic' => '15602-3344556-2',
            'gender' => 'female',
            'dob' => '2004-02-20',
            'address' => 'Kabir Street, Mingora, Swat',
            'admission_date' => '2024-09-01',
            'current_semester' => 4,
            'status' => 'active',
        ]);

        $std3 = Student::create([
            'department_id' => $deptPs->id,
            'registration_number' => '2024-UVAS-PS-005',
            'roll_number' => 'PS-24-05',
            'first_name' => 'Hamza',
            'last_name' => 'Faruq',
            'father_name' => 'Faruq Shah',
            'email' => 'hamza.ps@uvasswat.edu.pk',
            'phone' => '+92-331-5544332',
            'cnic' => '15602-7788990-3',
            'gender' => 'male',
            'dob' => '2003-11-10',
            'address' => 'Kabal Road, Swat',
            'admission_date' => '2024-09-01',
            'current_semester' => 2,
            'status' => 'active',
        ]);

        // 8. Courses & Assignments
        $c1 = Course::create([
            'department_id' => $deptDvm->id,
            'course_code' => 'VET-401',
            'title' => 'Veterinary Clinical Medicine & Therapeutics',
            'credit_hours' => 4,
            'semester' => 4,
            'description' => 'Comprehensive diagnosis and medical management of livestock diseases.',
        ]);

        $c2 = Course::create([
            'department_id' => $deptDvm->id,
            'course_code' => 'VET-402',
            'title' => 'Veterinary Surgery & Anesthesiology',
            'credit_hours' => 3,
            'semester' => 4,
            'description' => 'Surgical procedures, sterilization, and anesthesia techniques.',
        ]);

        $c3 = Course::create([
            'department_id' => $deptPs->id,
            'course_code' => 'PS-201',
            'title' => 'Commercial Poultry Nutrition & Health',
            'credit_hours' => 3,
            'semester' => 2,
            'description' => 'Ration formulation and bio-security protocols in commercial farms.',
        ]);

        CourseAssignment::create([
            'course_id' => $c1->id,
            'employee_id' => $emp1->id,
            'academic_session' => '2025-2026',
            'semester' => 4,
        ]);

        CourseAssignment::create([
            'course_id' => $c3->id,
            'employee_id' => $emp2->id,
            'academic_session' => '2025-2026',
            'semester' => 2,
        ]);

        // 9. Exams & Marks Entry
        $exam1 = Exam::create([
            'course_id' => $c1->id,
            'title' => 'Mid-Term Examination Spring 2026',
            'exam_date' => '2026-06-15',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'room_no' => 'Auditorium Hall A',
            'total_marks' => 50,
        ]);

        ExamGrade::create([
            'exam_id' => $exam1->id,
            'student_id' => $std1->id,
            'marks_obtained' => 44.50,
            'grade' => 'A',
            'gpa_point' => 3.90,
            'remarks' => 'Excellent performance in clinical case diagnosis.',
        ]);

        ExamGrade::create([
            'exam_id' => $exam1->id,
            'student_id' => $std2->id,
            'marks_obtained' => 41.00,
            'grade' => 'A-',
            'gpa_point' => 3.70,
            'remarks' => 'Good conceptual understanding.',
        ]);

        // 10. Fee Structures & Challans
        $fsDvm = FeeStructure::create([
            'department_id' => $deptDvm->id,
            'semester' => 4,
            'tuition_fee' => 45000.00,
            'admission_fee' => 0.00,
            'examination_fee' => 5000.00,
            'library_fee' => 2000.00,
            'other_charges' => 3000.00,
            'total_amount' => 55000.00,
        ]);

        FeeChallan::create([
            'student_id' => $std1->id,
            'fee_structure_id' => $fsDvm->id,
            'challan_number' => 'CH-2026-0001',
            'issue_date' => '2026-07-01',
            'due_date' => '2026-07-20',
            'total_amount' => 55000.00,
            'paid_amount' => 55000.00,
            'status' => 'paid',
            'paid_at' => '2026-07-10 11:30:00',
        ]);

        FeeChallan::create([
            'student_id' => $std2->id,
            'fee_structure_id' => $fsDvm->id,
            'challan_number' => 'CH-2026-0002',
            'issue_date' => '2026-07-01',
            'due_date' => '2026-07-20',
            'total_amount' => 55000.00,
            'paid_amount' => 0.00,
            'status' => 'overdue',
        ]);

        // 11. General Ledger Accounts
        LedgerEntry::create([
            'entry_type' => 'credit',
            'category' => 'fee_collection',
            'title' => 'Semester Fee Collection - Spring 2026',
            'amount' => 4850000.00,
            'transaction_date' => '2026-07-10',
            'reference_number' => 'BANK-NBP-9821',
            'description' => 'Direct deposit via National Bank of Pakistan Saidu Sharif Branch.',
            'created_by' => $financeUser->id,
        ]);

        LedgerEntry::create([
            'entry_type' => 'debit',
            'category' => 'salary_payment',
            'title' => 'Faculty & Staff Payroll Payout (June 2026)',
            'amount' => 2450000.00,
            'transaction_date' => '2026-07-01',
            'reference_number' => 'SAL-2026-06',
            'description' => 'Monthly salaries disbursement.',
            'created_by' => $financeUser->id,
        ]);

        LedgerEntry::create([
            'entry_type' => 'debit',
            'category' => 'lab_equipment',
            'title' => 'Veterinary Surgery Autoclave & Surgical Tools',
            'amount' => 380000.00,
            'transaction_date' => '2026-07-15',
            'reference_number' => 'PO-2026-044',
            'description' => 'Purchased for DVM Clinical Surgery Operating Theater.',
            'created_by' => $financeUser->id,
        ]);

        // 12. System Announcements
        Announcement::create([
            'title' => 'Welcome to Fall Academic Session 2026 at UVAS Swat',
            'content' => 'Classes for all undergraduate DVM, Poultry Science, and Bio-Sciences programs will commence on September 1st, 2026. All students are advised to clear outstanding dues.',
            'target_role' => 'all',
            'priority' => 'high',
            'is_published' => true,
            'published_at' => now(),
            'created_by' => $adminUser->id,
        ]);

        Announcement::create([
            'title' => 'Faculty Research Grant Proposals Open',
            'content' => 'The Office of Research, Innovation & Commercialization (ORIC) invites faculty members to submit veterinary livestock research projects for HEC funding.',
            'target_role' => 'faculty',
            'priority' => 'normal',
            'is_published' => true,
            'published_at' => now()->subDays(2),
            'created_by' => $adminUser->id,
        ]);

        // 13. System Settings
        SystemSetting::set('university_name', 'The University of Veterinary and Animal Sciences, Swat (UVAS Swat)', 'general');
        SystemSetting::set('university_code', 'UVAS Swat', 'general');
        SystemSetting::set('tagline', 'Excellence in Veterinary Education, Research & Animal Healthcare', 'general');
        SystemSetting::set('vice_chancellor', 'Prof. Dr. Shakirullah', 'general');
        SystemSetting::set('address', 'Kabal Road, Saidu Sharif, Swat, Khyber Pakhtunkhwa, Pakistan', 'general');
        SystemSetting::set('contact_email', 'info@uvasswat.edu.pk', 'general');
        SystemSetting::set('contact_phone', '+92-946-9240401', 'general');
        SystemSetting::set('current_session', '2025-2026', 'academic');

        // 14. Audit Logs
        AuditLog::create([
            'user_id' => $adminUser->id,
            'action' => 'System Initialized',
            'model_type' => 'SystemSetting',
            'details' => ['message' => 'UVAS Swat ERP System seeded with initial configuration.'],
            'ip_address' => '127.0.0.1',
        ]);

        // 15. Admission Applications
        $progDvm = \App\Models\Program::first();
        $campusMain = \App\Models\Campus::first();

        if ($progDvm && $campusMain) {
            \App\Models\AdmissionApplication::create([
                'application_no' => 'APP-UVAS-9901',
                'program_id' => $progDvm->id,
                'campus_id' => $campusMain->id,
                'applicant_name' => 'Muhammad Bilal Khan',
                'cnic' => '15602-9988771-1',
                'father_name' => 'Sher Ali Khan',
                'email' => 'bilal.swat@gmail.com',
                'phone' => '+92-333-9112233',
                'matric_marks' => 990,
                'matric_total' => 1100,
                'inter_marks' => 1020,
                'inter_total' => 1100,
                'entry_test_marks' => 88,
                'entry_test_total' => 100,
                'merit_score' => 90.76,
                'status' => 'submitted',
            ]);

            \App\Models\AdmissionApplication::create([
                'application_no' => 'APP-UVAS-9902',
                'program_id' => $progDvm->id,
                'campus_id' => $campusMain->id,
                'applicant_name' => 'Fatima Bibi',
                'cnic' => '15602-5544332-2',
                'father_name' => 'Gul Zada',
                'email' => 'fatima.swat@gmail.com',
                'phone' => '+92-345-4455667',
                'matric_marks' => 1010,
                'matric_total' => 1100,
                'inter_marks' => 1045,
                'inter_total' => 1100,
                'entry_test_marks' => 92,
                'entry_test_total' => 100,
                'merit_score' => 93.45,
                'status' => 'approved',
            ]);
        }

        // 16. Campus Services Seeds
        \App\Models\Book::firstOrCreate(['isbn' => '978-0-7020-7258-1'], [
            'title' => 'Veterinary Medicine: A textbook of the diseases of cattle, horses, sheep, pigs and goats',
            'author' => 'Peter D. Constable',
            'publisher' => 'Elsevier',
            'category' => 'Clinical Veterinary Medicine',
            'total_copies' => 10,
            'available_copies' => 8,
        ]);

        \App\Models\Book::firstOrCreate(['isbn' => '978-1-118-80153-6'], [
            'title' => 'Dyce, Sack, and Wensing\'s Textbook of Veterinary Anatomy',
            'author' => 'Singh Baljit',
            'publisher' => 'Saunders',
            'category' => 'Veterinary Anatomy',
            'total_copies' => 6,
            'available_copies' => 5,
        ]);

        $hostel1 = \App\Models\Hostel::firstOrCreate(['name' => 'Karakoram Boys Hostel'], [
            'type' => 'boys',
            'warden_name' => 'Dr. Ihsanullah Khan',
            'capacity' => 120,
        ]);

        \App\Models\HostelRoom::firstOrCreate(['hostel_id' => $hostel1->id, 'room_number' => '101'], [
            'capacity' => 3,
            'occupied' => 2,
            'monthly_fee' => 4500,
        ]);

        \App\Models\TransportRoute::firstOrCreate(['route_name' => 'Kanju Main Campus - Mingora Bus Stand'], [
            'vehicle_number' => 'SWAT-BUS-01',
            'driver_name' => 'Bakht Zada',
            'driver_phone' => '+92-301-8877665',
            'monthly_fee' => 3000,
            'stops' => ['Kanju Township', 'Saidu Sharif', 'Mingora Bazaar', 'General Bus Stand'],
        ]);

        \App\Models\LabEquipment::firstOrCreate(['asset_code' => 'EQ-DIAG-001'], [
            'name' => 'Full Automated Veterinary Blood Chemistry Analyzer',
            'category' => 'Diagnostic',
            'department_id' => $deptDvm->id ?? 1,
            'quantity' => 2,
            'condition' => 'working',
            'last_calibrated_at' => now()->subMonths(1)->format('Y-m-d'),
        ]);

        // 17. Course Offering & Attendance Engine Seeds
        $academicSession = \App\Models\AcademicSession::firstOrCreate(['code' => 'FALL-2026'], [
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        $progDpt = \App\Models\Program::firstOrCreate(['code' => 'DPT'], [
            'department_id' => $deptDvm->id,
            'name' => 'Doctor of Physical Therapy (DPT)',
            'degree_level' => 'Undergraduate',
            'total_semesters' => 10,
            'total_credit_hours' => 175,
            'status' => 'active',
        ]);

        $progNursing = \App\Models\Program::firstOrCreate(['code' => 'BSN'], [
            'department_id' => $deptDvm->id,
            'name' => 'BS Nursing',
            'degree_level' => 'Undergraduate',
            'total_semesters' => 8,
            'total_credit_hours' => 135,
            'status' => 'active',
        ]);

        $batchMorning = \App\Models\Batch::firstOrCreate(['code' => 'DPT-F26-M'], [
            'program_id' => $progDpt->id,
            'academic_session_id' => $academicSession->id,
            'name' => 'DPT Fall 2026 Morning Batch',
            'status' => 'active',
        ]);

        $batchEvening = \App\Models\Batch::firstOrCreate(['code' => 'DPT-F26-E'], [
            'program_id' => $progDpt->id,
            'academic_session_id' => $academicSession->id,
            'name' => 'DPT Fall 2026 Evening Batch',
            'status' => 'active',
        ]);

        $batchNursingM = \App\Models\Batch::firstOrCreate(['code' => 'BSN-F26-M'], [
            'program_id' => $progNursing->id,
            'academic_session_id' => $academicSession->id,
            'name' => 'BS Nursing Fall 2026 Morning Batch',
            'status' => 'active',
        ]);

        $secA = \App\Models\Section::firstOrCreate(['name' => 'Section A'], [
            'semester_id' => 1,
            'max_capacity' => 50,
        ]);

        // Link student 1 to DPT program & batch
        $std1->update([
            'program_id' => $progDpt->id,
            'batch_id' => $batchMorning->id,
            'current_semester' => 1,
            'section_id' => $secA->id,
        ]);

        $std2->update([
            'program_id' => $progDpt->id,
            'batch_id' => $batchMorning->id,
            'current_semester' => 1,
            'section_id' => $secA->id,
        ]);

        // Master Courses (Catalog items exist ONLY ONCE in system)
        $courseEng = Course::firstOrCreate(['course_code' => 'ENG-101'], [
            'department_id' => $deptDvm->id,
            'title' => 'Basic English & Functional Writing',
            'credit_hours' => 3,
            'description' => 'Fundamental English communication, writing mechanics, and comprehension.',
        ]);

        $courseCsc = Course::firstOrCreate(['course_code' => 'CSC-201'], [
            'department_id' => $deptDvm->id,
            'title' => 'Programming Fundamentals & Logic',
            'credit_hours' => 4,
            'description' => 'Introduction to algorithms, data types, control structures, and software logic.',
        ]);

        $courseBio = Course::firstOrCreate(['course_code' => 'BIO-101'], [
            'department_id' => $deptDvm->id,
            'title' => 'Biology & Fundamentals',
            'credit_hours' => 3,
            'description' => 'Cellular biology, human anatomy, and physiological systems.',
        ]);

        $courseAnat1 = Course::firstOrCreate(['course_code' => 'ANAT-101'], [
            'department_id' => $deptDvm->id,
            'title' => 'Anatomy-I',
            'credit_hours' => 3,
            'description' => 'Gross human anatomy and musculoskeletal systems.',
        ]);

        $coursePhys1 = Course::firstOrCreate(['course_code' => 'PHYS-101'], [
            'department_id' => $deptDvm->id,
            'title' => 'Physiology-I',
            'credit_hours' => 3,
            'description' => 'Human organ functions and physiological regulation.',
        ]);

        $courseAnat2 = Course::firstOrCreate(['course_code' => 'ANAT-102'], [
            'department_id' => $deptDvm->id,
            'title' => 'Anatomy-II',
            'credit_hours' => 3,
            'description' => 'Neuroanatomy and visceral organ anatomy.',
        ]);

        $courseNurs1 = Course::firstOrCreate(['course_code' => 'NURS-101'], [
            'department_id' => $deptDvm->id,
            'title' => 'Nursing Foundation',
            'credit_hours' => 3,
            'description' => 'Principles and practices of patient care and nursing foundation.',
        ]);

        // Seed Curriculums / Study Schemes
        $curriculumDpt = \App\Models\Curriculum::firstOrCreate([
            'program_id' => $progDpt->id,
        ], [
            'name' => 'Doctor of Physical Therapy (DPT) Study Scheme 2026-2031',
            'code' => 'SCHEME-DPT-2026',
            'effective_year' => 2026,
            'total_semesters' => 10,
            'total_credit_hours' => 135,
            'status' => 'active',
        ]);

        $curriculumNurs = \App\Models\Curriculum::firstOrCreate([
            'program_id' => $progNursing->id,
        ], [
            'name' => 'BS Nursing (BSN) Study Scheme 2026-2030',
            'code' => 'SCHEME-BSN-2026',
            'effective_year' => 2026,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // Map courses to DPT Curriculum
        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumDpt->id,
            'course_id' => $courseAnat1->id,
            'semester_number' => 1,
        ], ['course_type' => 'core', 'credit_hours' => 3]);

        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumDpt->id,
            'course_id' => $coursePhys1->id,
            'semester_number' => 1,
        ], ['course_type' => 'core', 'credit_hours' => 3]);

        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumDpt->id,
            'course_id' => $courseEng->id,
            'semester_number' => 1,
        ], ['course_type' => 'general', 'credit_hours' => 3]);

        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumDpt->id,
            'course_id' => $courseAnat2->id,
            'semester_number' => 2,
        ], ['course_type' => 'core', 'credit_hours' => 3]);

        // Map courses to BS Nursing Curriculum
        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumNurs->id,
            'course_id' => $courseEng->id,
            'semester_number' => 1,
        ], ['course_type' => 'general', 'credit_hours' => 3]);

        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumNurs->id,
            'course_id' => $courseBio->id,
            'semester_number' => 1,
        ], ['course_type' => 'core', 'credit_hours' => 3]);

        \App\Models\CurriculumCourse::firstOrCreate([
            'curriculum_id' => $curriculumNurs->id,
            'course_id' => $courseNurs1->id,
            'semester_number' => 1,
        ], ['course_type' => 'core', 'credit_hours' => 3]);

        // Offering 1: Dr. Etisam Khan | DPT Morning | ENG-101
        $offering1 = \App\Models\CourseOffering::firstOrCreate([
            'course_id' => $courseEng->id,
            'program_id' => $progDpt->id,
            'batch_id' => $batchMorning->id,
            'academic_session_id' => $academicSession->id,
            'teacher_id' => $etisamsUser->id,
        ], [
            'semester_number' => 1,
            'section_id' => $secA->id,
            'status' => 'active',
        ]);

        // Offering 2: Dr. Yasir | DPT Evening | ENG-101
        $offering2 = \App\Models\CourseOffering::firstOrCreate([
            'course_id' => $courseEng->id,
            'program_id' => $progDpt->id,
            'batch_id' => $batchEvening->id,
            'academic_session_id' => $academicSession->id,
            'teacher_id' => $facultyUser->id,
        ], [
            'semester_number' => 1,
            'status' => 'active',
        ]);

        // Offering 3: Dr. Yasir | BS Nursing Morning | BIO-101
        $offering3 = \App\Models\CourseOffering::firstOrCreate([
            'course_id' => $courseBio->id,
            'program_id' => $progNursing->id,
            'batch_id' => $batchNursingM->id,
            'academic_session_id' => $academicSession->id,
            'teacher_id' => $facultyUser->id,
        ], [
            'semester_number' => 1,
            'status' => 'active',
        ]);

        // Seed Sample Attendance Session & Records for Offering 1
        $sess1 = \App\Models\AttendanceSession::firstOrCreate([
            'course_offering_id' => $offering1->id,
            'attendance_date' => now()->subDay()->format('Y-m-d'),
            'lecture_number' => 1,
        ], [
            'topic' => 'Introduction to Grammar & Noun Clauses',
            'remarks' => 'Full class attendance',
            'created_by' => $hodUser->id,
        ]);

        \App\Models\AttendanceRecord::firstOrCreate([
            'attendance_session_id' => $sess1->id,
            'student_id' => $std1->id,
        ], [
            'status' => 'Present',
        ]);

        \App\Models\AttendanceRecord::firstOrCreate([
            'attendance_session_id' => $sess1->id,
            'student_id' => $std2->id,
        ], [
            'status' => 'Present',
        ]);
    }
}
