<?php

namespace App\Http\Controllers;

use App\Models\CourseOfferingGrade;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $programId = $request->get('program_id');
        $semester = $request->get('semester');
        $perPage = $request->get('per_page', 100);

        $query = CourseOfferingGrade::with([
            'student.program',
            'courseOffering.course.department',
            'courseOffering.academicSession',
            'courseOffering.teacher'
        ]);

        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && !$user->hasRole('HOD')) {
            $employee = \App\Models\Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            $query->whereHas('courseOffering', function ($cq) use ($user, $employee) {
                $cq->where('teacher_id', $user->id);
                if ($employee) {
                    $cq->orWhere('teacher_id', $employee->id);
                }
            });
        }

        if ($search) {
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        if ($programId) {
            $query->whereHas('student', function ($sq) use ($programId) {
                $sq->where('program_id', $programId);
            });
        }

        if ($semester) {
            $query->whereHas('courseOffering', function ($cq) use ($semester) {
                $cq->where('semester_number', $semester);
            });
        }

        $results = $query->latest()->paginate($perPage);
        $programs = Program::where('status', 'active')->get();

        return view('academics.results', compact('results', 'programs', 'search', 'programId', 'semester'));
    }
}
