<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>General Ledger - {{ $period_label }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
        .report-period {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }
        .meta-section {
            margin-bottom: 15px;
            background-color: #fafafa;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 4px;
        }
        .meta-grid {
            width: 100%;
        }
        .meta-grid td {
            padding: 2px 5px;
            font-size: 10px;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .ledger-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .ledger-table td {
            padding: 6px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
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
            padding: 8px 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">Berfa CMS</div>
        <div class="report-title">General Ledger Report</div>
        <div class="report-period">
            Period: {{ $period_label }}
        </div>
    </div>

    <div class="meta-section">
        <table class="meta-grid">
            <tr>
                <td style="width: 15%; font-weight: bold;">Account:</td>
                <td style="width: 85%;">{{ $account->code }} - {{ $account->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Type:</td>
                <td>{{ ucfirst($account->type) }}</td>
            </tr>
        </table>
    </div>

    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 15%; text-align: left;">Trx Date</th>
                <th style="width: 15%; text-align: left;">COA Code</th>
                <th style="width: 25%; text-align: left;">Account Name</th>
                <th style="width: 15%; text-align: right;">Debit</th>
                <th style="width: 15%; text-align: right;">Credit</th>
                <th style="width: 15%; text-align: right;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                    <td>{{ $row['code'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td class="text-right">
                        @if($row['debit'] > 0)
                            Rp {{ number_format($row['debit'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row['credit'] > 0)
                            Rp {{ number_format($row['credit'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($row['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px 10px; color: #777; italic;">
                        No ledger entries found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rows) > 0)
            <tfoot>
                <tr class="footer-summary">
                    <td colspan="3">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

</body>
</html>
