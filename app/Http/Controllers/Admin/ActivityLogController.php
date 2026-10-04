<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->role === UserRole::Admin, 403);
        $filters = $request->validate([
            'action' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        return view('admin.activity-logs.index', [
            'logs' => ActivityLog::query()
                ->when($filters['action'] ?? null, fn (Builder $query, string $action): Builder => $query->where('action', $action))
                ->when($filters['user_id'] ?? null, fn (Builder $query, int|string $userId): Builder => $query->where('user_id', $userId))
                ->with('user:id,name,email')
                ->latest('created_at')
                ->latest('id')
                ->paginate(30)
                ->withQueryString(),
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action'),
            'actors' => User::query()->whereHas('activityLogs')->orderBy('name')->get(['id', 'name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless($request->user()->role === UserRole::Admin, 403);

        $response = new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Người thực hiện', 'Email', 'Hành động', 'Đối tượng', 'Mô tả', 'Thời gian']);

            ActivityLog::query()
                ->with('user:id,name,email')
                ->latest('id')
                ->chunk(200, function ($logs) use ($handle): void {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            $log->id,
                            $log->user?->name ?? 'Hệ thống',
                            $log->user?->email ?? '',
                            $log->action,
                            $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '',
                            $log->description,
                            $log->created_at->format('d/m/Y H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="nhat-ky-hoat-dong-'.now()->format('Ymd_His').'.csv"');

        return $response;
    }
}
