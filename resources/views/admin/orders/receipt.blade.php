<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Order - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #121214;
            padding: 20px;
            color: #e4e4e7;
        }

        .receipt-container {
            max-width: 380px;
            margin: 0 auto;
        }

        .receipt {
            background: white;
            color: black;
            padding: 24px;
            font-size: 12px;
            line-height: 1.4;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
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

        .marketplace-badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
            margin-top: 4px;
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
            font-family: sans-serif;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #10b981;
        }

        .btn-close {
            background: #4b5563;
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
                max-width: 100%;
                width: 80mm;
            }

            .receipt {
                padding: 5mm;
                box-shadow: none;
                border-radius: 0;
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
                <div class="marketplace-badge">{{ $order->marketplace }}</div>
            </div>

            <div class="divider"></div>

            <div class="item-row">
                <span>No. Order:</span>
                <span><strong>{{ $order->order_number }}</strong></span>
            </div>
            <div class="item-row">
                <span>Tanggal:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($order->courier_name)
            <div class="item-row">
                <span>Ekspedisi:</span>
                <span>{{ $order->courier_name }} {{ $order->courier_service ? '('.$order->courier_service.')' : '' }}</span>
            </div>
            @endif
            @if($order->tracking_number)
            <div class="item-row">
                <span>No. Resi:</span>
                <span>{{ $order->tracking_number }}</span>
            </div>
            @endif

            <div class="divider"></div>

            <div style="margin-bottom: 8px;">
                <p><strong>Penerima:</strong></p>
                <p>{{ $order->customer_name }} ({{ $order->customer_phone }})</p>
                <p style="font-size: 11px; color: #333; word-break: break-all;">
                    {{ $order->customer_address }}
                    @if($order->city || $order->province || $order->postal_code)
                        <br>{{ implode(', ', array_filter([$order->city, $order->province, $order->postal_code])) }}
                    @endif
                </p>
            </div>

            <div class="divider"></div>

            @foreach($order->items as $item)
            <div style="margin-bottom: 6px;">
                <div><strong>{{ $item->product_name }}</strong></div>
                <div style="font-size: 10px; color: #555;">{{ $item->variant_info }} @if($item->sku) [{{ $item->sku }}] @endif</div>
                <div class="item-row">
                    <span>{{ $item->quantity }} x {{ format_rupiah($item->unit_price) }}</span>
                    <span>{{ format_rupiah($item->subtotal) }}</span>
                </div>
            </div>
            @endforeach

            <div class="divider"></div>

            <div class="item-row">
                <span>Subtotal:</span>
                <span>{{ format_rupiah($order->subtotal) }}</span>
            </div>
            @if($order->shipping_cost > 0)
            <div class="item-row">
                <span>Ongkos Kirim:</span>
                <span>{{ format_rupiah($order->shipping_cost) }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="item-row">
                <span>Diskon:</span>
                <span>-{{ format_rupiah($order->discount) }}</span>
            </div>
            @endif
            <div class="item-row" style="font-size: 14px; font-weight: bold;">
                <span>TOTAL:</span>
                <span>{{ format_rupiah($order->total) }}</span>
            </div>

            @if($order->notes)
            <div class="divider"></div>
            <div style="font-size: 11px;">
                <p><strong>Catatan:</strong></p>
                <p>{{ $order->notes }}</p>
            </div>
            @endif

            <div class="divider"></div>

            <div class="text-center" style="font-size: 10px; margin-top: 10px;">
                <p><strong>Terima kasih atas pesanan Anda!</strong></p>
                <p style="margin-top: 5px;">Diproses oleh JSC-WMS</p>
                <p>{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-print" onclick="window.print()">🖨️ CETAK STRUK</button>
            <button class="btn btn-close" onclick="window.close()">❌ TUTUP</button>
        </div>
    </div>
</body>

</html>
