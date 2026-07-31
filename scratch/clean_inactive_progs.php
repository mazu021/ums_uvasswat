<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Program;

$deleted = Program::where('status', 'inactive')->delete();
echo "Deleted {$deleted} inactive programs that do not exist in official hierarchy.\n";

echo "REMAINING OFFICIAL ACTIVE PROGRAMS:\n";
foreach (Program::where('status', 'active')->get() as $p) {
    echo "ID: {$p->id} | Code: {$p->code} | Name: {$p->name}\n";
}
