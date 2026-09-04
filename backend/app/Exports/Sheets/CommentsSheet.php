<?php
namespace App\Exports\Sheets;
use App\Models\SharedDay;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\Concerns\HasCommentMapping;

class CommentsSheet implements FromCollection, WithHeadings, WithTitle, WithMapping{
    use HasCommentMapping;
    
    protected $sharedDay;
    public function __construct(SharedDay $sharedDay)
    {
        $this->sharedDay = $sharedDay;
    }

    public function collection(): Collection
    {
        return $this->sharedDay->comments;
    }


}

