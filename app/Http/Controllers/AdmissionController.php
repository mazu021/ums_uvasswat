<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\Campus;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = AdmissionApplication::with(['program', 'campus']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('application_no', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 100);
        $applications = $query->orderBy('merit_score', 'desc')->paginate($perPage);
        $programs = Program::where('status', 'active')->orderBy('name')->get();
        $campuses = Campus::all();

        return view('admissions.index', compact('applications', 'programs', 'campuses'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        return view('admissions.apply', compact('programs', 'campuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'cnic' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'program_id' => 'required|exists:programs,id',
            'campus_id' => 'required|exists:campuses,id',
            'matric_marks' => 'required|numeric|min:0',
            'matric_total' => 'required|numeric|min:1',
            'inter_marks' => 'required|numeric|min:0',
            'inter_total' => 'required|numeric|min:1',
            'entry_test_marks' => 'required|numeric|min:0',
            'entry_test_total' => 'required|numeric|min:1',
        ]);

        $application = new AdmissionApplication($validated);
        $application->application_no = 'APP-UVAS-' . strtoupper(Str::random(6));
        $application->merit_score = $application->calculateMeritScore();
        $application->status = 'submitted';
        $application->save();

        return redirect()->route('admissions.index')->with('success', "Application {$application->application_no} submitted successfully with Merit Score: {$application->merit_score}%");
    }

    public function updateStatus(Request $request, AdmissionApplication $application)
    {
        $request->validate([
            'status' => 'required|in:submitted,under_review,fee_pending,approved,enrolled,rejected',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $application) {
            $application->update([
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            // If status changed to enrolled, automatically create Student profile & User account
            if ($request->status === 'enrolled') {
                $user = User::firstOrCreate(
                    ['email' => $application->email],
                    [
                        'name' => $application->applicant_name,
                        'password' => bcrypt('Student123!'),
                        'status' => 'active',
                        'phone' => $application->phone,
                    ]
                );
                $user->assignRole('Student');

                $regNo = 'UVAS-' . date('Y') . '-' . rand(1000, 9999);
                Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'registration_number' => $regNo,
                        'roll_number' => 'RL-' . rand(100, 999),
                        'department_id' => $application->program->department_id ?? 1,
                        'cnic' => $application->cnic,
                        'father_name' => $application->father_name,
                        'status' => 'active',
                        'admission_date' => now()->format('Y-m-d'),
                    ]
                );
            }
        });

        return back()->with('success', "Application {$application->application_no} status updated to " . ucfirst($request->status));
    }

    public function meritList(Request $request)
    {
        $programId = $request->input('program_id');
        $programs = Program::all();

        $applications = AdmissionApplication::with(['program', 'campus'])
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->orderBy('merit_score', 'desc')
            ->get();

        return view('admissions.merit_list', compact('applications', 'programs', 'programId'));
    }

    /**
     * API Webhook: Sync Admitted Students from external portal (admission.uvasswat.edu.pk)
     */
    public function syncAdmittedStudent(Request $request)
    {
        $expectedToken = config('app.admission_api_token', 'uvas_swat_secret_sync_token_2026');
        $token = $request->header('X-API-TOKEN') ?? $request->input('api_token');

        if ($token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized API Access Token',
            ], 401);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'cnic' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $programCode = $request->input('program_code', 'DVM');
                $program = Program::where('code', $programCode)->first() 
                           ?? Program::where('name', 'like', "%{$programCode}%")->first()
                           ?? Program::first();

                // 1. Admission Application
                $application = AdmissionApplication::updateOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'application_no' => $request->input('application_no') ?? ('APP-UVAS-' . strtoupper(Str::random(6))),
                        'applicant_name' => $request->input('applicant_name'),
                        'father_name' => $request->input('father_name'),
                        'cnic' => $request->input('cnic'),
                        'phone' => $request->input('phone'),
                        'program_id' => $program->id ?? 1,
                        'campus_id' => 1,
                        'matric_marks' => $request->input('matric_marks', 900),
                        'matric_total' => $request->input('matric_total', 1100),
                        'inter_marks' => $request->input('inter_marks', 950),
                        'inter_total' => $request->input('inter_total', 1100),
                        'entry_test_marks' => $request->input('entry_test_marks', 80),
                        'entry_test_total' => $request->input('entry_test_total', 100),
                        'merit_score' => $request->input('merit_score', 88.50),
                        'status' => 'enrolled',
                        'remarks' => 'Auto-synced from admission.uvasswat.edu.pk',
                    ]
                );

                // 2. User Account
                $user = User::firstOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'name' => $request->input('applicant_name'),
                        'password' => bcrypt('Student123!'),
                        'status' => 'active',
                        'phone' => $request->input('phone'),
                    ]
                );

                if (!$user->hasRole('Student')) {
                    $user->assignRole('Student');
                }

                // 3. Student Record
                $regNo = '2026-UVAS-' . ($program->code ?? 'DVM') . '-' . sprintf('%03d', rand(100, 999));
                $rollNo = ($program->code ?? 'DVM') . '-26-' . sprintf('%02d', rand(1, 99));

                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $program->department_id ?? 1,
                        'registration_number' => $regNo,
                        'roll_number' => $rollNo,
                        'first_name' => Str::before($request->input('applicant_name'), ' '),
                        'last_name' => Str::after($request->input('applicant_name'), ' ') ?: 'Student',
                        'father_name' => $request->input('father_name'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'cnic' => $request->input('cnic'),
                        'gender' => $request->input('gender', 'male'),
                        'dob' => $request->input('dob', '2004-01-01'),
                        'address' => $request->input('address', 'Swat, KP'),
                        'admission_date' => now()->format('Y-m-d'),
                        'current_semester' => 1,
                        'status' => 'active',
                    ]
                );

                // 4. Generate 1st Semester Fee Challan
                $feeStructure = \App\Models\FeeStructure::where('department_id', $student->department_id)->first()
                             ?? \App\Models\FeeStructure::firstOrCreate(
                                    ['department_id' => $student->department_id, 'semester' => 1],
                                    [
                                        'tuition_fee' => 45000.00,
                                        'admission_fee' => 5000.00,
                                        'examination_fee' => 3000.00,
                                        'library_fee' => 2000.00,
                                        'other_charges' => 0.00,
                                        'total_amount' => 55000.00,
                                    ]
                                );

                $feeChallan = \App\Models\FeeChallan::firstOrCreate(
                    ['student_id' => $student->id],
                    [
                        'fee_structure_id' => $feeStructure->id,
                        'challan_number' => 'CH-2026-' . sprintf('%04d', rand(1000, 9999)),
                        'issue_date' => now()->format('Y-m-d'),
                        'due_date' => now()->addDays(20)->format('Y-m-d'),
                        'total_amount' => $feeStructure->total_amount ?? 55000.00,
                        'paid_amount' => 0.00,
                        'status' => 'unpaid',
                    ]
                );

                return [
                    'user_id' => $user->id,
                    'student_id' => $student->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'registration_number' => $student->registration_number,
                    'roll_number' => $student->roll_number,
                    'challan_number' => $feeChallan->challan_number,
                    'initial_password' => 'Student123!',
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Student successfully admitted and synchronized with UVAS Swat UMS system.',
                'data' => $result,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

