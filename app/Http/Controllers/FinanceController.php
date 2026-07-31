<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeChallanRequest;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\FeeChallan;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\Student;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $programs = Program::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();

        // Query Fee Structures
        $feeStructuresQuery = FeeStructure::with(['department', 'program', 'academicSession']);
        if ($request->filled('filter_session_id')) {
            $feeStructuresQuery->where('academic_session_id', $request->filter_session_id);
        }
        if ($request->filled('filter_dept_id')) {
            $feeStructuresQuery->where('department_id', $request->filter_dept_id);
        }
        if ($request->filled('filter_prog_id')) {
            $feeStructuresQuery->where('program_id', $request->filter_prog_id);
        }
        $feeStructures = $feeStructuresQuery->orderBy('academic_session_id', 'desc')->latest()->get();

        // Query Student Fee Challans
        $challansQuery = FeeChallan::with(['student.department', 'student.program', 'feeStructure', 'verifier']);

        if ($request->filled('status')) {
            $challansQuery->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $challansQuery->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('program_id')) {
            $challansQuery->whereHas('student', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $challansQuery->where(function ($q) use ($search) {
                $q->where('challan_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('roll_number', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 100);
        $challans = $challansQuery->latest()->paginate($perPage);
        $students = Student::where('status', 'active')->orderBy('roll_number')->get();

        return view('finance.fees', compact(
            'challans',
            'feeStructures',
            'departments',
            'programs',
            'academicSessions',
            'students'
        ));
    }

    public function storeStructure(Request $request)
    {
        if ($request->input('program_id') === 'all') {
            $request->merge(['apply_scope' => 'all_programs', 'program_id' => null]);
        }
        if ($request->input('department_id') === 'all') {
            $request->merge(['apply_scope' => 'all_departments', 'department_id' => null]);
        }

        $validated = $request->validate([
            'apply_scope' => 'required|in:department,program,all_programs,all_departments',
            'department_id' => 'required_if:apply_scope,department|nullable',
            'program_id' => 'required_if:apply_scope,program|nullable',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester' => 'required|integer|min:0|max:10',
            'tuition_fee' => 'required|numeric|min:0',
            'admission_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'late_fee_fine' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $tuition = (float) $validated['tuition_fee'];
        $admission = (float) ($validated['admission_fee'] ?? 0);
        $exam = (float) ($validated['examination_fee'] ?? 0);
        $library = (float) ($validated['library_fee'] ?? 0);
        $other = (float) ($validated['other_charges'] ?? 0);
        $fine = (float) ($validated['late_fee_fine'] ?? 0);

        $total = $tuition + $admission + $exam + $library + $other;
        $dueDate = !empty($validated['due_date']) ? Carbon::parse($validated['due_date']) : Carbon::today()->addDays(30);
        $sessionId = $validated['academic_session_id'];

        if ($validated['apply_scope'] === 'all_programs') {
            $progs = Program::where('status', 'active')->get();
            $count = 0;
            foreach ($progs as $prog) {
                $fs = FeeStructure::updateOrCreate(
                    [
                        'academic_session_id' => $sessionId,
                        'program_id' => $prog->id,
                        'semester' => $validated['semester'],
                    ],
                    [
                        'department_id' => $prog->department_id,
                        'tuition_fee' => $tuition,
                        'admission_fee' => $admission,
                        'examination_fee' => $exam,
                        'library_fee' => $library,
                        'other_charges' => $other,
                        'late_fee_fine' => $fine,
                        'total_amount' => $total,
                    ]
                );
                $count += $this->allocateChallansForStructure($fs, $dueDate);
            }
            $msg = "Fee structure saved for All Active Degree Programs in selected session and allocated to {$count} students.";
        } elseif ($validated['apply_scope'] === 'all_departments') {
            $depts = Department::all();
            $count = 0;
            foreach ($depts as $dept) {
                $fs = FeeStructure::updateOrCreate(
                    [
                        'academic_session_id' => $sessionId,
                        'department_id' => $dept->id,
                        'program_id' => null,
                        'semester' => $validated['semester'],
                    ],
                    [
                        'tuition_fee' => $tuition,
                        'admission_fee' => $admission,
                        'examination_fee' => $exam,
                        'library_fee' => $library,
                        'other_charges' => $other,
                        'late_fee_fine' => $fine,
                        'total_amount' => $total,
                    ]
                );
                $count += $this->allocateChallansForStructure($fs, $dueDate);
            }
            $msg = "Fee structure saved globally for All Departments and allocated to {$count} students.";
        } elseif ($validated['apply_scope'] === 'program') {
            $prog = Program::findOrFail($validated['program_id']);
            $fs = FeeStructure::updateOrCreate(
                [
                    'academic_session_id' => $sessionId,
                    'program_id' => $prog->id,
                    'semester' => $validated['semester'],
                ],
                [
                    'department_id' => $prog->department_id,
                    'tuition_fee' => $tuition,
                    'admission_fee' => $admission,
                    'examination_fee' => $exam,
                    'library_fee' => $library,
                    'other_charges' => $other,
                    'late_fee_fine' => $fine,
                    'total_amount' => $total,
                ]
            );
            $count = $this->allocateChallansForStructure($fs, $dueDate);
            $msg = "Fee structure saved for {$prog->name} (Sem " . ($validated['semester'] == 0 ? 'Full' : $validated['semester']) . ") and allocated to {$count} students.";
        } else {
            $dept = Department::findOrFail($validated['department_id']);
            $fs = FeeStructure::updateOrCreate(
                [
                    'academic_session_id' => $sessionId,
                    'department_id' => $dept->id,
                    'program_id' => null,
                    'semester' => $validated['semester'],
                ],
                [
                    'tuition_fee' => $tuition,
                    'admission_fee' => $admission,
                    'examination_fee' => $exam,
                    'library_fee' => $library,
                    'other_charges' => $other,
                    'late_fee_fine' => $fine,
                    'total_amount' => $total,
                ]
            );
            $count = $this->allocateChallansForStructure($fs, $dueDate);
            $msg = "Fee structure saved for Department of {$dept->name} and allocated to {$count} students.";
        }

        AuditService::log('Configured Fee Structure & Allocated Challans', 'FeeStructure', 0);

        return back()->with('success', $msg);
    }

    public function allocateStructureChallans(Request $request, FeeStructure $feeStructure)
    {
        $dueDate = $request->filled('due_date') ? Carbon::parse($request->due_date) : Carbon::today()->addDays(30);
        $count = $this->allocateChallansForStructure($feeStructure, $dueDate);

        return back()->with('success', "Allocated/updated fee challans for {$count} enrolled students.");
    }

    private function allocateChallansForStructure(FeeStructure $feeStructure, $dueDate = null)
    {
        $dueDate = $dueDate ? Carbon::parse($dueDate) : Carbon::today()->addDays(30);
        $issueDate = Carbon::today();
        $isOverdue = Carbon::today()->greaterThan($dueDate);

        $studentsQuery = Student::where('status', 'active');
        if ($feeStructure->program_id) {
            $studentsQuery->where('program_id', $feeStructure->program_id);
        } elseif ($feeStructure->department_id) {
            $studentsQuery->where('department_id', $feeStructure->department_id);
        }

        $students = $studentsQuery->get();
        $count = 0;

        foreach ($students as $st) {
            $targetSem = ($feeStructure->semester && $feeStructure->semester > 0) ? $feeStructure->semester : ($st->current_semester ?: 1);
            $challanNo = 'CH-' . date('Y') . '-S' . $targetSem . '-' . str_pad($st->id, 5, '0', STR_PAD_LEFT);
            $lateFine = $isOverdue ? $feeStructure->late_fee_fine : 0.00;

            FeeChallan::updateOrCreate(
                [
                    'student_id' => $st->id,
                    'semester' => $targetSem,
                ],
                [
                    'fee_structure_id' => $feeStructure->id,
                    'challan_number' => $challanNo,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'total_amount' => $feeStructure->total_amount,
                    'late_fine_amount' => $lateFine,
                    'status' => 'unpaid',
                ]
            );
            $count++;
        }

        return $count;
    }

    public function updateStructure(Request $request, FeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'program_id' => 'nullable|exists:programs,id',
            'department_id' => 'nullable|exists:departments,id',
            'semester' => 'required|integer|min:0|max:10',
            'tuition_fee' => 'required|numeric|min:0',
            'admission_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'late_fee_fine' => 'nullable|numeric|min:0',
        ]);

        $tuition = (float) $validated['tuition_fee'];
        $admission = (float) ($validated['admission_fee'] ?? 0);
        $exam = (float) ($validated['examination_fee'] ?? 0);
        $library = (float) ($validated['library_fee'] ?? 0);
        $other = (float) ($validated['other_charges'] ?? 0);
        $fine = (float) ($validated['late_fee_fine'] ?? 0);

        $total = $tuition + $admission + $exam + $library + $other;

        $deptId = $validated['department_id'] ?? null;
        if (!empty($validated['program_id'])) {
            $prog = Program::find($validated['program_id']);
            if ($prog && $prog->department_id) {
                $deptId = $prog->department_id;
            }
        }

        $feeStructure->update([
            'academic_session_id' => $validated['academic_session_id'],
            'program_id' => $validated['program_id'] ?? null,
            'department_id' => $deptId,
            'semester' => $validated['semester'],
            'tuition_fee' => $tuition,
            'admission_fee' => $admission,
            'examination_fee' => $exam,
            'library_fee' => $library,
            'other_charges' => $other,
            'late_fee_fine' => $fine,
            'total_amount' => $total,
        ]);

        $this->allocateChallansForStructure($feeStructure);

        AuditService::log('Updated Fee Structure', 'FeeStructure', $feeStructure->id);

        return back()->with('success', 'Fee structure details updated and student challans refreshed.');
    }

    public function destroyStructure(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        AuditService::log('Deleted Fee Structure', 'FeeStructure', $feeStructure->id);
        return back()->with('success', 'Fee structure record deleted.');
    }

    public function generateBatchChallans(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required|in:program,department,all',
            'department_id' => 'required_if:scope,department|nullable|exists:departments,id',
            'program_id' => 'required_if:scope,program|nullable|exists:programs,id',
            'semester' => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]);

        $dueDate = Carbon::parse($validated['due_date']);
        $issueDate = Carbon::today();
        $isOverdue = Carbon::today()->greaterThan($dueDate);

        $studentsQuery = Student::where('status', 'active');
        if ($validated['scope'] === 'program') {
            $studentsQuery->where('program_id', $validated['program_id']);
        } elseif ($validated['scope'] === 'department') {
            $studentsQuery->where('department_id', $validated['department_id']);
        }

        $students = $studentsQuery->get();
        $generatedCount = 0;

        foreach ($students as $st) {
            $currSem = $validated['semester'] ?? ($st->current_semester ?: 1);
            $sessionId = $st->batch->academic_session_id ?? null;

            $fs = null;
            if ($sessionId) {
                $fs = FeeStructure::where('academic_session_id', $sessionId)
                    ->where('program_id', $st->program_id)
                    ->where(function($q) use ($currSem) {
                        $q->where('semester', $currSem)->orWhere('semester', 0)->orWhereNull('semester');
                    })->first();
            }

            if (!$fs) {
                $fs = FeeStructure::where('program_id', $st->program_id)
                    ->where(function($q) use ($currSem) {
                        $q->where('semester', $currSem)->orWhere('semester', 0)->orWhereNull('semester');
                    })->first();
            }

            if (!$fs && $st->department_id) {
                $fs = FeeStructure::where('department_id', $st->department_id)
                    ->where(function($q) use ($currSem) {
                        $q->where('semester', $currSem)->orWhere('semester', 0)->orWhereNull('semester');
                    })->first();
            }

            if (!$fs) {
                $fs = FeeStructure::first();
            }

            $totalAmt = $fs ? $fs->total_amount : 20000.00;
            $lateFine = ($isOverdue && $fs) ? $fs->late_fee_fine : 0.00;

            $challanNo = 'CH-' . date('Y') . '-S' . $currSem . '-' . str_pad($st->id, 5, '0', STR_PAD_LEFT);

            FeeChallan::updateOrCreate(
                [
                    'student_id' => $st->id,
                    'semester' => $currSem,
                ],
                [
                    'fee_structure_id' => $fs ? $fs->id : null,
                    'challan_number' => $challanNo,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'total_amount' => $totalAmt,
                    'late_fine_amount' => $lateFine,
                    'status' => 'unpaid',
                ]
            );
            $generatedCount++;
        }

        AuditService::log('Batch Generated Student Fee Challans', 'FeeChallan', 0, ['count' => $generatedCount]);

        return back()->with('success', "Batch generated {$generatedCount} semester fee challans successfully.");
    }

    public function storeChallan(StoreFeeChallanRequest $request)
    {
        $validated = $request->validated();
        $challanNo = 'CH-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $challan = FeeChallan::create([
            'student_id' => $validated['student_id'],
            'fee_structure_id' => $validated['fee_structure_id'] ?? null,
            'challan_number' => $challanNo,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0.00,
            'status' => 'unpaid',
        ]);

        AuditService::log('Generated Student Fee Challan', 'FeeChallan', $challan->id, ['challan' => $challanNo]);

        return back()->with('success', "Fee Challan {$challanNo} issued successfully.");
    }

    public function verifyPayment(Request $request, FeeChallan $feeChallan)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,reopen',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($validated['action'] === 'approve') {
            $feeChallan->update([
                'status' => 'paid',
                'paid_amount' => $feeChallan->total_amount + $feeChallan->late_fine_amount,
                'paid_at' => Carbon::now(),
                'rejection_reason' => null,
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now(),
            ]);

            AuditService::log('Approved Student Fee Payment Proof', 'FeeChallan', $feeChallan->id, ['challan' => $feeChallan->challan_number]);
            $msg = "Fee payment proof for Challan {$feeChallan->challan_number} APPROVED and verified as Paid.";
        } elseif ($validated['action'] === 'reject') {
            $feeChallan->update([
                'status' => 'rejected_reupload',
                'paid_amount' => 0.00,
                'paid_at' => null,
                'rejection_reason' => $validated['rejection_reason'],
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now(),
            ]);

            AuditService::log('Rejected Student Fee Payment Proof (Re-upload Requested)', 'FeeChallan', $feeChallan->id, ['reason' => $validated['rejection_reason']]);
            $msg = "Fee payment proof for Challan {$feeChallan->challan_number} REJECTED. Student can view rejection note and re-upload receipt slip.";
        } else {
            $feeChallan->update([
                'status' => 'unpaid',
                'paid_amount' => 0.00,
                'paid_at' => null,
                'rejection_reason' => null,
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now(),
            ]);

            AuditService::log('Re-opened Student Fee Challan as Unpaid', 'FeeChallan', $feeChallan->id);
            $msg = "Challan {$feeChallan->challan_number} re-opened as Unpaid.";
        }

        return back()->with('success', $msg);
    }

    public function updateChallan(Request $request, FeeChallan $feeChallan)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'late_fine_amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,pending_verification,paid,rejected_reupload',
            'rejection_reason' => 'nullable|string|max:500',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $status = $validated['status'];
        $paidAmount = $status === 'paid' ? ((float)$validated['total_amount'] + (float)($validated['late_fine_amount'] ?? 0)) : 0.00;

        $updateData = [
            'total_amount' => $validated['total_amount'],
            'late_fine_amount' => $validated['late_fine_amount'] ?? 0.00,
            'due_date' => Carbon::parse($validated['due_date']),
            'status' => $status,
            'paid_amount' => $paidAmount,
            'rejection_reason' => $status === 'rejected_reupload' ? ($validated['rejection_reason'] ?? $feeChallan->rejection_reason) : null,
            'verified_by' => auth()->id(),
            'verified_at' => Carbon::now(),
        ];

        if ($request->filled('transaction_reference')) {
            $updateData['transaction_reference'] = $validated['transaction_reference'];
        }

        if ($request->filled('payment_notes')) {
            $updateData['payment_notes'] = $validated['payment_notes'];
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('fee_proofs', 'public');
            $updateData['payment_proof'] = $path;
        }

        $feeChallan->update($updateData);

        AuditService::log('Admin Updated Student Fee Challan & Receipt Slip', 'FeeChallan', $feeChallan->id, ['challan' => $feeChallan->challan_number]);

        return back()->with('success', "Fee Challan {$feeChallan->challan_number} updated successfully.");
    }

    public function markAsPaid(FeeChallan $feeChallan)
    {
        $feeChallan->update([
            'paid_amount' => $feeChallan->total_amount + $feeChallan->late_fine_amount,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'verified_by' => auth()->id(),
            'verified_at' => Carbon::now(),
        ]);

        AuditService::log('Marked Challan Paid', 'FeeChallan', $feeChallan->id);

        return back()->with('success', "Challan {$feeChallan->challan_number} marked as fully paid.");
    }

    public function printChallan(FeeChallan $feeChallan)
    {
        $feeChallan->load(['student.department', 'student.program', 'feeStructure']);
        return view('finance.challan_print', compact('feeChallan'));
    }
}
