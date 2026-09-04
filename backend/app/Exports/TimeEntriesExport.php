<?php

namespace App\Exports;
use App\Models\TimeEntry;
use App\Models\User;
use App\Exports\Concerns\HasTimeEntryMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimeEntriesExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable, HasTimeEntryMapping;
    public String $startDate, $endDate;
    public User $user;
    public function __construct(?string $startDate, ?string $endDate, User $user)
    {
        $this->user = $user;
        $this->startDate= $startDate;
        $this->endDate = $endDate;
    }

    #[Override]
    public function query()
    {
        return TimeEntry::query()
        ->inRange($this->startDate, $this->endDate)
        ->where('user_id', $this->user->id)
        ->latest();
    }
    public function styles(Worksheet $sheet)
    {
       return [
        1 => ['font' => ['bold' => true]],
       ];
    }
}
