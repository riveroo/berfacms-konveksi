<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Vertical {{ $transaction->trx_id }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Arial Narrow', Arial, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.3;
            background-color: #fff;
            margin: 0;
            padding: 4mm 2mm;
            width: 54mm; /* safe printable area for 58mm */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Floating / Print controls */
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f3f4f6;
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 15px;
            font-family: sans-serif;
            font-size: 11px;
            width: 100vw;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .print-btn {
            background-color: #4f46e5;
            color: #fff;
            border: none;
            padding: 6px 14px;
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

        /* Invoice Container styling */
        .invoice-wrapper {
            width: 100%;
            box-sizing: border-box;
        }

        /* Centered Header Info */
        .header-section {
            text-align: center;
            margin-bottom: 8px;
        }

        .brand-title {
            font-size: 14px;
            font-weight: bold;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }

        .brand-logo-img {
            max-height: 35px;
            width: auto;
            margin: 0 auto 4px auto;
            display: block;
            filter: grayscale(100%);
        }

        /* Divider lines */
        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .divider-double {
            border: none;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            height: 3px;
            margin: 6px 0;
        }

        /* Metadata Details */
        .meta-list {
            margin: 4px 0;
            font-size: 9.5px;
        }

        .meta-row {
            display: flex;
            margin-bottom: 2px;
        }

        .meta-label {
            width: 16mm;
            flex-shrink: 0;
        }

        .meta-value {
            flex-grow: 1;
            word-break: break-all;
        }

        /* Items Section */
        .items-section {
            margin: 6px 0;
        }

        .item-block {
            margin-bottom: 5px;
            font-size: 9.5px;
        }

        .item-name {
            font-weight: bold;
            word-break: break-word;
        }

        .item-variant-size {
            font-size: 8.5px;
            color: #444;
            padding-left: 2px;
        }

        .item-calc-row {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
            font-size: 9px;
            padding-left: 2px;
        }

        /* Calculations */
        .calc-section {
            margin: 6px 0;
            font-size: 9.5px;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .calc-row.bold {
            font-weight: bold;
        }

        .calc-row.grand-total {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        /* Sisa yang harus dibayar */
        .outstanding-box {
            background-color: #000;
            color: #fff;
            padding: 3px;
            text-align: center;
            font-weight: bold;
            margin: 8px 0;
            font-size: 10px;
        }

        /* Footer Notes */
        .notes-section {
            margin-top: 8px;
            font-size: 8.5px;
            line-height: 1.3;
        }

        .payment-box {
            margin-top: 6px;
            padding: 4px;
            border: 1px dashed #000;
            font-size: 8.5px;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .thank-you-msg {
            text-align: center;
            margin-top: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        /* Print Override */
        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
                width: 58mm;
            }
        }
    </style>
</head>

<body>

    {{-- Floating Print Control Bar --}}
    <div class="no-print-bar">
        <span style="font-weight: 600;">Invoice Thermal Preview</span>
        <button onclick="window.print()" class="print-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print
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

    <div class="invoice-wrapper">
        {{-- Header Section --}}
        <div class="header-section">
            @if ($appearance && $appearance->header_logo)
                <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Logo" class="brand-logo-img">
            @else
                <div class="brand-title">UCUF KONVEKSI</div>
            @endif
            <div style="font-size: 8px;">Jasa Jahit & Konveksi Berkualitas</div>
        </div>

        <div class="divider"></div>

        {{-- Metadata List --}}
        <div class="meta-list">
            <div class="meta-row">
                <span class="meta-label">No Faktur</span>
                <span>: {{ $transaction->trx_id }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer</span>
                <span style="font-weight: bold;">: {{ optional($transaction->client)->client_name ?? '-' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Tgl Masuk</span>
                <span>: {{ $transaction->created_at->format('d-M-y') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Deadline</span>
                <span>: {{ $deadlineDate }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">CS</span>
                <span>: {{ $csName }}</span>
            </div>
        </div>

        <div class="divider-double"></div>

        {{-- Items List --}}
        <div class="items-section">
            @foreach ($transaction->details as $index => $item)
                <div class="item-block">
                    <div class="item-name">
                        {{ $index + 1 }}. 
                        @if (optional($item->variant)->variant_name)
                            {{ $item->variant->variant_name }}
                        @else
                            {{ optional($item->product)->product_name ?? 'N/A' }}
                        @endif
                    </div>
                    <div class="item-variant-size">
                        {{ optional($item->sizeOption)->name ? '(Size: ' . $item->sizeOption->name . ')' : '' }}
                    </div>
                    <div class="item-calc-row">
                        <span>
                            {{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }}
                            @if($item->discount > 0)
                                (-Rp {{ number_format($item->discount, 0, ',', '.') }})
                            @endif
                        </span>
                        <span style="font-weight: bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        {{-- Calculations Section --}}
        <div class="calc-section">
            <div class="calc-row" style="font-weight: bold;">
                <span>{{ __('transaction.total_qty') }}</span>
                <span>{{ $transaction->details->sum('quantity') }}</span>
            </div>
            <div class="calc-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($totalItemsPrice, 0, ',', '.') }}</span>
            </div>
            <div class="calc-row">
                <span>Discount</span>
                <span>
                    @if ($transaction->total_discount > 0)
                        -Rp {{ number_format($transaction->total_discount, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </span>
            </div>
            @if ($transaction->customer_balance > 0)
            <div class="calc-row">
                <span>Saldo Deposit</span>
                <span>-Rp {{ number_format($transaction->customer_balance, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="calc-row grand-total">
                <span>GRAND TOTAL</span>
                <span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
            </div>
            <div class="calc-row">
                <span>DP (Paid)</span>
                <span>
                    @if ($totalPaid > 0)
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </span>
            </div>
            <div class="calc-row bold">
                <span>Sisa Bayar</span>
                <span>
                    @if ($remaining > 0)
                        Rp {{ number_format($remaining, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </span>
            </div>
            <div class="calc-row" style="border-top: 1px dashed #000; padding-top: 2px; margin-top: 2px;">
                <span>Total Items</span>
                <span>{{ $totalQty }} pcs</span>
            </div>
        </div>

        {{-- Prominent Outstanding Banner if unpaid --}}
        @if ($remaining > 0)
            <div class="outstanding-box">
                Sisa Bayar: Rp {{ number_format($remaining, 0, ',', '.') }}
            </div>
        @else
            <div class="outstanding-box" style="background-color: #059669;">
                LUNAS
            </div>
        @endif

        <div class="divider"></div>

        {{-- Notes & Payment Instructions --}}
        <div class="notes-section">
            <div style="font-weight: bold; text-align: center; margin-bottom: 4px;">PENTING</div>
            <div style="text-align: center;">Pesanan diproses setelah melakukan pembayaran.</div>
            
            <div class="payment-box">
                <div class="payment-title">Info Pembayaran:</div>
                <div>Bank: {{ $appearance && $appearance->bank_logo ? 'BCA / Mandiri / Lainnya' : 'BCA' }}</div>
                <div>No. Rek: <strong>{{ $appearance && $appearance->bank_account_number ? $appearance->bank_account_number : '0561496870' }}</strong></div>
                <div>A.N: <strong>{{ $appearance && $appearance->bank_account_name ? $appearance->bank_account_name : 'M Dwi Dzulqarnain Hambali' }}</strong></div>
            </div>
        </div>

        <div class="thank-you-msg">
            Terima Kasih atas Kepercayaan Anda!<br>
            <span style="font-size: 7px; font-weight: normal; color: #777;">Powered by Berfa CMS</span>
        </div>
    </div>

    <script>
        // Automatically open print dialog on page load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
