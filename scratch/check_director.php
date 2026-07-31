<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$u = User::where('email', 'directorit@uvasswat.edu.pk')->first();
if ($u) {
    echo "User: {$u->name} ({$u->email})\n";
    echo "Assigned Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
} else {
    echo "User directorit@uvasswat.edu.pk not found.\n";
}

echo "\nMatching Roles in Database:\n";
foreach (Role::all() as $r) {
    if (str_contains(strtolower($r->name), 'director') || str_contains(strtolower($r->name), 'uvas') || str_contains(strtolower($r->name), 'super') || str_contains(strtolower($r->name), 'admin')) {
        echo "ID: {$r->id} | Name: {$r->name}\n";
    }
}
