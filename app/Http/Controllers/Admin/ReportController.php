<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Show a summary report for a chosen period (today / this week / this month / custom).
     */
    public function index(Request $request): View
    {
        [$from, $to, $period] = $this->resolveRange($request);

        $complaints = Complaint::with(['user', 'department'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $summary = [
            'total' => $complaints->count(),
            'pending' => $complaints->where('status', Complaint::STATUS_PENDING)->count(),
            'under_review' => $complaints->where('status', Complaint::STATUS_UNDER_REVIEW)->count(),
            'in_progress' => $complaints->where('status', Complaint::STATUS_IN_PROGRESS)->count(),
            'resolved' => $complaints->where('status', Complaint::STATUS_RESOLVED)->count(),
            'rejected' => $complaints->where('status', Complaint::STATUS_REJECTED)->count(),
        ];

        $byCategory = $complaints->groupBy('category')->map->count();

        return view('admin.reports.index', [
            'complaints' => $complaints,
            'summary' => $summary,
            'byCategory' => $byCategory,
            'period' => $period,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Stream the same filtered data as a downloadable CSV file.
     * No extra package is needed - PHP's built-in fputcsv() handles this.
     */
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveRange($request);

        $complaints = Complaint::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $filename = 'ecs-report-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($complaints) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Reference #', 'Resident', 'Title', 'Category', 'Status', 'Location', 'Date Submitted', 'Date Resolved']);

            foreach ($complaints as $complaint) {
                fputcsv($handle, [
                    $complaint->reference_number,
                    $complaint->user->name ?? 'N/A',
                    $complaint->title,
                    $complaint->category_label,
                    $complaint->status_label,
                    $complaint->location,
                    $complaint->created_at->format('Y-m-d H:i'),
                    optional($complaint->resolved_at)->format('Y-m-d H:i') ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Turn the ?period= (and optional ?date_from=/?date_to=) query string
     * into a concrete [from, to] Carbon date range.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveRange(Request $request): array
    {
        $period = $request->input('period', 'month');

        $to = Carbon::now()->endOfDay();

        $from = match ($period) {
            'today' => Carbon::now()->startOfDay(),
            'week' => Carbon::now()->startOfWeek(),
            'custom' => $request->filled('date_from')
                ? Carbon::parse($request->input('date_from'))->startOfDay()
                : Carbon::now()->startOfMonth(),
            default => Carbon::now()->startOfMonth(),
        };

        if ($period === 'custom' && $request->filled('date_to')) {
            $to = Carbon::parse($request->input('date_to'))->endOfDay();
        }

        return [$from, $to, $period];
    }
}
