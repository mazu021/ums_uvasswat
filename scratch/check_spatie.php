<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== ALL ROLES IN DATABASE ===\n";
foreach (Role::all() as $r) {
    echo "ID: {$r->id} | Name: '{$r->name}'\n";
}

echo "\n=== ADMIN USERS AND THEIR ROLES ===\n";
foreach (User::all() as $u) {
    $roleNames = implode(', ', $u->getRoleNames()->toArray());
    if ($u->id < 10 || str_contains(strtolower($u->name), 'admin') || str_contains(strtolower($roleNames), 'admin')) {
        echo "User ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Roles: '{$roleNames}'\n";
    }
}
