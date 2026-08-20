<footer style="width: 100%; border-top: 1px solid #000000; padding-top: 5px; font-size: 8pt; font-family: Arial, Helvetica, sans-serif; color: #000000;">
    <table style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td style="text-align: left; border: none; padding: 0; color: #000000;">
                @if(!($hide_print_date ?? false))
                    Print Date: {{ now()->format('Y-m-d H:i:s') }}
                @endif
                @if(!($hide_print_by ?? false) && isset($authUserInfo))
                    | User: {{ $authUserInfo->name ?? 'System' }}
                @endif
            </td>
            <td style="text-align: right; border: none; padding: 0; color: #000000;">
                @if(!($hide_page_number ?? false))
                    Page {PAGENO} of {nbpg}
                @endif
            </td>
        </tr>
    </table>
    @if(!empty($additional_footer ?? ''))
        <div style="margin-top: 3px; color: #000000;">{!! $additional_footer !!}</div>
    @endif
</footer>
