<?php

namespace App\Http\Controllers\Api;

use App\Exports\SharedDayExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimeEntriesExport;
use App\Http\Requests\TimeEntryExportRequest;
use App\Http\Requests\ReportExportRequest;
use App\Http\Requests\DownloadChannelDataRequest;
use App\Http\Requests\ExportChannelDataRequest;
use Illuminate\Http\Request;
use App\Jobs\ExportChannelDataJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\SharedDayExportRequest;
use App\Models\Channel;
use App\Models\SharedDay;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ReportService;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller

{
    public function timeEntries(TimeEntryExportRequest $request)
{
    $validated = $request->validated();

    $export = new TimeEntriesExport(
        $validated['start_date'],
        $validated['end_date'],
        $request->user()
    );

    return match ($validated['file_extension']) {
        'xlsx' => Excel::download($export, 'time_entries.xlsx'),
        'csv'   => Excel::download($export, 'time_entries.csv'),
        'pdf'   => Excel::download($export, 'time_entries.pdf'),
    };
}

    public function reportExport(ReportExportRequest $request, ReportService $service)
    {
        $validated = $request->validated();
        $report = $service->generate(
            $request->user()->id,
            $validated['from'],
            $validated['to']
        );

        $pdf = Pdf::loadView('exports.report', [
            'stats'            => $report['quick_stats'],
            'mostUsedLabels'   => $report['most_used_labels'], // ← add this
            'from'             => $validated['from'],
            'to'               => $validated['to'],
            'total_time_chart' => $validated['total_time_chart'],
            'most_used_chart'  => $validated['most_used_chart'],
        ]);

        return $pdf->download('report.pdf');
    }


    public function sharedDay(SharedDayExportRequest $request, SharedDay $sharedDay){
        $sharedDay->load(['entries.timeEntry', 'comments.author']);
        $file_extension = $request->validated()['file_extension'];
        return match($file_extension) {
            'xlsx' => Excel::download(new SharedDayExport($sharedDay), 'shared-day-' . $sharedDay->date . '.xlsx'),
            'csv'  => Excel::download(new SharedDayExport($sharedDay), 'shared-day-' . $sharedDay->date . '.csv'),
            'pdf'  => Pdf::loadView('exports.shared-day', [
                            'sharedDay' => $sharedDay,
                    ])->download('shared-day-' . $sharedDay->date . '.pdf'),
        };
    }

    public function channelData(Channel $channel, ExportChannelDataRequest $request)
    {
        $this->authorize('update', $channel);
        // only the owner should be able to trigger a full channel export
        // confirm this matches your ChannelPolicy

        $file_extension = $request->validated()['file_extension'];

        dispatch(new ExportChannelDataJob($channel, $request->user(), $file_extension));

        return response()->json([
            'message' => 'Your channel data export is being prepared. You will be notified when it is ready.'
        ], 202);
    }

    public function download(string $user, string $filename)
    {
        return Storage::download("exports/{$user}/{$filename}");
    }


}
