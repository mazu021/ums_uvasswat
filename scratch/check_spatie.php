<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$teacher = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Teacher', 'Faculty']))->first();

auth()->login($teacher);
echo "Logged in Teacher: " . $teacher->name . "\n";
echo "Role: " . implode(', ', $teacher->getRoleNames()->toArray()) . "\n";
echo "Has role 'Faculty'? " . ($teacher->hasRole('Faculty') ? 'YES' : 'NO') . "\n";
echo "Has role 'Teacher'? " . ($teacher->hasRole('Teacher') ? 'YES' : 'NO') . "\n";
