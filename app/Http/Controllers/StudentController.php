<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $departmentId = $request->get('department_id');
        $semesterFilter = $request->get('semester');

        $perPage = $request->get('per_page', 100);
        $students = Student::with('department')
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%");
            })
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where('department_id', $departmentId);
            })
            ->when($semesterFilter, function ($query, $semesterFilter) {
                return $query->where('current_semester', $semesterFilter);
            })
            ->latest()
            ->paginate($perPage);

        $departments = Department::all();

        return view('academics.students', compact('students', 'departments', 'search', 'departmentId', 'semesterFilter'));
    }

    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => "{$validated['first_name']} {$validated['last_name']}",
            'email' => $validated['email'],
            'password' => Hash::make('Password123!'),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
        ]);

        $user->assignRole('Student');

        $validated['user_id'] = $user->id;
        $student = Student::create($validated);

        AuditService::log('Admitted New Student', 'Student', $student->id, ['reg' => $student->registration_number]);

        return back()->with('success', 'Student enrolled successfully. Credentials generated.');
    }

    public function show(Student $student)
    {
        $student->load(['department', 'examGrades.exam.course', 'feeChallans']);
        return view('academics.student_show', compact('student'));
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="uvas_students_import_template.csv"',
        ];

        $columns = [
            'S.No', 'Full Name', 'Father Name', 'CNIC', 'Phone No', 'Father Phone',
            'Gender', 'DOB', 'Nationality', 'Religion', 'Domicile',
            'SSC Total', 'SSC Obtained', 'HSSC Total', 'HSSC Obtained', 'Merit %', 'Deparment'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Sample Data Rows
            fputcsv($file, [
                '1', 'Muhammad Bilal Khan', 'Sher Ali Khan', '15602-9988771-1', '+92-333-9112233', '+92-333-9000001',
                'Male', '2004-05-14', 'Pakistani', 'Islam', 'Swat',
                '1100', '990', '1100', '1020', '90.76', 'Department of Clinical Studies'
            ]);

            fputcsv($file, [
                '2', 'Fatima Bibi', 'Gul Zada', '15602-5544332-2', '+92-345-4455667', '+92-345-4400002',
                'Female', '2004-02-20', 'Pakistani', 'Islam', 'Swat',
                '1100', '1010', '1100', '1045', '93.45', 'Department of Artificial Intelligence'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240',
        ]);

        $file = $request->file('excel_file') ?? $request->files->get('excel_file');
        if (!$file) {
            return back()->withErrors(['excel_file' => 'Please select a valid Excel or CSV file.']);
        }
        
        $path = $file->getRealPath();
        if (!$path || !file_exists($path)) {
            $path = $file->getPathname();
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['excel_file' => 'Unable to read the uploaded file.']);
        }

        $header = fgetcsv($handle, 2000, ',');
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['excel_file' => 'Invalid or empty CSV file.']);
        }

        // Normalize header strings
        $cleanHeader = array_map(function ($item) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9%]/', '', $item)));
        }, $header);

        $allDepartments = Department::all();
        $importedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                $rowData = [];
                foreach ($cleanHeader as $index => $key) {
                    $rowData[$key] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                // Extract fields
                $fullName = $rowData['fullname'] ?? $rowData['name'] ?? 'Student';
                $fatherName = $rowData['fathername'] ?? '';
                $cnic = !empty($rowData['cnic']) ? $rowData['cnic'] : ('15602-' . rand(1000000, 9999999) . '-' . rand(1, 9));
                $phone = !empty($rowData['phoneno']) ? $rowData['phoneno'] : (!empty($rowData['phone']) ? $rowData['phone'] : '+92-300-' . rand(1000000, 9999999));
                $gender = strtolower($rowData['gender'] ?? 'male') === 'female' ? 'female' : 'male';
                $dob = !empty($rowData['dob']) ? date('Y-m-d', strtotime($rowData['dob'])) : '2004-01-01';
                $domicile = $rowData['domicile'] ?? 'Swat';
                $nationality = $rowData['nationality'] ?? 'Pakistani';

                $sscTotal = floatval($rowData['ssctotal'] ?? 1100);
                $sscObtained = floatval($rowData['sscobtained'] ?? 900);
                $hsscTotal = floatval($rowData['hssctotal'] ?? 1100);
                $hsscObtained = floatval($rowData['hsscobtained'] ?? 950);
                $meritScore = floatval($rowData['merit%'] ?? $rowData['merit'] ?? 85.00);
                $deptInput = $rowData['deparment'] ?? $rowData['department'] ?? '';

                // Match Department
                $department = null;
                if (!empty($deptInput)) {
                    $department = $allDepartments->first(function ($d) use ($deptInput) {
                        return strcasecmp($d->name, $deptInput) === 0 || 
                               strcasecmp($d->code, $deptInput) === 0 || 
                               stripos($d->name, $deptInput) !== false;
                    });
                }
                if (!$department) {
                    $department = $allDepartments->first();
                }

                // Generate Deterministic Unique Email & User Account
                $cleanCnic = preg_replace('/[^0-9]/', '', $cnic);
                if (empty($cleanCnic)) {
                    $cleanCnic = strtoupper(\Illuminate\Support\Str::random(8));
                }
                
                $email = !empty($rowData['email']) ? strtolower($rowData['email']) : ("std.{$cleanCnic}@uvasswat.edu.pk");

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $fullName,
                        'password' => Hash::make('Student123!'),
                        'phone' => $phone,
                        'status' => 'active',
                    ]
                );

                if (!$user->hasRole('Student')) {
                    $user->assignRole('Student');
                }

                // Names
                $firstName = \Illuminate\Support\Str::before($fullName, ' ');
                $lastName = \Illuminate\Support\Str::after($fullName, ' ') ?: 'Student';

                // Student Record (updateOrCreate by user_id to avoid unique email crash)
                $existingStudent = Student::where('user_id', $user->id)->orWhere('cnic', $cnic)->first();
                
                if ($existingStudent && $existingStudent->registration_number) {
                    $regNo = $existingStudent->registration_number;
                } else {
                    $regNo = date('Y') . '-UVAS-' . ($department->code ?? 'DVM') . '-' . sprintf('%04d', rand(100, 9999));
                    while (Student::where('registration_number', $regNo)->exists()) {
                        $regNo = date('Y') . '-UVAS-' . ($department->code ?? 'DVM') . '-' . sprintf('%04d', rand(100, 9999));
                    }
                }

                if ($existingStudent && $existingStudent->roll_number) {
                    $rollNo = $existingStudent->roll_number;
                } else {
                    $rollNo = ($department->code ?? 'DVM') . '-26-' . sprintf('%03d', rand(1, 999));
                    while (Student::where('roll_number', $rollNo)->exists()) {
                        $rollNo = ($department->code ?? 'DVM') . '-26-' . sprintf('%03d', rand(1, 999));
                    }
                }

                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $department->id,
                        'registration_number' => $regNo,
                        'roll_number' => $rollNo,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'father_name' => $fatherName,
                        'email' => $email,
                        'phone' => $phone,
                        'cnic' => $cnic,
                        'gender' => $gender,
                        'dob' => $dob,
                        'address' => "Domicile: {$domicile}, {$nationality}",
                        'admission_date' => now()->format('Y-m-d'),
                        'current_semester' => 1,
                        'status' => 'active',
                    ]
                );

                // Dynamic Campus & Program Resolution
                $campus = \App\Models\Campus::first() ?? \App\Models\Campus::create([
                    'code' => 'MAIN-SWAT',
                    'name' => 'Main Campus Swat',
                    'city' => 'Swat',
                    'address' => 'Ghabragee, Swat, KPK',
                    'phone' => '+92-946-9240404',
                    'email' => 'info@uvasswat.edu.pk',
                    'is_main' => true,
                    'status' => 'active',
                ]);

                $program = \App\Models\Program::where('department_id', $department->id)->first()
                        ?? \App\Models\Program::first()
                        ?? \App\Models\Program::create([
                            'department_id' => $department->id,
                            'code' => $department->code ?? 'GEN',
                            'name' => 'Degree Program in ' . $department->name,
                            'degree_level' => 'Undergraduate',
                            'duration_years' => 4,
                            'total_semesters' => 8,
                            'total_credit_hours' => 130,
                            'status' => 'active',
                        ]);

                // Admission Application Record (updateOrCreate by email)
                \App\Models\AdmissionApplication::updateOrCreate(
                    ['email' => $email],
                    [
                        'application_no' => 'APP-EXCEL-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'program_id' => $program->id,
                        'campus_id' => $campus->id,
                        'applicant_name' => $fullName,
                        'father_name' => $fatherName,
                        'cnic' => $cnic,
                        'email' => $email,
                        'phone' => $phone,
                        'matric_marks' => $sscObtained,
                        'matric_total' => $sscTotal,
                        'inter_marks' => $hsscObtained,
                        'inter_total' => $hsscTotal,
                        'entry_test_marks' => 80,
                        'entry_test_total' => 100,
                        'merit_score' => $meritScore,
                        'status' => 'enrolled',
                        'remarks' => "Bulk Excel Import ({$department->name})",
                    ]
                );

                // 1st Semester Fee Challan
                $feeStructure = \App\Models\FeeStructure::where('department_id', $department->id)->first()
                             ?? \App\Models\FeeStructure::firstOrCreate(
                                    ['department_id' => $department->id, 'semester' => 1],
                                    [
                                        'tuition_fee' => 45000.00,
                                        'admission_fee' => 5000.00,
                                        'examination_fee' => 3000.00,
                                        'library_fee' => 2000.00,
                                        'other_charges' => 0.00,
                                        'total_amount' => 55000.00,
                                    ]
                                );

                $existingChallan = \App\Models\FeeChallan::where('student_id', $student->id)->first();
                if (!$existingChallan) {
                    $challanNo = 'CH-' . date('Y') . '-' . sprintf('%06d', $student->id + rand(100, 999));
                    while (\App\Models\FeeChallan::where('challan_number', $challanNo)->exists()) {
                        $challanNo = 'CH-' . date('Y') . '-' . sprintf('%06d', rand(100000, 999999));
                    }

                    \App\Models\FeeChallan::create([
                        'student_id' => $student->id,
                        'fee_structure_id' => $feeStructure->id,
                        'challan_number' => $challanNo,
                        'issue_date' => now()->format('Y-m-d'),
                        'due_date' => now()->addDays(20)->format('Y-m-d'),
                        'total_amount' => $feeStructure->total_amount,
                        'paid_amount' => 0.00,
                        'status' => 'unpaid',
                    ]);
                }

                $importedCount++;
            }

            \Illuminate\Support\Facades\DB::commit();
            fclose($handle);

            AuditService::log('Bulk Excel Import', 'Student', null, ['count' => $importedCount]);

            return back()->with('success', "Excel Sheet Analyzed & Processed Successfully! Imported {$importedCount} students across departments.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            return back()->withErrors(['excel_file' => 'Import Error: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'registration_number' => 'required|string|max:255|unique:students,registration_number,' . $student->id,
            'roll_number' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:50',
            'cnic' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'current_semester' => 'required|integer|min:1|max:12',
            'gender' => 'required|in:male,female',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $student->update($validated);

        if ($student->user) {
            $student->user->update([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $student->user->phone,
            ]);
        }

        AuditService::log('Updated Student Profile', 'Student', $student->id, ['reg' => $student->registration_number]);

        return back()->with('success', "Student {$student->full_name} updated successfully.");
    }

    public function destroy(Student $student)
    {
        $name = $student->full_name;
        $reg = $student->registration_number;

        if ($student->user) {
            $student->user->delete();
        }
        $student->delete();

        AuditService::log('Deleted Student', 'Student', null, ['reg' => $reg]);

        return back()->with('success', "Student {$name} ({$reg}) deleted successfully.");
    }

    public function promotionView(Request $request)
    {
        $departments = Department::all();
        return view('academics.students_promotion', compact('departments'));
    }

    public function getBatchStudents(Request $request)
    {
        $deptId = $request->input('department_id');
        $semester = $request->input('current_semester');

        $query = Student::where('status', 'active');

        if (!empty($deptId)) {
            $query->where('department_id', $deptId);
        }
        if (!empty($semester)) {
            $query->where('current_semester', $semester);
        }

        $students = $query->with('department:id,name,code')
            ->get(['id', 'first_name', 'last_name', 'registration_number', 'roll_number', 'department_id', 'current_semester']);

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    public function promoteBatch(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'target_semester' => 'required|integer|min:1|max:12',
        ]);

        $studentIds = $request->input('student_ids');
        $targetSemester = $request->input('target_semester');

        $updatedCount = Student::whereIn('id', $studentIds)->update([
            'current_semester' => $targetSemester
        ]);

        AuditService::log('Promoted Student Batch', 'Student', null, ['count' => $updatedCount, 'target_semester' => $targetSemester]);

        return back()->with('success', "Batch Promotion Complete! Successfully promoted {$updatedCount} students to Semester {$targetSemester}.");
    }

    public function transferView(Request $request)
    {
        $students = Student::with('department')->orderBy('first_name')->get();
        $departments = Department::all();
        
        $transferLogs = \App\Models\AuditLog::where('action', 'Student Department Transfer')
            ->latest()
            ->take(50)
            ->get();

        return view('academics.students_transfer', compact('students', 'departments', 'transferLogs'));
    }

    public function processTransfer(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'target_department_id' => 'required|exists:departments,id',
            'target_semester' => 'required|integer|min:1|max:12',
            'reason' => 'nullable|string|max:500',
            'noc_number' => 'nullable|string|max:100',
        ]);

        $student = Student::findOrFail($request->student_id);
        $oldDept = $student->department->name ?? 'None';
        $targetDept = Department::findOrFail($request->target_department_id);

        if ($student->department_id == $targetDept->id && $student->current_semester == $request->target_semester) {
            return back()->with('error', 'Student is already enrolled in ' . $targetDept->name . ' Semester ' . $request->target_semester);
        }

        $student->update([
            'department_id' => $targetDept->id,
            'current_semester' => $request->target_semester,
        ]);

        AuditService::log('Student Department Transfer', 'Student', $student->id, [
            'student_name' => $student->full_name,
            'reg_no' => $student->registration_number,
            'from_department' => $oldDept,
            'to_department' => $targetDept->name,
            'new_semester' => $request->target_semester,
            'reason' => $request->reason ?? 'Department Migration',
            'noc_number' => $request->noc_number ?? 'N/A',
        ]);

        return back()->with('success', "Transfer Completed! {$student->full_name} ({$student->registration_number}) successfully transferred to {$targetDept->name} (Semester {$request->target_semester}).");
    }
}

