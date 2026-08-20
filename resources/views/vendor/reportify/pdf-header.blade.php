<header style="width: 100%; border-bottom: 2px solid #000000; margin-bottom: 12px; padding-bottom: 6px; font-family: Arial, Helvetica, sans-serif;">
    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td style="text-align: left; vertical-align: bottom; border: none; padding: 0;">
                <div style="font-size: 14pt; font-weight: bold; color: #000000; text-transform: uppercase;">{{ $title ?? 'User Directory Report' }}</div>
                <div style="font-size: 8pt; color: #000000; margin-top: 2px;">{{ config('app.name', 'Laravel Application') }}</div>
            </td>
            <td style="text-align: right; vertical-align: bottom; font-size: 8pt; color: #000000; border: none; padding: 0;">
                <div>Generated: {{ date('Y-m-d H:i') }}</div>
            </td>
        </tr>
    </table>
    @if(!empty($header_html ?? ''))
        <div style="margin-top: 4px; font-size: 8pt; color: #000000;">{!! $header_html !!}</div>
    @endif
</header>
