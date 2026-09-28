<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexAuditLogRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index(IndexAuditLogRequest $request)
    {
        Gate::authorize('viewAny', AuditLog::class);

        $filters = $request->validated();
        $search = trim((string) ($filters['search'] ?? ''));
        $status = $filters['status'] ?? '';

        $logs = AuditLog::query()
            ->with('actor')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('event', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('actor_name', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit.index', compact('logs', 'search', 'status'));
    }
}
