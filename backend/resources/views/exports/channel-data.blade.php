{{-- resources/views/exports/channel-data.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .entries-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .entries-table th {
            font-size: 10px;
            font-weight: bold;
            color: #A0A0A0;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 12px;
            background-color: #F9F7F2;
            border: 1px solid #F0F0F0;
            text-align: left;
        }

        .entries-table td {
            font-size: 13px;
            color: #4A4A4A;
            padding: 10px 12px;
            border: 1px solid #F0F0F0;
            vertical-align: top;
        }

        .entries-table tr:nth-child(even) td {
            background-color: #FDFCFB;
        }

        .duration {
            font-weight: bold;
            color: #D4A373;
        }

        .comment-author {
            font-weight: bold;
            color: #5A7D5A;
        }

        .comment-date {
            font-size: 11px;
            color: #A0A0A0;
        }

        .no-data {
            font-size: 12px;
            color: #A0A0A0;
            font-style: italic;
            padding: 12px 0;
        }

        /* page-break-before forces DomPDF to start a new page
           before this element — keeps each section on its own page(s) */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="header">
        <h1>{{ $channel->name }}  </h1>
        <p class="date-range">Channel Data Export — Generated {{ now()->format('Y-m-d') }} </p>
    </div>

    {{-- ── Overview Stats ───────────────────────────────────── --}}
    <table class="stats-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <p class="stat-label">Shared Days </p>
                <p class="stat-value">{{ $channel->sharedDays->count() }} </p>
            </td>
            <td class="highlight">
                <p class="stat-label-light">Members   </p>
                <p class="stat-value-light">{{ $channel->members->count() }} </p>
            </td>
            <td>
                <p class="stat-label">Total Comments   </p>
                <p class="stat-value">
                    {{ $channel->sharedDays->sum(fn($day) => $day->comments->count()) }}
                </p>
            </td>
        </tr>
    </table>

    {{-- ── Shared Days ───────────────────────────────────────── --}}
    <p class="section-title">Shared Days</p>

    @if($channel->sharedDays->isEmpty())
        <p class="no-data">No shared days in this channel.</p>
    @else
        <table class="entries-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Time</th>
                    <th>Entries</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($channel->sharedDays as $day)
                <tr>
                    <td>{{ $day->date->format('Y-m-d') }}</td>
                    <td class="duration">
                        {{ $day->total_time['hours'] }}h {{ $day->total_time['minutes'] }}m
                    </td>
                    <td>{{ $day->entries->count() }}</td>
                    <td>{{ $day->comments->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Comments ──────────────────────────────────────────── --}}
    <div class="page-break"></div>
    {{-- new page — comments can get long, keep them separate from the days table --}}

    <p class="section-title">Comments</p>

    @php
        // flatten comments from every shared day into one collection
        // each comment keeps a reference to which day it belongs to
        $allComments = $channel->sharedDays->flatMap(fn($day) =>
            $day->comments->map(fn($comment) => (object)[
                'date'    => $day->date,
                'author'  => $comment->author,
                'body'    => $comment->body,
                'created' => $comment->created_at,
            ])
        );
    @endphp

    @if($allComments->isEmpty())
        <p class="no-data">No comments in this channel.</p>
    @else
        <table class="entries-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Comment</th>
                    <th>Author</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allComments as $comment)
                <tr>
                    <td>{{ $comment->date->format('Y-m-d') }}</td>
                    <td>{{ $comment->body }}</td>
                    <td class="comment-author">{{ $comment->author->name }}</td>
                    <td class="comment-date">{{ $comment->created->format('Y-m-d h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Members ──────────────────────────────────────────── --}}
    <div class="page-break"></div>

    <p class="section-title">Members</p>

    @if($channel->members->isEmpty())
        <p class="no-data">No members in this channel.</p>
    @else
        <table class="entries-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    {{-- owner shown first, separately, since they're not in $channel->members --}}
                    <td>{{ $channel->owner->name }} (Owner)</td>
                    <td>{{ $channel->owner->email }}</td>
                    <td>{{ $channel->created_at->format('Y-m-d') }}</td>
                </tr>
                @foreach($channel->members as $member)
                <tr>
                    <td>{{ $member->user->name }}</td>
                    <td>{{ $member->user->email }}</td>
                    <td>{{ $member->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Footer ───────────────────────────────────────────── --}}
    <div class="footer">
        Generated on {{ now()->format('Y-m-d') }} — {{ $channel->name }}
    </div>

</body>
</html>