<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss - {{ $period_label }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 0.5px;
        }
        .report-period {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary-card {
            width: 25%;
            padding: 12px;
            border: 1px solid #ddd;
            background-color: #fafafa;
        }
        .summary-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .statement-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 12px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .statement-table td {
            padding: 8px 12px;
            vertical-align: middle;
        }
        .section-header {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            background-color: #fafafa;
            border-bottom: 1px solid #eee;
        }
        .account-row {
            border-bottom: 1px solid #f9f9f9;
        }
        .account-name {
            padding-left: 25px;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #fafafa;
        }
        .net-profit-row {
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid #333;
            border-bottom: 3px double #333;
            background-color: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-balanced {
            color: #15803d;
            font-weight: bold;
        }
        .status-loss {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">Berfa CMS</div>
        <div class="report-title">Profit & Loss Statement</div>
        <div class="report-period">
            Period: {{ $period_label }}
        </div>
    </div>

    {{-- Summary Metadata Block --}}
    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="summary-title">Total Income</div>
                <div class="summary-value" style="color: #16a34a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="border-left: none;">
                <div class="summary-title">Total Expenses</div>
                <div class="summary-value" style="color: #dc2626;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="border-left: none;">
                <div class="summary-title">Net Profit / Loss</div>
                <div class="summary-value {{ $netProfit >= 0 ? 'status-balanced' : 'status-loss' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="border-left: none;">
                <div class="summary-title">Profit Margin</div>
                <div class="summary-value" style="color: #4f46e5;">{{ number_format($profitMargin, 2, ',', '.') }}%</div>
            </td>
        </tr>
    </table>

    {{-- Profit & Loss Table --}}
    <table class="statement-table">
        <thead>
            <tr>
                <th style="width: 50%; text-align: left;">Category / Account</th>
                <th style="width: 20%; text-align: left;">Code</th>
                <th style="width: 30%; text-align: right;">Balance</th>
            </tr>
        </thead>
        <tbody>
            {{-- INCOME SECTION --}}
            <tr class="section-header">
                <td colspan="3" style="padding: 10px 12px;">Income</td>
            </tr>
            @forelse($revenueAccounts as $account)
                <tr class="account-row">
                    <td class="account-name">{{ $account->name }}</td>
                    <td style="color: #666;">{{ $account->code }}</td>
                    <td class="text-right font-semibold" style="color: #111;">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="account-name" style="color: #777; font-style: italic;">No active income accounts.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td style="padding-left: 15px; font-size: 10px; text-transform: uppercase;">Total Income</td>
                <td></td>
                <td class="text-right" style="color: #16a34a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>

            {{-- EXPENSES SECTION --}}
            <tr class="section-header">
                <td colspan="3" style="padding: 10px 12px;">Expenses</td>
            </tr>
            @forelse($expenseAccounts as $account)
                <tr class="account-row">
                    <td class="account-name">{{ $account->name }}</td>
                    <td style="color: #666;">{{ $account->code }}</td>
                    <td class="text-right font-semibold" style="color: #111;">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="account-name" style="color: #777; font-style: italic;">No active expense accounts.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td style="padding-left: 15px; font-size: 10px; text-transform: uppercase;">Total Expenses</td>
                <td></td>
                <td class="text-right" style="color: #dc2626;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>

            {{-- NET PROFIT SECTION --}}
            <tr class="net-profit-row">
                <td style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Net Profit / Loss</td>
                <td></td>
                <td class="text-right {{ $netProfit >= 0 ? 'status-balanced' : 'status-loss' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
