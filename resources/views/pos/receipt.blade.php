<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #1a1f3a;
            padding: 20px;
            color: white;
        }

        .receipt-container {
            max-width: 320px;
            margin: 0 auto;
        }

        .receipt {
            background: white;
            color: black;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .controls {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            color: white;
        }

        .btn-print {
            background: #06b6d4;
        }

        .btn-new {
            background: #8b5cf6;
        }

        .btn-home {
            background: #6b7280;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .controls {
                display: none !important;
            }

            .receipt-container {
                max-width: 80mm;
            }

            .receipt {
                padding: 5mm;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt" id="receipt">
            <div class="text-center">
                <div class="logo">jersic.id</div>
                <p>Premium Jersey Store</p>
                <p style="font-size: 10px;">{{ $transaction->warehouse->address ?? 'Jakarta' }}</p>
                <p style="font-size: 10px;">Telp: {{ $transaction->warehouse->phone ?? '08123456789' }}</p>
            </div>

            <div class="divider"></div>

            <div class="item-row">
                <span>No:</span>
                <span><strong>{{ $transaction->invoice_number }}</strong></span>
            </div>
            <div class="item-row">
                <span>Tanggal:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="item-row">
                <span>Kasir:</span>
                <span>{{ $transaction->cashier->name }}</span>
            </div>
            <div class="item-row">
                <span>Customer:</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>

            <div class="divider"></div>

            @foreach($transaction->items as $item)
            <div style="margin-bottom: 4px;">
                <div><strong>{{ $item->product_name }}</strong></div>
                <div style="font-size: 10px; color: #666;">{{ $item->variant_name }}</div>
                <div class="item-row">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach

            <div class="divider"></div>

            <div class="item-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaction->discount_amount > 0)
            <div class="item-row">
                <span>Diskon:</span>
                <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="item-row">
                <span>Pajak ({{ $transaction->tax_rate }}%):</span>
                <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
            <div class="item-row" style="font-size: 14px; font-weight: bold;">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>

            <div class="divider"></div>

            <div class="item-row">
                <span>{{ ucfirst($transaction->payment_method) }}:</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="item-row">
                <span>Kembali:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>

            <div class="divider"></div>

            <div class="text-center" style="margin-top: 10px;">
                <p style="font-size: 10px; margin-bottom: 8px;">Scan untuk E-Receipt & Promo:</p>
                @if($transaction->qr_code_url)
                <img src="{{ asset('storage/' . $transaction->qr_code_url) }}" style="width: 120px; height: 120px; margin: 0 auto;">
                @endif
            </div>

            <div class="divider"></div>

            <div class="text-center" style="font-size: 10px; margin-top: 10px;">
                <p><strong>Terima kasih telah berbelanja</strong></p>
                <p>di jersic.id 🏆</p>
                <p style="margin-top: 5px;">Barang yang sudah dibeli</p>
                <p>tidak dapat ditukar/dikembalikan</p>
                <p>kecuali ada cacat produksi.</p>
                <p style="margin-top: 5px;">www.jersic.id</p>
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-print" onclick="window.print()">🖨️ CETAK STRUK</button>
            <a href="{{ route('pos.index') }}" class="btn btn-new">🛒 Transaksi Baru</a>
            <a href="{{ route('dashboard') }}" class="btn btn-home">🏠 Dashboard</a>
        </div>
    </div>
</body>

</html>