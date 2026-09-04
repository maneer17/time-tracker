<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* ─── Base ─────────────────────────────────────────── */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #4A4A4A;
            margin: 0;
            padding: 30px;
            background-color: #ffffff;
        }

        /* ─── Header ────────────────────────────────────────── */
        .header {
            border-bottom: 2px solid #5A7D5A;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333333;
            margin: 0 0 6px 0;
        }

        .header .date-range {
            font-size: 12px;
            color: #8E9AAF;
            margin: 0;
        }

        /* ─── Section title ─────────────────────────────────── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #A0A0A0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 12px 0;
        }

        /* ─── Stats table ───────────────────────────────────── */
        /* 
            No flexbox/grid in DomPDF.
            Tables are the only reliable way to do columns.
            width="100%" makes it stretch full page width.
            cellpadding/cellspacing control spacing without CSS gaps.
        */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .stats-table td {
            width: 33%;
            padding: 16px;
            border: 1px solid #F0F0F0;
            background-color: #F9F7F2;
            vertical-align: top;
        }

        .stats-table td.highlight {
            background-color: #5A7D5A;
        }

        .stat-label {
            font-size: 10px;
            font-weight: bold;
            color: #A0A0A0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px 0;
        }

        .stat-label-light {
            font-size: 10px;
            font-weight: bold;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px 0;
        }

        .stat-value {
            font-size: 26px;
            font-weight: bold;
            color: #333333;
            margin: 0;
        }

        .stat-value-light {
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            margin: 0;
        }

        .stat-unit {
            font-size: 14px;
            font-weight: normal;
            color: #A0A0A0;
        }

        .stat-unit-light {
            font-size: 14px;
            font-weight: normal;
            color: rgba(255,255,255,0.6);
        }

        /* ─── Charts ────────────────────────────────────────── */
        .chart-section {
            margin-bottom: 30px;
        }

        .chart-title {
            font-size: 10px;
            font-weight: bold;
            color: #A0A0A0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #F0F0F0;
        }

        .chart-img {
            width: 100%;
            /* page-break-inside: avoid prevents DomPDF from
               splitting a chart across two pages */
            page-break-inside: avoid;
        }

        /* ─── Footer ────────────────────────────────────────── */
        .footer {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #F0F0F0;
            font-size: 11px;
            color: #C0C0C0;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="header">
        <h1>Time Report</h1>
        <p class="date-range">{{ $from }} → {{ $to }}</p>
    </div>

    {{-- ── Quick Stats ─────────────────────────────────────── --}}
    <p class="section-title">Quick Stats</p>

    {{-- 
        Table used for 3-column layout.
        DomPDF doesn't support flexbox or grid.
        Tables are the only reliable multi-column solution.
    --}}
    <table class="stats-table" cellpadding="0" cellspacing="0">
        <tr>

            {{-- Total Entries --}}
            <td>
                <p class="stat-label">Total Entries</p>
                <p class="stat-value">{{ $stats['total_entries'] }}</p>
            </td>

            {{-- Total Time --}}
            <td>
                <p class="stat-label">Total Time</p>
                <p class="stat-value">
                    {{ $stats['total_hours'] }}<span class="stat-unit">h</span>
                    {{ $stats['total_minutes'] }}<span class="stat-unit">m</span>
                </p>
            </td>

            {{-- Daily Average — highlighted with green background --}}
            <td class="highlight">
                <p class="stat-label-light">Daily Average</p>
                <p class="stat-value-light">
                    {{ $stats['avg_hours_per_day'] }}<span class="stat-unit-light">h</span>
                    {{ $stats['avg_minutes_per_day'] }}<span class="stat-unit-light">m</span>
                </p>
            </td>

        </tr>
    </table>

    {{-- ── Charts ───────────────────────────────────────────── --}}

    {{-- 
        $total_time_chart and $most_used_chart are base64 strings
        that came from Vue via the POST request.
        Format: "data:image/png;base64,iVBORw0KGgo..."
        DomPDF reads the base64 data directly — no HTTP request needed.
    --}}
{{-- ── Charts ───────────────────────────────────────────── --}}

    {{-- Time Distribution --}}
    <div class="chart-section">
        <p class="chart-title">Time Distribution by Label</p>
        <img src="{{ $total_time_chart }}" class="chart-img">
    </div>

    {{-- Most Used Labels --}}
    <div class="chart-section">
        <p class="chart-title">Most Used Labels</p>
        <img src="{{ $most_used_chart }}" class="chart-img" style="width: 50%; display: block; margin: 0 auto;">
        
        <table width="100%" cellpadding="8" cellspacing="0" style="margin-top: 16px;">
            @foreach($mostUsedLabels as $row)
            <tr>
                <td style="font-size: 13px; color: #4A4A4A;">{{ $row['label'] }}</td>
                <td style="font-size: 13px; font-weight: bold; color: #E07A5F; text-align: right;">{{ $row['percentage'] }}%</td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- ── Footer ───────────────────────────────────────────── --}}
    <div class="footer">
        Generated on {{ now()->format('Y-m-d') }}
    </div>

</body>
</html>