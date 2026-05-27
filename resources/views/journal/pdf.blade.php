<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>General Journal - {{ $filterMonth }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
        .report-period {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }
        .journal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .journal-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .journal-table td {
            padding: 7px 10px;
            vertical-align: top;
        }
        .divider-row {
            border-top: 1px solid #ddd;
        }
        .credit-account {
            padding-left: 25px;
            color: #555;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer-summary {
            font-weight: bold;
            border-top: 1px solid #333;
            border-bottom: 3px double #333;
            background-color: #fafafa;
        }
        .footer-summary td {
            padding: 9px 10px;
        }
        .status-section {
            margin-top: 20px;
            font-size: 11px;
        }
        .status-balanced {
            color: #15803d;
            font-weight: bold;
        }
        .status-unbalanced {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">Berfa CMS</div>
        <div class="report-title">General Journal</div>
        <div class="report-period">
            Period: {{ \Carbon\Carbon::createFromFormat('Y-m', $filterMonth)->format('F Y') }}
        </div>
    </div>

    <table class="journal-table">
        <thead>
            <tr>
                <th style="width: 15%; text-align: left;">Trx Date</th>
                <th style="width: 45%; text-align: left;">COA</th>
                <th style="width: 10%; text-align: left;">Code</th>
                <th style="width: 15%; text-align: right;">Debit</th>
                <th style="width: 15%; text-align: right;">Credit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $prevEntryId = null;
            @endphp
            @forelse($details as $detail)
                @php
                    $isNewEntry = $detail->journal_entry_id !== $prevEntryId;
                    $prevEntryId = $detail->journal_entry_id;
                @endphp
                <tr class="{{ $isNewEntry && !$loop->first ? 'divider-row' : '' }}">
                    <td>
                        @if($isNewEntry)
                            <strong style="color: #111;">{{ \Carbon\Carbon::parse($detail->entry_date)->format('d/m/Y') }}</strong>
                            <div style="font-size: 9px; color: #777; margin-top: 2px;">{{ $detail->entry_description }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="{{ $detail->credit > 0 ? 'credit-account' : '' }}" style="font-weight: {{ $detail->credit > 0 ? 'normal' : 'bold' }};">
                            {{ $detail->account_name }}
                        </div>
                    </td>
                    <td style="color: #666;">
                        {{ $detail->account_code }}
                    </td>
                    <td class="text-right">
                        @if($detail->debit > 0)
                            Rp {{ number_format($detail->debit, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detail->credit > 0)
                            Rp {{ number_format($detail->credit, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px 10px; color: #777;">
                        No journal entries found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($details->isNotEmpty())
            <tfoot>
                <tr class="footer-summary">
                    <td colspan="3" style="text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px;">Total Summary</td>
                    <td class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($details->isNotEmpty())
        <div class="status-section">
            Journal Status: 
            @if($totalDebit === $totalCredit)
                <span class="status-balanced">Balanced (Debits equal Credits)</span>
            @else
                <span class="status-unbalanced">Unbalanced (Debits do not equal Credits!)</span>
            @endif
        </div>
    @endif

</body>
</html>
