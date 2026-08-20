<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'User List Report' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #000000;
            margin: 0 0 4px 0;
        }
        .subtitle {
            font-size: 9pt;
            color: #000000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #000000;
            font-size: 9pt;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #000000;
            font-size: 9pt;
            color: #000000;
        }
        .footer {
            margin-top: 20px;
            font-size: 8pt;
            color: #000000;
            text-align: center;
            border-top: 1px solid #000000;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    @php
        $items = $response ?? $data ?? [];
    @endphp

    <div class="header">
        <h1 class="title">{{ $title ?? 'User List Report' }}</h1>
        <div class="subtitle">Generated on {{ date('Y-m-d H:i') }} | Total Records: {{ count($items) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Name</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 12%;">Role</th>
                <th style="width: 15%;">Department</th>
                <th style="width: 13%;">Phone</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->department ?? '-' }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">
                        No user records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Report generated via Reportify Engine.
    </div>
</body>
</html>
