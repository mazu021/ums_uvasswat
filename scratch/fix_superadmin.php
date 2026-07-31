<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Create 'Super Admin' role if it doesn't exist
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
$uvasSwatRole = Role::firstOrCreate(['name' => 'UVAS SWAT']);

$user1 = User::find(1);
if ($user1) {
    $user1->assignRole($superAdminRole);
    $user1->assignRole($uvasSwatRole);
    echo "Successfully assigned 'Super Admin' and 'UVAS SWAT' roles to User ID 1 ({$user1->name})!\n";
} else {
    echo "User 1 not found.\n";
}
