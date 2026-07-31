<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

$email = 'maazaliswati@gmail.com';
$password = 'Laila@1347';
$name = 'Maaz Ali Mumtaz';

// Ensure 'Super Admin' role exists
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

$user = User::where('email', $email)->first();

if (!$user) {
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'status' => 'active',
    ]);
    echo "Created new Super Admin user account: {$name} ({$email})\n";
} else {
    $user->update([
        'name' => $name,
        'password' => Hash::make($password),
        'status' => 'active',
    ]);
    echo "Updated existing account to Super Admin: {$name} ({$email})\n";
}

// Sync exclusively with Super Admin role
$user->syncRoles([$superAdminRole]);

echo "Successfully configured {$name} ({$email}) as the One and Only Super Admin!\n";
