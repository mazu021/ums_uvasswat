<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$superAdmin = User::role('Super Admin')->first();
$teacher = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Teacher', 'Faculty']))->first();
$student = User::role('Student')->first();

echo "Super Admin roles: " . implode(', ', $superAdmin->getRoleNames()->toArray()) . "\n";
echo "Teacher roles: " . implode(', ', $teacher->getRoleNames()->toArray()) . "\n";
echo "Student roles: " . implode(', ', $student->getRoleNames()->toArray()) . "\n";

echo "\n--- PERMISSION & GATE TEST ---\n";
auth()->login($superAdmin);
echo "Super Admin can manage users? " . (auth()->user()->can('manage users') ? 'YES' : 'NO') . "\n";

auth()->login($student);
echo "Student hasRole('Student')? " . (auth()->user()->hasRole('Student') ? 'YES' : 'NO') . "\n";
echo "Student hasRole('Admin')? " . (auth()->user()->hasRole('Admin') ? 'YES' : 'NO') . "\n";
