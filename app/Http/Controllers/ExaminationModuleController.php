<?php

namespace App\Http\Controllers;

use App\Models\CourseOfferingGrade;
use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;

use Illuminate\Http\Request;

class ExaminationModuleController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['course', 'academicSession'])->get();
        return view('academics.exams.index', compact('exams'));
    }

    public function seatingPlan(Exam $exam)
    {
        $exam->load(['course']);
        $registeredStudents = Student::where('department_id', $exam->course->department_id ?? null)
            ->where('status', 'active')
            ->get();

        return view('academics.exams.seating_plan', compact('exam', 'registeredStudents'));
    }

    public function transcript(Request $request, Student $student = null)
    {
        $studentsList = Student::with(['program'])->orderBy('roll_number')->get();

        $studentId = $request->get('student_id');
        if ($studentId) {
            $student = Student::find($studentId);
        } elseif (!$student && $studentsList->count() > 0) {
            $student = $studentsList->first();
        }

        $grades = collect();
        $cgpa = 0.00;
        $totalCreditHours = 0;
        $totalQualityPoints = 0;

        if ($student) {
            $student->load(['program.department', 'batch']);
            $grades = CourseOfferingGrade::with(['courseOffering.course', 'courseOffering.teacher', 'courseOffering.program'])
                ->where('student_id', $student->id)
                ->get();

            $cgpa = $student->calculateCgpa();

            foreach ($grades as $g) {
                $ch = $g->courseOffering->course->credit_hours ?? 3;
                $totalCreditHours += $ch;
                $totalQualityPoints += ($g->gpa_point * $ch);
            }
        }

        return view('academics.transcript', compact(
            'studentsList',
            'student',
            'grades',
            'cgpa',
            'totalCreditHours',
            'totalQualityPoints'
        ));
    }

    public function degreeAudit(Student $student)
    {
        $student->load(['department', 'user']);
        return view('academics.exams.degree_audit', compact('student'));
    }
}
