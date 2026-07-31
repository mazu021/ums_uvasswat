<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(string $action, ?string $modelType = null, ?int $modelId = null, ?array $details = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'details' => $details,
                'ip_address' => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            // Ignore audit logging errors to prevent breaking main flow
        }
    }
}
