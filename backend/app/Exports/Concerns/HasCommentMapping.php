<?php
namespace App\Exports\Concerns;
trait HasCommentMapping
{
    public function headings(): array
    {
        return [
            "Comment",
            "Author",
            "Date"
        ];
    }
    public function map($comment): array
    {
        return [
            $comment->body,
            $comment->author->name,
            $comment->created_at->format('Y-m-d')
        ];
    }

    public function title():string{
        return "Comments";
    }
}