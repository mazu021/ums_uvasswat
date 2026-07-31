<?php

use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceEngineController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusServicesController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExaminationModuleController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admission Portal Sync Webhook / API Endpoint
Route::post('/api/v1/admissions/sync-student', [AdmissionController::class, 'syncAdmittedStudent']);

// Authenticated ERP Routes
Route::middleware(['auth'])->group(function () {

    // Common Authenticated Routes (Accessible to all logged in users)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/academics/calendar', [AcademicCalendarController::class, 'index'])->name('academics.calendar.index');

    Route::get('/coming-soon', function (\Illuminate\Http\Request $request) {
        $feature = $request->get('feature', 'Upcoming Feature');
        return view('coming_soon', compact('feature'));
    })->name('coming-soon');

    // 1. Dedicated Student Portal Routes (Restricted to Students & Admins)
    Route::middleware(['role:Student|Director IT|Super Admin|UVAS SWAT|Super-Admin|University Admin|Admin'])->group(function () {
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/courses', [StudentPortalController::class, 'courses'])->name('courses');
            Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
            Route::get('/exams', [StudentPortalController::class, 'exams'])->name('exams');
            Route::get('/transcript', [StudentPortalController::class, 'transcript'])->name('transcript');
            Route::get('/exams/export', [StudentPortalController::class, 'exportStudentResult'])->name('exams.export');
            Route::get('/fees', [StudentPortalController::class, 'feeChallans'])->name('fees');
            Route::post('/fees/{feeChallan}/upload-proof', [StudentPortalController::class, 'uploadFeeProof'])->name('fees.upload-proof');
        });
        Route::get('/academic-attendance/student', [StudentAttendanceController::class, 'dashboard'])->name('attendance.student.dashboard');
    });

    // 2. Dedicated Faculty & Teacher Routes (Restricted to Faculty, Teachers, Admins)
    Route::middleware(['role:Faculty|Teacher|Director IT|Super Admin|UVAS SWAT|Super-Admin|University Admin|Admin|Staff|Accounts|HR'])->group(function () {
        Route::get('/academic-attendance/teacher', [AttendanceEngineController::class, 'teacherDashboard'])->name('attendance.teacher.dashboard');
        Route::get('/academic-attendance/offering/{courseOffering}/mark', [AttendanceEngineController::class, 'markAttendanceForm'])->name('attendance.mark.form');
        Route::post('/academic-attendance/offering/{courseOffering}/store', [AttendanceEngineController::class, 'storeAttendance'])->name('attendance.mark.store');
        Route::get('/academic-attendance/offering/{courseOffering}/history', [AttendanceEngineController::class, 'offeringHistory'])->name('attendance.offering.history');
        Route::get('/academic-attendance/session/{attendanceSession}', [AttendanceEngineController::class, 'showSession'])->name('attendance.session.detail');
        Route::get('/academic-attendance/reports', [AttendanceReportController::class, 'index'])->name('attendance.reports.index');

        // Faculty Exams, Results & Transcripts for Assigned Courses
        Route::prefix('academics')->name('academics.')->group(function () {
            Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
            Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
            Route::post('/exams/grades', [ExamController::class, 'storeGrade'])->name('exams.store-grade');
            Route::post('/exams/gradebook/save', [ExamController::class, 'saveGradebook'])->name('exams.save-gradebook');
            Route::get('/exams/gradebook/{offering}/export', [ExamController::class, 'exportGradebook'])->name('exams.export-gradebook');
            Route::get('/results', [ResultController::class, 'index'])->name('results.index');

            Route::get('/exams/{exam}/seating-plan', [ExaminationModuleController::class, 'seatingPlan'])->name('exams.seating-plan');
            Route::get('/transcript', [ExaminationModuleController::class, 'transcript'])->name('transcript');
            Route::get('/students/{student}/transcript', [ExaminationModuleController::class, 'transcript'])->name('exams.transcript');
            Route::get('/students/{student}/degree-audit', [ExaminationModuleController::class, 'degreeAudit'])->name('exams.degree-audit');
        });

        // Faculty Leave Application & Management
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
            Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
        });
    });

    // 3. Admin & Staff Management Routes (Restricted: NOT accessible by Students or Faculty Teachers!)
    Route::middleware(['role:Director IT|Super Admin|UVAS SWAT|Super-Admin|University Admin|Admin|Staff|Accounts|HR'])->group(function () {

        // User & Role Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::put('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('update-permissions');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('update-permissions');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // HR & Administration
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
            Route::post('/faculties', [DepartmentController::class, 'storeFaculty'])->name('faculties.store');
            Route::put('/faculties/{faculty}', [DepartmentController::class, 'updateFaculty'])->name('faculties.update');
            Route::delete('/faculties/{faculty}', [DepartmentController::class, 'destroyFaculty'])->name('faculties.destroy');

            Route::post('/departments', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
            Route::put('/departments/{department}', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
            Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');

            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
            Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

            Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance', [AttendanceController::class, 'mark'])->name('attendance.mark');

            Route::patch('/leaves/{leaveApplication}/status', [LeaveController::class, 'updateStatus'])->name('leaves.update-status');

            Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
            Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
            Route::get('/payroll/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('payroll.payslip');
        });

        // Admissions Management
        Route::prefix('admissions')->name('admissions.')->group(function () {
            Route::get('/', [AdmissionController::class, 'index'])->name('index');
            Route::get('/apply', [AdmissionController::class, 'create'])->name('apply');
            Route::post('/apply', [AdmissionController::class, 'store'])->name('store');
            Route::patch('/{application}/status', [AdmissionController::class, 'updateStatus'])->name('update-status');
            Route::get('/merit-list', [AdmissionController::class, 'meritList'])->name('merit-list');
        });

        // Course Offering Management
        Route::get('/course-offerings', [CourseOfferingController::class, 'index'])->name('course-offerings.index');
        Route::post('/course-offerings', [CourseOfferingController::class, 'store'])->name('course-offerings.store');
        Route::put('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'update'])->name('course-offerings.update');
        Route::delete('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'destroy'])->name('course-offerings.destroy');
        Route::get('/course-offerings/{courseOffering}/export-students', [CourseOfferingController::class, 'exportStudents'])->name('course-offerings.export-students');

        // Academic Management
        Route::prefix('academics')->name('academics.')->group(function () {
            Route::get('/students', [StudentController::class, 'index'])->name('students.index');
            Route::post('/students', [StudentController::class, 'store'])->name('students.store');
            Route::post('/students/import-excel', [StudentController::class, 'importExcel'])->name('students.import-excel');
            Route::get('/students/download-sample', [StudentController::class, 'downloadSampleCsv'])->name('students.download-sample');
            Route::get('/students/batch-list', [StudentController::class, 'getBatchStudents'])->name('students.batch-list');
            Route::post('/students/promote-batch', [StudentController::class, 'promoteBatch'])->name('students.promote-batch');
            Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
            Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
            Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

            Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
            Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
            Route::post('/courses/assign', [CourseController::class, 'assignFaculty'])->name('courses.assign');
            Route::delete('/courses/unassign', [CourseController::class, 'unassignFaculty'])->name('courses.unassign');
            Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
            Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

            Route::get('/curriculum', [CurriculumController::class, 'index'])->name('curriculum.index');
            Route::post('/curriculum', [CurriculumController::class, 'store'])->name('curriculum.store');
            Route::post('/curriculum/{curriculum}/add-course', [CurriculumController::class, 'addCourse'])->name('curriculum.add-course');
            Route::delete('/curriculum/course/{curriculumCourse}', [CurriculumController::class, 'removeCourse'])->name('curriculum.remove-course');

            Route::get('/course-registration', [CourseRegistrationController::class, 'index'])->name('course-registration.index');
            Route::post('/course-registration', [CourseRegistrationController::class, 'store'])->name('course-registration.store');
            Route::patch('/course-registration/{registration}/status', [CourseRegistrationController::class, 'updateStatus'])->name('course-registration.update-status');

            Route::get('/sessions', [AcademicSessionController::class, 'index'])->name('sessions.index');
            Route::post('/sessions', [AcademicSessionController::class, 'store'])->name('sessions.store');
            Route::patch('/sessions/{academicSession}/status', [AcademicSessionController::class, 'updateStatus'])->name('sessions.update-status');

            Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
            Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');

            Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
            Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');

            Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
            Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');

            Route::post('/calendar/upload', [AcademicCalendarController::class, 'upload'])->name('calendar.upload');
            Route::delete('/calendar/{academicCalendar}', [AcademicCalendarController::class, 'destroy'])->name('calendar.destroy');
        });

        // Finance & Accounts
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/fees', [FinanceController::class, 'index'])->name('fees.index');
            Route::post('/fees/structures', [FinanceController::class, 'storeStructure'])->name('fees.structures.store');
            Route::put('/fees/structures/{feeStructure}', [FinanceController::class, 'updateStructure'])->name('fees.structures.update');
            Route::delete('/fees/structures/{feeStructure}', [FinanceController::class, 'destroyStructure'])->name('fees.structures.destroy');
            Route::post('/fees/structures/{feeStructure}/allocate', [FinanceController::class, 'allocateStructureChallans'])->name('fees.structures.allocate');
            Route::post('/fees/generate-batch', [FinanceController::class, 'generateBatchChallans'])->name('fees.generate-batch');
            Route::post('/fees/challans', [FinanceController::class, 'storeChallan'])->name('fees.challans.store');
            Route::put('/fees/challans/{feeChallan}', [FinanceController::class, 'updateChallan'])->name('fees.challans.update');
            Route::post('/fees/challans/{feeChallan}/verify', [FinanceController::class, 'verifyPayment'])->name('fees.challans.verify');
            Route::patch('/fees/challans/{feeChallan}/pay', [FinanceController::class, 'markAsPaid'])->name('fees.challans.pay');
            Route::get('/fees/challans/{feeChallan}/print', [FinanceController::class, 'printChallan'])->name('fees.challans.print');

            Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');
            Route::post('/accounts/entries', [AccountsController::class, 'store'])->name('accounts.store');

            Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
            Route::post('/scholarships', [ScholarshipController::class, 'store'])->name('scholarships.store');
        });

        // Campus Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/library', [CampusServicesController::class, 'library'])->name('library');
            Route::get('/hostel', [CampusServicesController::class, 'hostel'])->name('hostel');
            Route::get('/transport', [CampusServicesController::class, 'transport'])->name('transport');
            Route::get('/inventory', [CampusServicesController::class, 'inventory'])->name('inventory');
        });

        // Utilities, Reports & Settings
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [ReportController::class, 'printReport'])->name('reports.print');

        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

});
