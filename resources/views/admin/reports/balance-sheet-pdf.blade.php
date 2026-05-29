<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - {{ $period_label }}</title>
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
        .bs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .bs-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .bs-table td {
            padding: 6px 10px;
            vertical-align: middle;
        }
        .category-row {
            background-color: #fafafa;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .subcategory-row {
            font-weight: bold;
            font-size: 10px;
            color: #111;
        }
        .account-row td {
            padding-left: 25px;
            border-bottom: 1px solid #f9f9f9;
        }
        .indent-2 {
            padding-left: 35px !important;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #fdfdfd;
        }
        .grand-total-row {
            font-weight: bold;
            border-top: 1px solid #333;
            border-bottom: 3px double #333;
            background-color: #f5f5f5;
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
        <div class="report-title">Balance Sheet Report</div>
        <div class="report-period">
            Period: {{ $period_label }} (Calculated from Jan 1st)
        </div>
    </div>

    <div class="validation-section {{ $isBalanced ? 'val-balanced' : 'val-unbalanced' }}">
        @if($isBalanced)
            ✅ Balance Sheet Balanced
        @else
            ⚠ Balance Sheet Out of Balance (Difference: Rp {{ number_format($difference, 0, ',', '.') }})
        @endif
    </div>

    <table class="bs-table">
        <thead>
            <tr>
                <th style="width: 50%; text-align: left;">Category / Account</th>
                <th style="width: 20%; text-align: left;">Code</th>
                <th style="width: 30%; text-align: right;">Balance</th>
            </tr>
        </thead>
        <tbody>
            {{-- ASSETS SECTION --}}
            <tr class="category-row">
                <td colspan="3">Assets</td>
            </tr>
            
            <tr class="subcategory-row">
                <td colspan="3" style="padding-left: 15px;">Current Assets</td>
            </tr>
            
            {{-- Cash Accounts --}}
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Cash and Cash Equivalents</td>
            </tr>
            @foreach($cashAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Cash and Cash Equivalents</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
            </tr>

            {{-- Accounts Receivable --}}
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Accounts Receivable</td>
            </tr>
            @foreach($arAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Accounts Receivable</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalAR, 0, ',', '.') }}</td>
            </tr>

            {{-- Inventory --}}
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Inventory</td>
            </tr>
            @foreach($inventoryAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Inventory</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalInventory, 0, ',', '.') }}</td>
            </tr>

            <tr class="total-row" style="background-color: #f0f0f0;">
                <td style="padding-left: 15px;">TOTAL CURRENT ASSETS</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalCurrentAssets, 0, ',', '.') }}</td>
            </tr>

            {{-- Non-Current Assets --}}
            <tr class="subcategory-row">
                <td colspan="3" style="padding-left: 15px;">Non-Current Assets</td>
            </tr>
            
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Property, Plant and Equipment</td>
            </tr>
            @foreach($ppeAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Property, Plant and Equipment</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalPPE, 0, ',', '.') }}</td>
            </tr>

            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Less: Accumulated Depreciation</td>
            </tr>
            @foreach($depreciationAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Accumulated Depreciation</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</td>
            </tr>

            <tr class="total-row" style="background-color: #f0f0f0;">
                <td style="padding-left: 15px;">TOTAL NON-CURRENT ASSETS</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalNonCurrentAssets, 0, ',', '.') }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL ASSETS</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
            </tr>

            {{-- LIABILITIES & EQUITY SECTION --}}
            <tr class="category-row" style="margin-top: 15px;">
                <td colspan="3">Liabilities & Equity</td>
            </tr>

            <tr class="subcategory-row">
                <td colspan="3" style="padding-left: 15px;">Current Liabilities</td>
            </tr>
            
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Accounts Payable</td>
            </tr>
            @foreach($apAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Accounts Payable</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalAP, 0, ',', '.') }}</td>
            </tr>

            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Accrued Expenses</td>
            </tr>
            @foreach($accruedLiabilities as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Accrued Expenses</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalAccrued, 0, ',', '.') }}</td>
            </tr>

            <tr class="total-row" style="background-color: #f0f0f0;">
                <td style="padding-left: 15px;">TOTAL CURRENT LIABILITIES</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalCurrentLiabilities, 0, ',', '.') }}</td>
            </tr>

            {{-- Equity --}}
            <tr class="subcategory-row">
                <td colspan="3" style="padding-left: 15px;">Equity</td>
            </tr>
            
            <tr class="subcategory-row" style="font-style: italic; color: #555;">
                <td colspan="3" style="padding-left: 25px;">Share Capital</td>
            </tr>
            @foreach($equityAccounts as $acc)
                <tr class="account-row">
                    <td class="indent-2">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row" style="font-size: 10px;">
                <td style="padding-left: 25px;">Total Share Capital</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalShareCapital, 0, ',', '.') }}</td>
            </tr>

            <tr class="account-row">
                <td style="padding-left: 25px; font-weight: bold; font-style: italic; color: #555;">Retained Earnings</td>
                <td></td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($retainedEarnings, 0, ',', '.') }}</td>
            </tr>

            <tr class="total-row" style="background-color: #f0f0f0;">
                <td style="padding-left: 15px;">TOTAL EQUITY</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL LIABILITIES & EQUITY</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
