<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ExactProgramHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $mainCampus = Campus::where('is_main', true)->first() ?? Campus::first();

        // 1. Four Faculties
        $fvs = Faculty::updateOrCreate(
            ['code' => 'FVS'],
            [
                'campus_id' => $mainCampus ? $mainCampus->id : 1,
                'name' => 'Faculty of Veterinary Sciences',
                'description' => 'Clinical veterinary studies, DVM degrees, and DVS 3-year diploma.',
                'dean_name' => 'Prof. Dr. Muhammad Shah',
            ]
        );

        $fahs = Faculty::updateOrCreate(
            ['code' => 'FAHS'],
            [
                'campus_id' => $mainCampus ? $mainCampus->id : 1,
                'name' => 'Faculty of Allied Health Sciences',
                'description' => 'Physical therapy (DPT), dental technology, medical lab technology, and imaging.',
                'dean_name' => 'Prof. Dr. Jan Alam',
            ]
        );

        $fs = Faculty::updateOrCreate(
            ['code' => 'FS'],
            [
                'campus_id' => $mainCampus ? $mainCampus->id : 1,
                'name' => 'Faculty of Sciences',
                'description' => 'Artificial intelligence, computer science, biochemistry, and zoology.',
                'dean_name' => 'Prof. Dr. Shaheen Begum',
            ]
        );

        $fass = Faculty::updateOrCreate(
            ['code' => 'FASS'],
            [
                'campus_id' => $mainCampus ? $mainCampus->id : 1,
                'name' => 'Faculty of Arts and Social Science',
                'description' => 'English, management sciences, sociology, psychology, and ADCP diploma.',
                'dean_name' => 'Prof. Dr. Tariq Ahmad',
            ]
        );

        // 2. Departments
        // FVS
        $deptDvm = Department::updateOrCreate(
            ['code' => 'DVM'],
            [
                'faculty_id' => $fvs->id,
                'name' => 'Department of Doctor of Veterinary Medicine (DVM)',
                'hod_name' => 'Dr. Zafar Iqbal',
                'description' => 'Clinical veterinary studies, surgery, and DVS diploma.',
            ]
        );

        // FAHS
        $deptDpt = Department::updateOrCreate(
            ['code' => 'DPT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Physical Therapy',
                'hod_name' => 'Dr. Asma Khan',
                'description' => 'Physical Therapy and rehabilitation sciences.',
            ]
        );

        $deptDdt = Department::updateOrCreate(
            ['code' => 'DDT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Dental Technology',
                'hod_name' => 'Dr. Usman Ghani',
                'description' => 'Dental technology and prosthodontics.',
            ]
        );

        $deptMlt = Department::updateOrCreate(
            ['code' => 'MLT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of Medical Laboratory Technology',
                'hod_name' => 'Dr. Sajjad Ahmad',
                'description' => 'Clinical laboratory diagnostics and pathology.',
            ]
        );

        $deptMit = Department::updateOrCreate(
            ['code' => 'MIT'],
            [
                'faculty_id' => $fahs->id,
                'name' => 'Department of MIT',
                'hod_name' => 'Dr. Nadia Shah',
                'description' => 'Medical Imaging Technology and radiography.',
            ]
        );

        // FS
        $deptDai = Department::updateOrCreate(
            ['code' => 'DAI'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Artificial Intelligence',
                'hod_name' => 'Dr. Bilal Hassan',
                'description' => 'Artificial intelligence, machine learning, and computer vision.',
            ]
        );

        $deptDcs = Department::updateOrCreate(
            ['code' => 'DCS'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Computer Science',
                'hod_name' => 'Dr. Fazal Wahab',
                'description' => 'Computer science and software engineering.',
            ]
        );

        $deptDbc = Department::updateOrCreate(
            ['code' => 'DBC'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Biochemistry',
                'hod_name' => 'Dr. Fatima Bibi',
                'description' => 'Molecular biochemistry and cellular biology.',
            ]
        );

        $deptZoo = Department::updateOrCreate(
            ['code' => 'ZOO'],
            [
                'faculty_id' => $fs->id,
                'name' => 'Department of Zoology',
                'hod_name' => 'Dr. Anwar Ali',
                'description' => 'Animal diversity and wildlife research.',
            ]
        );

        // FASS
        $deptEng = Department::updateOrCreate(
            ['code' => 'ENG'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of English',
                'hod_name' => 'Dr. Shazia Rehman',
                'description' => 'English literature and linguistics.',
            ]
        );

        $deptMs = Department::updateOrCreate(
            ['code' => 'MS'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Management Sciences',
                'hod_name' => 'Dr. Shahabuddin',
                'description' => 'Business administration and management.',
            ]
        );

        $deptSoc = Department::updateOrCreate(
            ['code' => 'SOC'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Sociology',
                'hod_name' => 'Dr. Bakht Zada',
                'description' => 'Sociology and social work.',
            ]
        );

        $deptPsy = Department::updateOrCreate(
            ['code' => 'PSY'],
            [
                'faculty_id' => $fass->id,
                'name' => 'Department of Psychology',
                'hod_name' => 'Dr. Gul Naz',
                'description' => 'Psychology and Advanced Diploma in Clinical Psychology (ADCP).',
            ]
        );

        // 3. Official Programs with Exact Semesters
        $officialPrograms = [
            // FVS
            [
                'code' => 'DVM-OPEN',
                'name' => 'Doctor of Veterinary Medicine (DVM) Open Merit',
                'department_id' => $deptDvm->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 5,
                'total_semesters' => 10,
                'total_credit_hours' => 178,
            ],
            [
                'code' => 'DVM-SELF',
                'name' => 'Doctor of Veterinary Medicine (DVM) Self-Finance',
                'department_id' => $deptDvm->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 5,
                'total_semesters' => 10,
                'total_credit_hours' => 178,
            ],
            [
                'code' => 'DVS-DIP',
                'name' => 'Diploma in Veterinary Sciences (DVS) 3 Years Diploma',
                'department_id' => $deptDvm->id,
                'degree_level' => 'Diploma',
                'duration_years' => 3,
                'total_semesters' => 6,
                'total_credit_hours' => 90,
            ],

            // FAHS - DPT
            [
                'code' => 'DPT-M',
                'name' => 'Doctor of Physical Therapy (DPT) Morning',
                'department_id' => $deptDpt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 5,
                'total_semesters' => 10,
                'total_credit_hours' => 175,
            ],
            [
                'code' => 'DPT-E',
                'name' => 'Doctor of Physical Therapy (DPT) Evening',
                'department_id' => $deptDpt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 5,
                'total_semesters' => 10,
                'total_credit_hours' => 175,
            ],

            // FAHS - DDT
            [
                'code' => 'BS-DDT-M',
                'name' => 'BS Dental Technology Morning',
                'department_id' => $deptDdt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],
            [
                'code' => 'BS-DDT-E',
                'name' => 'BS Dental Technology Evening',
                'department_id' => $deptDdt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FAHS - MLT
            [
                'code' => 'BS-MLT-M',
                'name' => 'BS MLT Morning',
                'department_id' => $deptMlt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 134,
            ],
            [
                'code' => 'BS-MLT-E',
                'name' => 'BS MLT Evening',
                'department_id' => $deptMlt->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 134,
            ],

            // FAHS - MIT
            [
                'code' => 'BS-MIT-M',
                'name' => 'BS MIT Morning',
                'department_id' => $deptMit->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 132,
            ],
            [
                'code' => 'BS-MIT-E',
                'name' => 'BS MIT Evening',
                'department_id' => $deptMit->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 132,
            ],

            // FS - DAI
            [
                'code' => 'BS-AI-M',
                'name' => 'BS Artificial Intelligence Morning',
                'department_id' => $deptDai->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],
            [
                'code' => 'BS-AI-E',
                'name' => 'BS Artificial Intelligence Evening',
                'department_id' => $deptDai->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FS - DCS
            [
                'code' => 'BS-CS-M',
                'name' => 'BS Computer Science Morning',
                'department_id' => $deptDcs->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],
            [
                'code' => 'BS-CS-E',
                'name' => 'BS Computer Science Evening',
                'department_id' => $deptDcs->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FS - DBC
            [
                'code' => 'BS-DBC-M',
                'name' => 'BS Biochemistry Morning',
                'department_id' => $deptDbc->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 132,
            ],

            // FS - ZOO
            [
                'code' => 'BS-ZOO-M',
                'name' => 'BS Zoology Morning',
                'department_id' => $deptZoo->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FASS - ENG
            [
                'code' => 'BS-ENG-M',
                'name' => 'BS English Morning',
                'department_id' => $deptEng->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FASS - MS
            [
                'code' => 'BBA-M',
                'name' => 'BS Business Administration (BBA) Morning',
                'department_id' => $deptMs->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 132,
            ],

            // FASS - SOC
            [
                'code' => 'BS-SOC-M',
                'name' => 'BS Sociology Morning',
                'department_id' => $deptSoc->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],

            // FASS - PSY
            [
                'code' => 'BS-PSY-M',
                'name' => 'BS Psychology Morning',
                'department_id' => $deptPsy->id,
                'degree_level' => 'Undergraduate',
                'duration_years' => 4,
                'total_semesters' => 8,
                'total_credit_hours' => 130,
            ],
            [
                'code' => 'ADCP-DIP',
                'name' => 'ADCP 1 Year Diploma',
                'department_id' => $deptPsy->id,
                'degree_level' => 'Postgraduate Diploma',
                'duration_years' => 1,
                'total_semesters' => 2,
                'total_credit_hours' => 32,
            ],
        ];

        $officialCodes = [];

        foreach ($officialPrograms as $progData) {
            $officialCodes[] = $progData['code'];
            Program::updateOrCreate(
                ['code' => $progData['code']],
                [
                    'name' => $progData['name'],
                    'department_id' => $progData['department_id'],
                    'degree_level' => $progData['degree_level'],
                    'duration_years' => $progData['duration_years'],
                    'total_semesters' => $progData['total_semesters'],
                    'total_credit_hours' => $progData['total_credit_hours'],
                    'status' => 'active',
                ]
            );
        }

        // Deactivate or delete extra non-official programs
        Program::whereNotIn('code', $officialCodes)->update(['status' => 'inactive']);
    }
}
