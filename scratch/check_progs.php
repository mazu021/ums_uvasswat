<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Program;

echo "ALL PROGRAMS IN DATABASE:\n";
foreach (Program::all() as $p) {
    echo "ID: {$p->id} | Code: {$p->code} | Status: {$p->status} | Name: {$p->name}\n";
}
