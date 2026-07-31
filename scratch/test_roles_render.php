<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$director = User::where('email', 'directorit@uvasswat.edu.pk')->first();
Auth::login($director);
$rolesController = app(\App\Http\Controllers\RoleController::class);
$viewData = $rolesController->index()->getData();
echo "Director IT Logged In -> Roles Count Visible: " . $viewData['roles']->count() . "\n";
foreach ($viewData['roles'] as $r) {
    echo "  - " . $r->name . "\n";
}

$hrUser = User::where('email', 'hr@uvasswat.edu.pk')->first();
if ($hrUser) {
    Auth::login($hrUser);
    $viewData2 = $rolesController->index()->getData();
    echo "\nHR Admin Logged In -> Roles Count Visible: " . $viewData2['roles']->count() . "\n";
    foreach ($viewData2['roles'] as $r) {
        echo "  - " . $r->name . "\n";
    }
}
