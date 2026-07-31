<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FacultyDirectorySeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password123!');

        $facultyData = [
            // Page 1
            ['first_name' => 'Liaqat', 'last_name' => 'Ali', 'email' => 'liaqat.ali@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'SOC'],
            ['first_name' => 'Majid', 'last_name' => 'Khan', 'email' => 'majid.khan@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'SOC'],
            ['first_name' => 'Maria', 'last_name' => 'Ilyas', 'email' => 'maria.ilyas@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'PSY'],
            ['first_name' => 'Mukrameen', 'last_name' => 'Khan', 'email' => 'mukrameen.khan@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'PSY'],
            ['first_name' => 'Muhammad', 'last_name' => 'Ismail', 'email' => 'm.ismail@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'MS'],
            ['first_name' => 'Azka', 'last_name' => 'Mehmood', 'email' => 'azka.mehmood@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MS'],
            ['first_name' => 'Chinar', 'last_name' => 'Shah', 'email' => 'chinar.shah@uvasswat.edu.pk', 'designation' => 'Teaching Assistant', 'dept_code' => 'MS'],
            ['first_name' => 'Adnan', 'last_name' => 'Ahad', 'email' => 'adnan.ahad@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'ENG'],
            ['first_name' => 'Nadia', 'last_name' => 'Shaukat', 'email' => 'nadia.shaukat@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'ENG'],
            ['first_name' => 'Muhammad Aslam', 'last_name' => 'Khan', 'email' => 'm.aslam@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'ENG'],
            ['first_name' => 'Sada', 'last_name' => 'Hamid', 'email' => 'sadahamid@uvasswat.edu.pk', 'designation' => 'Teaching Assistant', 'dept_code' => 'ENG'],
            ['first_name' => 'Abdullah', 'last_name' => 'Khan', 'email' => 'abdullah@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'ZOO'],
            ['first_name' => 'Waqar', 'last_name' => 'Ahmad', 'email' => 'waqar.ahmad@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'ZOO'],
            ['first_name' => 'Wasim', 'last_name' => 'Khan', 'email' => 'wasim.ahmad@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS-SCI'],
            ['first_name' => 'Shafiq', 'last_name' => 'Ahmad', 'email' => 'shafiq.ahmad@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS-SCI'],
            ['first_name' => 'Waqar', 'last_name' => 'Hussain', 'email' => 'waqar.hussain@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS-SCI'],
            ['first_name' => 'Abid ur', 'last_name' => 'Rahman', 'email' => 'abidurrahman@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS-SCI'],
            ['first_name' => 'Maryam', 'last_name' => 'Iqbal', 'email' => 'drmaryam@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DBC'],
            ['first_name' => 'Abdullah', 'last_name' => 'Shah', 'email' => 'abdullah.shah@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DBC'],
            ['first_name' => 'Irfan Ullah', 'last_name' => 'Khan', 'email' => 'irfan.ullah@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DBC'],
            ['first_name' => 'Umar', 'last_name' => 'Islam', 'email' => 'umar.islam@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DAI'],
            ['first_name' => 'Ayaz', 'last_name' => 'Khan', 'email' => 'ayaz.khan@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DAI'],
            ['first_name' => 'Taseer', 'last_name' => 'Ullah', 'email' => 'taseer.ullah@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DAI'],
            ['first_name' => 'Shah', 'last_name' => 'Zeb', 'email' => 'shahzeb@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MIT'],
            ['first_name' => 'Faiza', 'last_name' => 'Iqbal', 'email' => 'faiza.iqbal@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MIT'],
            ['first_name' => 'Naeem', 'last_name' => 'Ullah', 'email' => 'naeem.ullah@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MIT'],
            ['first_name' => 'Miraj', 'last_name' => 'Iqbal', 'email' => 'miraj.iqbal@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],
            ['first_name' => 'Muhammad', 'last_name' => 'Tahir', 'email' => 'm.tahir@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],
            ['first_name' => 'Adnan', 'last_name' => 'Haleem', 'email' => 'adnan.haleem@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],

            // Page 2
            ['first_name' => 'Muhammad', 'last_name' => 'Khurshaid', 'email' => 'khursheed@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],
            ['first_name' => 'Junaid', 'last_name' => 'Hameed', 'email' => 'junaid.hameed@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],
            ['first_name' => 'Tuseefa', 'last_name' => 'Sani', 'email' => 'tuseefa@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'MLT'],
            ['first_name' => 'Nisar Ud', 'last_name' => 'Din', 'email' => 'nisar.uddin@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DDT'],
            ['first_name' => 'Zuhra', 'last_name' => 'Anwar', 'email' => 'drzuhra@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DDT'],
            ['first_name' => 'Rizwan Ullah', 'last_name' => 'Shah', 'email' => 'rizwan.ullah@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DDT'],
            ['first_name' => 'Syed M.', 'last_name' => 'Salman', 'email' => 'syed.salman@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DDT'],
            ['first_name' => 'Shahzad', 'last_name' => 'Ahmad', 'email' => 'drshahzad@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DPT'],
            ['first_name' => 'Etisam', 'last_name' => 'Wahid', 'email' => 'dr.etisam@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DPT'],
            ['first_name' => 'Nimrah', 'last_name' => 'Humayoon', 'email' => 'drnimrahpt@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DPT'],
            ['first_name' => 'Shaheen', 'last_name' => 'Abdullah', 'email' => 'dr.shaheen@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DPT'],
            ['first_name' => 'Ajab', 'last_name' => 'Khan', 'email' => 'drajab22@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'PB'],
            ['first_name' => 'Nida', 'last_name' => 'Ullah', 'email' => 'nida.ullah@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'PB'],
            ['first_name' => 'Qasim', 'last_name' => 'Ali', 'email' => 'qasim.ali@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'PB'],
            ['first_name' => 'Arsalan', 'last_name' => 'Said', 'email' => 'arsalansaid@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'PB'],
            ['first_name' => 'Muhammad Zain', 'last_name' => 'Saleem', 'email' => 'zain.saleem@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'PB'],
            ['first_name' => 'Adnan', 'last_name' => 'Badshah', 'email' => 'adnan.badshah@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'PB'],
            ['first_name' => 'Tariq', 'last_name' => 'Aziz', 'email' => 'tariq.aziz@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'PB'],
            ['first_name' => 'Rahman', 'last_name' => 'Ullah', 'email' => 'rahmanullah@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Assar Ali', 'last_name' => 'Shah', 'email' => 'asaralishah@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Khalid', 'last_name' => 'Khan', 'email' => 'dr.khalid@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Muhammad Shahkar', 'last_name' => 'Uzair', 'email' => 'dr.shahkar@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Muhammad Najmus', 'last_name' => 'Saqib', 'email' => 'drsaqib@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Zaheer', 'last_name' => 'Ahmad', 'email' => 'zaheer.ahmad@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Mudasir', 'last_name' => 'Nazar', 'email' => 'drmudasir.nazar@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Ray Adil', 'last_name' => 'Quddus', 'email' => 'drrayadil@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'LPM'],
            ['first_name' => 'Haris', 'last_name' => 'Khan', 'email' => 'haris.khan@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'LPM'],
            ['first_name' => 'Asghar', 'last_name' => 'Khan', 'email' => 'drasghar@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'DCS'],

            // Page 3
            ['first_name' => 'Obaid', 'last_name' => 'Ullah', 'email' => 'obaid.ullah@uvasswat.edu.pk', 'designation' => 'Professor', 'dept_code' => 'DCS'],
            ['first_name' => 'Muhammad Tayyab', 'last_name' => 'Khan', 'email' => 'tayyab.khan@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DCS'],
            ['first_name' => 'Ayesha', 'last_name' => 'Humayun', 'email' => 'drayesha@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DCS'],
            ['first_name' => 'Asfand Yar', 'last_name' => 'Khan', 'email' => 'asfandyar.khan@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'DCS'],
            ['first_name' => 'Khurram Adrian', 'last_name' => 'Shah', 'email' => 'dr.khurram@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS'],
            ['first_name' => 'Ulfat', 'last_name' => 'Batool', 'email' => 'ulfatbatool@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'DCS'],
            ['first_name' => 'Nida', 'last_name' => 'Ullah BVS', 'email' => 'nida.bvs@uvasswat.edu.pk', 'designation' => 'Associate Professor', 'dept_code' => 'BVS'],
            ['first_name' => 'Sohrab', 'last_name' => 'Ahmad', 'email' => 'dr.sohrab@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'BVS'],
            ['first_name' => 'Muhammad', 'last_name' => 'Zeeshan', 'email' => 'm.zeeshan@uvasswat.edu.pk', 'designation' => 'Assistant Professor', 'dept_code' => 'BVS'],
            ['first_name' => 'Muhammad', 'last_name' => 'Sadeeq', 'email' => 'msadeeq@uvasswat.edu.pk', 'designation' => 'Lecturer', 'dept_code' => 'BVS'],
        ];

        $salaryMap = [
            'Professor' => 260000.00,
            'Associate Professor' => 195000.00,
            'Assistant Professor' => 145000.00,
            'Lecturer' => 110000.00,
            'Teaching Assistant' => 75000.00,
        ];

        $empCounter = 101;

        foreach ($facultyData as $item) {
            $fullName = $item['first_name'] . ' ' . $item['last_name'];
            $email = strtolower($item['email']);

            // Find Department
            $department = Department::where('code', $item['dept_code'])->first();
            if (!$department) {
                $department = Department::first();
            }

            // Create or update User Account
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => $password,
                    'status' => 'active',
                    'phone' => '+92-3' . rand(0, 4) . rand(1000000, 9999999),
                    'last_login_at' => now()->subDays(rand(1, 15)),
                ]
            );

            // Assign Faculty role
            if (!$user->hasRole('Faculty')) {
                $user->assignRole('Faculty');
            }

            // Salary & Emp Code
            $salary = $salaryMap[$item['designation']] ?? 100000.00;
            $empCode = 'EMP-UVAS-' . sprintf('%03d', $empCounter++);

            // Create Employee Record
            Employee::firstOrCreate(
                ['email' => $email],
                [
                    'user_id' => $user->id,
                    'department_id' => $department->id,
                    'employee_code' => $empCode,
                    'first_name' => $item['first_name'],
                    'last_name' => $item['last_name'],
                    'phone' => $user->phone,
                    'cnic' => '15602-' . rand(1000000, 9999999) . '-' . rand(1, 9),
                    'designation' => $item['designation'],
                    'type' => 'faculty',
                    'basic_salary' => $salary,
                    'joining_date' => now()->subMonths(rand(6, 36))->format('Y-m-d'),
                    'status' => 'active',
                ]
            );
        }
    }
}
