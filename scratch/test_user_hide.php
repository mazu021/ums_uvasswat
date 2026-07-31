<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$maaz = User::where('email', 'maazaliswati@gmail.com')->first();
Auth::login($maaz);

$userController = app(\App\Http\Controllers\UserController::class);
$users = $userController->index(request())->getData()['users'];

$found = false;
foreach ($users as $u) {
    if ($u->email === 'maazaliswati@gmail.com') {
        $found = true;
    }
}

echo "Is maazaliswati@gmail.com in /users table when Super Admin logged in? " . ($found ? "YES (FAIL)" : "NO (SUCCESS - 100% HIDDEN)") . "\n";
