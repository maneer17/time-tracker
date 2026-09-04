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
    </style>
</head>
<body>

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="header">
        <h1>Shared Day</h1>
        <p class="date-range">{{ $sharedDay->date->format('Y-m-d') }}</p>
        {{-- date cast is 'date:Y-m-d' → Carbon instance → format() works --}}
    </div>

    {{-- ── Stats ───────────────────────────────────────────── --}}
    <table class="stats-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <p class="stat-label">Total Entries</p>
                <p class="stat-value">{{ $sharedDay->entries->count() }}</p>
                {{-- entries already loaded — count() on collection, no extra query --}}
            </td>
            <td class="highlight">
                <p class="stat-label-light">Total Time</p>
                <p class="stat-value-light">
                    {{ $sharedDay->total_time['hours'] }}<span class="stat-unit-light">h</span>
                    {{ $sharedDay->total_time['minutes'] }}<span class="stat-unit-light">m</span>
                </p>
                {{-- total_time is a computed Attribute — returns ['hours' => x, 'minutes' => y] --}}
            </td>
            <td>
                <p class="stat-label">Comments</p>
                <p class="stat-value">{{ $sharedDay->comments->count() }}</p>
                {{-- comments already loaded — same pattern --}}
            </td>
        </tr>
    </table>

    {{-- ── Time Entries ─────────────────────────────────────── --}}
    <p class="section-title">Time Entries</p>

    @if($sharedDay->entries->isEmpty())
        <p class="no-data">No time entries for this day.</p>
    @else
        <table class="entries-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sharedDay->entries as $entry)
                {{-- $entry is SharedDayEntry — actual data lives in $entry->timeEntry --}}
                <tr>
                    <td>{{ $entry->timeEntry->label }}</td>
                    <td>{{ $entry->timeEntry->start_time->format('h:i A') }}</td>
                    <td>{{ $entry->timeEntry->end_time->format('h:i A') }}</td>
                    <td class="duration">
                        {{ $entry->timeEntry->time_taken['hours'] }}h
                        {{ $entry->timeEntry->time_taken['minutes'] }}m
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Comments ─────────────────────────────────────────── --}}
    <p class="section-title" style="margin-top: 20px;">Comments</p>

    @if($sharedDay->comments->isEmpty())
        <p class="no-data">No comments yet.</p>
    @else
        <table class="entries-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Comment</th>
                    <th>Author</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sharedDay->comments as $comment)
                <tr>
                    <td>{{ $comment->body }}</td>
                    <td class="comment-author">{{ $comment->author->name }}</td>
                    {{-- author relationship loaded via comments.author eager load --}}
                    <td class="comment-date">{{ $comment->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── Footer ───────────────────────────────────────────── --}}
    <div class="footer">
        Generated on {{ now()->format('Y-m-d') }}
    </div>

</body>
</html>