<?php

namespace App\Exports;

use App\Exports\Sheets\CommentsSheet;
use App\Exports\Sheets\TimeEntriesSheet;
use App\Models\SharedDay;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class SharedDayExport implements  WithMultipleSheets
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $sharedDay;
    
    public function __construct(SharedDay $sharedDay)
    {
        $this->sharedDay = $sharedDay;
    }

    public function sheets(): array
    {
        return [
            new TimeEntriesSheet($this->sharedDay),
            new CommentsSheet($this->sharedDay)
        ];
        
    }

 
}
