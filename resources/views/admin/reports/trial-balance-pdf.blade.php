<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('finance.trial_balance') }} - {{ $period_label }}</title>
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
        .validation-section {
            margin-bottom: 15px;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .val-balanced {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .val-unbalanced {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .tb-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .tb-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .tb-table td {
            padding: 5px 10px;
        }
        .total-row {
            font-weight: bold;
            border-top: 1.5px solid #333;
            border-bottom: 3px double #333;
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">Berfa CMS</div>
        <div class="report-title">{{ __('finance.trial_balance') }}</div>
        <div class="report-period">
            {{ __('finance.accounting_period') }}: {{ $period_label }}
        </div>
    </div>

    <div class="validation-section {{ $isBalanced ? 'val-balanced' : 'val-unbalanced' }}">
        @if($isBalanced)
            ✅ {{ __('finance.trial_balance_balanced') }}
        @else
            ⚠ {{ __('finance.trial_balance_out_of_balance') }} ({{ __('finance.difference') }}: Rp {{ number_format($difference, 0, ',', '.') }})
        @endif
    </div>

    <table class="tb-table">
        <thead>
            <tr>
                <th style="width: 20%; text-align: left;">{{ __('finance.code') }}</th>
                <th style="width: 40%; text-align: left;">{{ __('finance.name') }}</th>
                <th style="width: 20%; text-align: right;">{{ __('finance.debit') }}</th>
                <th style="width: 20%; text-align: right;">{{ __('finance.credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['code'] }}</td>
                    <td style="{{ $row['parent_id'] ? 'padding-left: 25px;' : '' }}">{{ $row['name'] }}</td>
                    <td class="text-right">
                        {{ $row['debit'] !== null ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $row['credit'] !== null ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">{{ __('finance.total_summary') }}</td>
                <td class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
