<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "=== TESTING STRICT ROLE-BASED ACCESS CONTROL ===\n";

$superAdmin = User::role('Super Admin')->first();
$teacher = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Teacher', 'Faculty']))->first();
$student = User::role('Student')->first();

function testRoute($user, $url) {
    auth()->login($user);
    $request = Request::create($url, 'GET');
    try {
        $response = app('router')->dispatch($request);
        return $response->getStatusCode();
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        return $e->getStatusCode();
    } catch (\Exception $e) {
        return 'ERR: ' . $e->getMessage();
    }
}

echo "1. Super Admin accessing /users (Admin Panel): " . testRoute($superAdmin, '/users') . " (Expected: 200)\n";
echo "2. Student accessing /users (Admin Panel): " . testRoute($student, '/users') . " (Expected: 403 Forbidden)\n";
echo "3. Teacher accessing /users (Admin Panel): " . testRoute($teacher, '/users') . " (Expected: 403 Forbidden)\n";
echo "4. Student accessing /student/dashboard: " . testRoute($student, '/student/dashboard') . " (Expected: 200)\n";
echo "5. Teacher accessing /academic-attendance/teacher: " . testRoute($teacher, '/academic-attendance/teacher') . " (Expected: 200)\n";
echo "6. Student accessing /academic-attendance/teacher: " . testRoute($student, '/academic-attendance/teacher') . " (Expected: 403 Forbidden)\n";
echo "7. Super Admin accessing /student/dashboard: " . testRoute($superAdmin, '/student/dashboard') . " (Expected: 200)\n";
