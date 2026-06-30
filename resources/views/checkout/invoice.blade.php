<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $transaction->trx_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.4;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
        }

        /* Floating / Print controls */
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f3f4f6;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .print-btn {
            background-color: #4f46e5;
            color: #fff;
            border: none;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .print-btn:hover {
            background-color: #4338ca;
        }

        /* Invoice Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .faktur-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .faktur-label {
            font-size: 11px;
            font-weight: normal;
            color: #111;
        }

        .faktur-number-container {
            border: 1px solid #111;
            padding: 4px 15px;
            font-size: 16px;
            font-weight: bold;
            color: #ff0000;
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
        }

        .logo-container {
            text-align: right;
        }

        .logo-img {
            max-height: 48px;
            width: auto;
            display: block;
        }

        .logo-fallback {
            font-size: 20px;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }

        /* Outstanding balance bar */
        .outstanding-bar {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .outstanding-label {
            font-style: italic;
        }

        .outstanding-value {
            color: #ff0000;
            font-size: 16px;
            font-weight: 900;
            margin-left: 10px;
        }

        .header-line {
            border: none;
            border-top: 1.5px solid #111;
            margin: 5px 0 10px 0;
        }

        /* Metadata Grid */
        .metadata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .metadata-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }

        .meta-bold {
            font-weight: bold;
        }

        .meta-red {
            color: #ff0000;
            font-weight: bold;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th {
            font-weight: bold;
            font-style: italic;
            border-top: 1.5px solid #111;
            border-bottom: 1.5px solid #111;
            padding: 6px 4px;
            font-size: 10px;
            text-align: left;
        }

        .items-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        .items-table tr:last-child td {
            border-bottom: 1.5px solid #111;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Footer block */
        .footer-grid {
            width: 100%;
            margin-top: 5px;
        }

        .footer-grid td {
            vertical-align: top;
        }

        .note-text {
            color: #ff0000;
            font-weight: bold;
            font-size: 11px;
            margin-top: 10px;
        }

        .payment-label {
            font-weight: normal;
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .payment-info-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }

        .bca-logo-svg {
            width: 70px;
            height: auto;
        }

        .payment-details {
            font-size: 11px;
            line-height: 1.3;
        }

        .payment-details .rek-num {
            font-size: 13px;
            font-weight: bold;
        }

        .payment-details .rek-name {
            font-weight: bold;
        }

        /* Calculations Box */
        .calc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .calc-table td {
            padding: 4px 6px;
            font-size: 10px;
        }

        .calc-border-top {
            border-top: 1.5px solid #111;
        }

        .calc-double-bottom {
            border-bottom: 3px double #111;
        }

        .calc-bold {
            font-weight: bold;
        }

        /* Print styles */
        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                padding: 0;
                background-color: #fff;
            }

            .invoice-container {
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    {{-- Floating Print Control Bar --}}
    <div class="no-print-bar">
        <span style="font-weight: 600; font-size: 12px; color: #374151;">Invoice Document Preview</span>
        <button onclick="window.print()" class="print-btn">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                style="width: 12px; height: 12px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print Invoice
        </button>
    </div>

    @php
        $appearance = \App\Models\AppearanceSetting::first();
        $totalPaid = $transaction->payments->sum('amount');
        $remaining = $transaction->grand_total - $totalPaid;
        $csName = auth()->user() ? auth()->user()->name : 'Yana';
        $deadlineDate = $transaction->deadline ? \Carbon\Carbon::parse($transaction->deadline)->format('d-M-y') : '-';

        $totalQty = $transaction->details->sum('quantity');
        $totalItemsPrice = $transaction->details->sum('subtotal');
    @endphp

    <div class="invoice-container">
        {{-- Invoice Header --}}
        <div class="invoice-header">
            <div class="faktur-box">
                <span class="faktur-label">No Faktur</span>
                <span class="faktur-label">:</span>
                <div class="faktur-number-container">
                    {{ $transaction->trx_id }}
                </div>
            </div>

            <div class="logo-container">
                @if ($appearance && $appearance->header_logo)
                    <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Logo" class="logo-img">
                @else
                    <div class="logo-fallback">UCUF KONVEKSI</div>
                @endif
            </div>
        </div>

        {{-- Outstanding balance bar --}}
        <div class="outstanding-bar">
            <span class="outstanding-label">Sisa yang harus DIBAYAR :</span>
            <span class="outstanding-value">
                @if ($remaining > 0)
                    Rp {{ number_format($remaining, 0, ',', '.') }}
                @else
                    Rp -
                @endif
            </span>
        </div>

        <hr class="header-line">

        {{-- Metadata Grid --}}
        <table class="metadata-table">
            <tr>
                <td style="width: 10%;">Costumer</td>
                <td style="width: 2%;">:</td>
                <td style="width: 48%;" class="meta-bold">{{ optional($transaction->client)->client_name ?? '-' }}</td>

                <td style="width: 12%;">Tgl Masuk</td>
                <td style="width: 2%;">:</td>
                <td style="width: 26%;">{{ $transaction->created_at->format('d-M-y') }}</td>
            </tr>
            <tr>
                <td>CS</td>
                <td>:</td>
                <td class="meta-red">{{ $csName }}</td>

                <td>Deadline</td>
                <td>:</td>
                <td>{{ $deadlineDate }}</td>
            </tr>
        </table>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 35%;">Pesanan</th>
                    <th style="width: 10%;">Size</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 10%;">Harga /pcs</th>
                    <th style="width: 10%;">discount /pcs</th>
                    <th style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNo = 1; @endphp
                @foreach ($transaction->details as $item)
                    <tr>
                        <td>{{ $rowNo++ }}</td>
                        <td>{{ optional($item->product)->code ?? '-' }}</td>
                        <td>
                            @if (optional($item->variant)->variant_name)
                                {{ $item->variant->variant_name }}
                            @else
                                {{ optional($item->product)->product_name ?? 'N/A' }}
                            @endif
                        </td>
                        <td>{{ optional($item->sizeOption)->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->discount > 0 ? number_format($item->discount, 0, ',', '.') : '-' }}</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                {{-- Fill empty rows up to exactly 10 rows for layout stability --}}
                @for ($i = $rowNo; $i <= 10; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor

                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; font-size: 10px; padding: 6px 4px;">{{ __('transaction.total_qty') }}</td>
                    <td style="font-weight: bold; font-size: 10px; padding: 6px 4px; text-align: left;">{{ $transaction->details->sum('quantity') }}</td>
                    <td colspan="3" style="padding: 6px 4px;">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        {{-- Footer grid containing payments and calculations --}}
        <table class="footer-grid">
            <tr>
                {{-- Left Column: Note and BCA Payment Info --}}
                <td style="width: 60%;">
                    <div class="note-text">
                        Note: Pesanan diproses setelah melakukan pembayaran
                    </div>

                    <div class="payment-label">
                        Pembayaran :
                    </div>
                    <div class="payment-info-box">
                        @if ($appearance && $appearance->bank_logo)
                            <img src="{{ asset('storage/' . $appearance->bank_logo) }}" alt="Bank Logo"
                                style="max-height: 35px; width: auto; object-contain: contain;">
                        @else
                            {{-- Clean SVG representation of BCA logo --}}
                            <svg class="bca-logo-svg" viewBox="0 0 100 35" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="100" height="35" rx="4" fill="#00569f" />
                                <text x="50" y="24" font-family="'Arial Black', sans-serif" font-size="20"
                                    font-weight="900" fill="#fff" text-anchor="middle" italic>BCA</text>
                            </svg>
                        @endif
                        <div class="payment-details">
                            rek. <span
                                class="rek-num">{{ $appearance && $appearance->bank_account_number ? $appearance->bank_account_number : '0561496870' }}</span><br>
                            an. <span
                                class="rek-name">{{ $appearance && $appearance->bank_account_name ? $appearance->bank_account_name : 'M Dwi Dzulqarnain Hambali' }}</span>
                        </div>
                    </div>
                </td>

                {{-- Right Column: Total Box Calculations --}}
                <td style="width: 40%;">
                    <table class="calc-table">
                        <tr>
                            <td style="width: 40%;" class="text-right">&nbsp;</td>
                            <td style="width: 25%;" class="text-center calc-bold">Subtotal</td>
                            <td style="width: 35%;" class="text-right calc-bold">Rp
                                {{ number_format($totalItemsPrice, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%;" class="text-right">&nbsp;</td>
                            <td style="width: 25%;" class="text-center calc-bold">Discount</td>
                            <td style="width: 35%;" class="text-right ">
                                @if ($transaction->total_discount > 0)
                                    Rp {{ number_format($transaction->total_discount, 0, ',', '.') }}
                                @else
                                    Rp -
                                @endif
                            </td>
                        </tr>
                        @if ($transaction->customer_balance > 0)
                        <tr>
                            <td style="width: 40%;" class="text-right">&nbsp;</td>
                            <td style="width: 25%;" class="text-center calc-bold">Saldo Deposit</td>
                            <td style="width: 35%;" class="text-right ">
                                Rp {{ number_format($transaction->customer_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr class="calc-border-top">
                            <td class="calc-bold" style="font-size: 11px;">GRAND TOTAL</td>
                            <td>&nbsp;</td>
                            <td class="text-right">
                                Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}

                            </td>
                        </tr>
                        <tr>
                            <td class="calc-bold">DP</td>
                            <td>&nbsp;</td>
                            <td class="text-right">
                                @if ($totalPaid > 0)
                                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                @else
                                    Rp -
                                @endif
                            </td>
                        </tr>


                        <tr class="calc-border-top calc-double-bottom">
                            <td class="calc-bold" style="font-size: 11px;">Sisa yang dibayar</td>
                            <td>&nbsp;</td>
                            <td class="text-right calc-bold" style="font-size: 11px;">
                                @if ($remaining > 0)
                                    Rp {{ number_format($remaining, 0, ',', '.') }}
                                @else
                                    Rp -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="calc-bold" style="font-size: 11px;">Total Barang</td>
                            <td>&nbsp;</td>
                            <td class="text-right calc-bold" style="font-size: 11px;">
                                {{ $transaction->details->sum('quantity') }} pcs
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
