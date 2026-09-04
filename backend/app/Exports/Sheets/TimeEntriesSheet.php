<?php
namespace App\Exports\Sheets;
use App\Models\SharedDay;
use App\Exports\Concerns\HasTimeEntryMapping;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TimeEntriesSheet implements FromCollection, WithHeadings, WithTitle, WithMapping{
    use HasTimeEntryMapping;
    
    protected $sharedDay;
    public function __construct(SharedDay $sharedDay)
    {
        $this->sharedDay = $sharedDay;
    }

    public function collection(): Collection
    {
        return $this->sharedDay->entries->map(fn($e) => $e->timeEntry);
    }

    public function title():string{
        return "Time Entries";
    }


}

