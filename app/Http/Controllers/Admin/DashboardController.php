<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EmailSent;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Usuario Autenticado. Bienvenido al Dashboard',
                'user' => $request->user(),
            ], 200);
        }

        $now = now();
        $dayStart = $now->copy()->startOfDay();
        $weekStart = $now->copy()->subDays(6)->startOfDay();
        $monthStart = $now->copy()->startOfMonth();

        $dailyActivity = AuditLog::query()
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
            ->whereBetween('created_at', [$weekStart, $now])
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');

        $activitySeries = collect(range(6, 0))->map(function (int $daysAgo) use ($now, $dailyActivity) {
            $date = $now->copy()->subDays($daysAgo);

            return [
                'date' => $date->toDateString(),
                'label' => $date->locale('es')->translatedFormat('D'),
                'count' => (int) $dailyActivity->get($date->toDateString(), 0),
            ];
        });

        $maxDailyActivity = max(1, (int) $activitySeries->max('count'));
        $activitySeries = $activitySeries->map(function (array $day) use ($maxDailyActivity) {
            return [...$day, 'height' => $day['count'] === 0 ? 4 : max(8, (int) round($day['count'] / $maxDailyActivity * 100))];
        });

        $stats = [
            'users' => User::query()->count(),
            'media' => Media::query()->count(),
            'media_week' => Media::query()->where('created_at', '>=', $weekStart)->count(),
            'emails_month' => EmailSent::query()->where('created_at', '>=', $monthStart)->count(),
            'emails_today' => EmailSent::query()->where('created_at', '>=', $dayStart)->count(),
            'failed_logins_today' => AuditLog::query()
                ->where('event', 'auth.login.failed')
                ->where('status', 'failure')
                ->where('created_at', '>=', $dayStart)
                ->count(),
            'audit_today' => AuditLog::query()->where('created_at', '>=', $dayStart)->count(),
        ];

        $recentActivity = AuditLog::query()->with('actor')->latest('created_at')->latest('id')->limit(6)->get();
        $recentMedia = Media::query()->select(['id', 'name', 'mime_type', 'created_at'])->latest()->limit(4)->get();
        $recentEmails = EmailSent::query()->select(['id', 'recipient', 'subject', 'created_at'])->latest()->limit(4)->get();

        return view('dashboard', compact('stats', 'activitySeries', 'recentActivity', 'recentMedia', 'recentEmails'));
    }
}
