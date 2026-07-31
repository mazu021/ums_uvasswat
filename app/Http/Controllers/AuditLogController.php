<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $logs = AuditLog::with('user')->latest()->paginate($perPage);
        return view('utilities.audit_logs', compact('logs'));
    }
}
