<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Batch;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class UVASSwatHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Campuses
        $mainCampus = Campus::firstOrCreate(
            ['code' => 'SWAT-MAIN'],
            [
                'name' => 'Main Campus Swat',
                'city' => 'Swat',
                'address' => 'Ghabragee, Sector A, Kanju Township, Swat, KPK',
                'phone' => '+92-946-9240404',
                'email' => 'info@uvasw.edu.pk',
                'is_main' => true,
                'status' => 'active',
            ]
        );

        $subCampus = Campus::firstOrCreate(
            ['code' => 'SWAT-KABAL'],
            [
                'name' => 'Kabal Sub-Campus',
                'city' => 'Kabal, Swat',
                'address' => 'Kabal Valley Road, Swat',
                'phone' => '+92-946-9240405',
                'email' => 'kabal@uvasw.edu.pk',
                'is_main' => false,
                'status' => 'active',
            ]
        );

        // 2. Four Faculties
        $fvs = Faculty::firstOrCreate(
            ['code' => 'FVS'],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'Faculty of Veterinary Sciences',
                'description' => 'Leading faculty for clinical veterinary studies, animal health, and pathobiology.',
                'dean_name' => 'Prof. Dr. Muhammad Shah',
            ]
        );

        $fahs = Faculty::firstOrCreate(
            ['code' => 'FAHS'],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'Faculty of Allied Health Sciences',
                'description' => 'Faculty dedicated to physical therapy, medical lab technology, dental technology, and imaging.',
                'dean_name' => 'Prof. Dr. Jan Alam',
            ]
        );

        $fs = Faculty::firstOrCreate(
            ['code' => 'FS'],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'Faculty of Sciences',
                'description' => 'Advanced studies in artificial intelligence, computer science, biochemistry, and zoology.',
                'dean_name' => 'Prof. Dr. Shaheen Begum',
            ]
        );

        $fass = Faculty::firstOrCreate(
            ['code' => 'FASS'],
            [
                'campus_id' => $mainCampus->id,
                'name' => 'Faculty of Arts and Social Science',
                'description' => 'Humanities, management sciences, psychology, sociology, and English literature.',
                'dean_name' => 'Prof. Dr. Tariq Ahmad',
            ]
        );

        // 3. Four Departments per Faculty

        // --- Faculty of Veterinary Sciences (4 Departments) ---
        $bvs = Department::firstOrCreate(
            ['code' => 'BVS'],
            [
                'faculty_id' => $fvs->id,
                'name' => 'Department of Basic Veterinary Sciences',
                'hod_name' => 'Dr. Ihsan Ullah',
                'description' => 'Veterinary anatomy, physiology, and pharmacology fundamentals.',
            ]
        );

        $cs = Department::firstOrCreate(
            ['code' => 'DCS'],
            [
                'faculty_id' => $fvs->id,
                'name' => 'Department of Clinical Studies',
                'hod_name' => 'Dr. Zafar Iqbal',
                'description' => 'Clinical diagnosis, animal surgery, and therapeutics.',
            ]
        );

        $lpm = Department::firstOrCreate(
            ['code' => 'LPM'],
            [
                'faculty_id' => $fvs->id,
                'name' => 'Department of Livestock Production and Management',
                'hod_name' => 'Dr. Rashid Khan',
                'description' => 'Livestock breeding, farm management, and animal nutrition.',
            ]
        );

        $pb = Department::firstOrCreate(
            ['code' => 'PB'],
            [
                'faculty_id' => $fvs->id,
                'name' => 'Department of Pathobiology',
                'hod_name' => 'Dr. Yasir Ali',
                'description' => 'Veterinary pathology, parasitology, and microbiology.',
            ]
        );

        // --- Faculty of Allied Health Sciences (4 Departments) ---
        $dpt = Department::firstOrCreate(
            ['code' => 'DPT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Physical Therapy',
                'hod_name' => 'Dr. Asma Khan',
                'description' => 'Doctor of Physical Therapy (DPT) and rehabilitation sciences.',
            ]
        );

        $ddt = Department::firstOrCreate(
            ['code' => 'DDT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Dental Technology',
                'hod_name' => 'Dr. Usman Ghani',
                'description' => 'Dental technology, prosthodontics, and oral care laboratory sciences.',
            ]
        );

        $mlt = Department::firstOrCreate(
            ['code' => 'MLT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Medical Laboratory Technology',
                'hod_name' => 'Dr. Sajjad Ahmad',
                'description' => 'Clinical laboratory diagnostics, hematology, and histopathology.',
            ]
        );

        $mit = Department::firstOrCreate(
            ['code' => 'MIT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of MIT',
                'hod_name' => 'Dr. Nadia Shah',
                'description' => 'Medical Imaging Technology, radiography, and ultrasound diagnostics.',
            ]
        );

        // --- Faculty of Sciences (4 Departments) ---
        $dai = Department::firstOrCreate(
            ['code' => 'DAI'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Artificial Intelligence',
                'hod_name' => 'Dr. Bilal Hassan',
                'description' => 'Machine learning, neural networks, computer vision, and AI algorithms.',
            ]
        );

        $dbc = Department::firstOrCreate(
            ['code' => 'DBC'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Biochemistry',
                'hod_name' => 'Dr. Fatima Bibi',
                'description' => 'Molecular biochemistry, enzymology, and cellular biology.',
            ]
        );

        $dcs = Department::firstOrCreate(
            ['code' => 'DCS-SCI'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Computer Science',
                'hod_name' => 'Dr. Fazal Wahab',
                'description' => 'Software engineering, database systems, networks, and computing.',
            ]
        );

        $zoo = Department::firstOrCreate(
            ['code' => 'ZOO'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Zoology',
                'hod_name' => 'Dr. Anwar Ali',
                'description' => 'Animal diversity, wildlife conservation, and ecological research.',
            ]
        );

        // --- Faculty of Arts and Social Science (4 Departments) ---
        $eng = Department::firstOrCreate(
            ['code' => 'ENG'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of English',
                'hod_name' => 'Dr. Shazia Rehman',
                'description' => 'English literature, linguistics, and professional communication.',
            ]
        );

        $ms = Department::firstOrCreate(
            ['code' => 'MS'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Management Sciences',
                'hod_name' => 'Dr. Shahabuddin',
                'description' => 'Business administration, finance, marketing, and public management.',
            ]
        );

        $psy = Department::firstOrCreate(
            ['code' => 'PSY'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Psychology',
                'hod_name' => 'Dr. Gul Naz',
                'description' => 'Behavioral sciences, clinical psychology, and counseling.',
            ]
        );

        $soc = Department::firstOrCreate(
            ['code' => 'SOC'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Sociology',
                'hod_name' => 'Dr. Bakht Zada',
                'description' => 'Social structures, community development, and cultural studies.',
            ]
        );

        // 4. Programs (Morning & Evening for All Departments)

        // --- Department of Clinical Studies (DVM) ---
        $dvmM = Program::firstOrCreate(['code' => 'DVM-M'], [
            'department_id' => $cs->id,
            'name' => 'Doctor of Veterinary Medicine (DVM) Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 5,
            'total_semesters' => 10,
            'total_credit_hours' => 178,
            'status' => 'active',
        ]);
        $dvmE = Program::firstOrCreate(['code' => 'DVM-E'], [
            'department_id' => $cs->id,
            'name' => 'Doctor of Veterinary Medicine (DVM) Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 5,
            'total_semesters' => 10,
            'total_credit_hours' => 178,
            'status' => 'active',
        ]);

        // --- Department of Basic Veterinary Sciences ---
        Program::firstOrCreate(['code' => 'BS-BVS-M'], [
            'department_id' => $bvs->id,
            'name' => 'BS Veterinary Sciences Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-BVS-E'], [
            'department_id' => $bvs->id,
            'name' => 'BS Veterinary Sciences Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);

        // --- Department of Livestock Production and Management ---
        Program::firstOrCreate(['code' => 'BS-LPM-M'], [
            'department_id' => $lpm->id,
            'name' => 'BS Livestock Management Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-LPM-E'], [
            'department_id' => $lpm->id,
            'name' => 'BS Livestock Management Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);

        // --- Department of Pathobiology ---
        Program::firstOrCreate(['code' => 'BS-PB-M'], [
            'department_id' => $pb->id,
            'name' => 'BS Pathobiology Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-PB-E'], [
            'department_id' => $pb->id,
            'name' => 'BS Pathobiology Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);

        // --- Department of Physical Therapy (DPT) ---
        Program::firstOrCreate(['code' => 'DPT-M'], [
            'department_id' => $dpt->id,
            'name' => 'Doctor of Physical Therapy (DPT) Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 5,
            'total_semesters' => 10,
            'total_credit_hours' => 175,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'DPT-E'], [
            'department_id' => $dpt->id,
            'name' => 'Doctor of Physical Therapy (DPT) Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 5,
            'total_semesters' => 10,
            'total_credit_hours' => 175,
            'status' => 'active',
        ]);

        // --- Department of Dental Technology (DDT) ---
        Program::firstOrCreate(['code' => 'BS-DDT-M'], [
            'department_id' => $ddt->id,
            'name' => 'BS Dental Technology Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-DDT-E'], [
            'department_id' => $ddt->id,
            'name' => 'BS Dental Technology Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of Medical Laboratory Technology (MLT) ---
        Program::firstOrCreate(['code' => 'BS-MLT-M'], [
            'department_id' => $mlt->id,
            'name' => 'BS MLT Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-MLT-E'], [
            'department_id' => $mlt->id,
            'name' => 'BS MLT Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 134,
            'status' => 'active',
        ]);

        // --- Department of MIT ---
        Program::firstOrCreate(['code' => 'BS-MIT-M'], [
            'department_id' => $mit->id,
            'name' => 'BS MIT Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-MIT-E'], [
            'department_id' => $mit->id,
            'name' => 'BS MIT Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);

        // --- Department of Artificial Intelligence ---
        Program::firstOrCreate(['code' => 'BS-AI-M'], [
            'department_id' => $dai->id,
            'name' => 'BS Artificial Intelligence Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-AI-E'], [
            'department_id' => $dai->id,
            'name' => 'BS Artificial Intelligence Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of Biochemistry ---
        Program::firstOrCreate(['code' => 'BS-DBC-M'], [
            'department_id' => $dbc->id,
            'name' => 'BS Biochemistry Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-DBC-E'], [
            'department_id' => $dbc->id,
            'name' => 'BS Biochemistry Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);

        // --- Department of Computer Science ---
        Program::firstOrCreate(['code' => 'BS-CS-M'], [
            'department_id' => $dcs->id,
            'name' => 'BS Computer Science Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-CS-E'], [
            'department_id' => $dcs->id,
            'name' => 'BS Computer Science Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of Zoology ---
        Program::firstOrCreate(['code' => 'BS-ZOO-M'], [
            'department_id' => $zoo->id,
            'name' => 'BS Zoology Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-ZOO-E'], [
            'department_id' => $zoo->id,
            'name' => 'BS Zoology Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of English ---
        Program::firstOrCreate(['code' => 'BS-ENG-M'], [
            'department_id' => $eng->id,
            'name' => 'BS English Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-ENG-E'], [
            'department_id' => $eng->id,
            'name' => 'BS English Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of Management Sciences ---
        Program::firstOrCreate(['code' => 'BBA-M'], [
            'department_id' => $ms->id,
            'name' => 'BS Business Administration (BBA) Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BBA-E'], [
            'department_id' => $ms->id,
            'name' => 'BS Business Administration (BBA) Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 132,
            'status' => 'active',
        ]);

        // --- Department of Psychology ---
        Program::firstOrCreate(['code' => 'BS-PSY-M'], [
            'department_id' => $psy->id,
            'name' => 'BS Psychology Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-PSY-E'], [
            'department_id' => $psy->id,
            'name' => 'BS Psychology Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // --- Department of Sociology ---
        Program::firstOrCreate(['code' => 'BS-SOC-M'], [
            'department_id' => $soc->id,
            'name' => 'BS Sociology Morning',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);
        Program::firstOrCreate(['code' => 'BS-SOC-E'], [
            'department_id' => $soc->id,
            'name' => 'BS Sociology Evening',
            'degree_level' => 'Undergraduate',
            'duration_years' => 4,
            'total_semesters' => 8,
            'total_credit_hours' => 130,
            'status' => 'active',
        ]);

        // 5. Academic Sessions
        $s2023 = AcademicSession::firstOrCreate(['code' => 'FALL-2023'], [
            'name' => 'Fall 2023',
            'start_date' => '2023-09-01',
            'end_date' => '2024-01-31',
            'is_current' => false,
            'status' => 'active',
        ]);

        $s2024 = AcademicSession::firstOrCreate(['code' => 'FALL-2024'], [
            'name' => 'Fall 2024',
            'start_date' => '2024-09-01',
            'end_date' => '2025-01-31',
            'is_current' => false,
            'status' => 'active',
        ]);

        $s2025 = AcademicSession::firstOrCreate(['code' => 'FALL-2025'], [
            'name' => 'Fall 2025',
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-31',
            'is_current' => false,
            'status' => 'active',
        ]);

        $s2026 = AcademicSession::firstOrCreate(['code' => 'FALL-2026'], [
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        $session2425 = $s2026;

        // 6. Batches
        $batchDvm24 = Batch::firstOrCreate(
            ['code' => 'DVM-F24'],
            [
                'program_id' => $dvmM->id,
                'academic_session_id' => $session2425->id,
                'name' => 'DVM Batch Fall 2024',
                'status' => 'active',
            ]
        );

        // 7. Semesters
        $sem1 = Semester::firstOrCreate(
            ['batch_id' => $batchDvm24->id, 'semester_number' => 1],
            [
                'name' => '1st Semester',
                'start_date' => '2024-09-15',
                'end_date' => '2025-01-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // 8. Sections
        Section::firstOrCreate(
            ['semester_id' => $sem1->id, 'name' => 'Section A'],
            ['max_capacity' => 50]
        );

        Section::firstOrCreate(
            ['semester_id' => $sem1->id, 'name' => 'Section B'],
            ['max_capacity' => 50]
        );
    }
}
