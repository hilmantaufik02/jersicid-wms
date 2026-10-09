<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code</title>
    <style>
        @page {
            size: 92mm 150mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fff;
        }

        .page {
            width: 92mm;
            height: 150mm;
            padding: 2mm;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(5, 1fr);
            gap: 1mm;
            page-break-after: always;
        }

        .qr-label {
            border: 0.1px solid #000;
            padding: 1mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .product-name {
            font-size: 4pt;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 0.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .variant-info {
            font-size: 4pt;
            color: #333;
            margin-bottom: 0.5mm;
        }

        .qr-img {
            width: 16mm;
            height: 16mm;
            object-fit: contain;
            margin: 0.5mm 0;
        }

        .sku {
            font-size: 4.5pt;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            margin-top: 0.5mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }

        /* Tampilan Preview di Layar */
        @media screen {
            body {
                background: #f3f4f6;
                padding: 20px;
            }

            .page {
                background: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                margin: 0 auto 20px auto;
            }

            .no-print {
                text-align: center;
                margin-bottom: 20px;
                padding: 10px;
                background: #fff;
                border-radius: 8px;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            🖨️ Print Sekarang
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            Tutup
        </button>
        <p style="margin-top: 10px; font-size: 12px; color: #666;">
            Total Label: {{ $variants->count() }} | Halaman: {{ ceil($variants->count() / 20) }}
        </p>
    </div>

    @php
    // Bagi varian menjadi chunk 20 (4 kolom x 5 baris) per halaman
    $pages = $variants->chunk(20);

    // Helper function untuk short SKU
    if (!function_exists('getShortSku')) {
    function getShortSku($sku) {
    $parts = explode('-', $sku);
    if (count($parts) >= 3) {
    return implode('-', array_slice($parts, 0, -1));
    }
    return $sku;
    }
    }
    @endphp

    @foreach($pages as $pageVariants)
    <div class="page">
        @foreach($pageVariants as $variant)
        <div class="qr-label">
            <div class="product-name">{{ $variant->product->name }}</div>
            <div class="variant-info">Size: {{ $variant->size }} | Color: {{ $variant->color }}</div>

            @if($variant->qr_code && Storage::disk('public')->exists($variant->qr_code))
            <img src="{{ asset('storage/' . $variant->qr_code) }}" class="qr-img" alt="QR">
            @else
            <div style="width: 16mm; height: 16mm; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 8px;">No QR</div>
            @endif

            {{-- ✅ PERBAIKAN: Gunakan function helper, bukan $this --}}
            <div class="sku">{{ getShortSku($variant->sku) }}</div>
        </div>
        @endforeach

        {{-- Isi sisa grid kosong jika varian kurang dari 20 --}}
        @for($i = 0; $i < (20 - $pageVariants->count()); $i++)
            <div class="qr-label" style="border: 0.5px dashed #ccc; opacity: 0.3;">
                <span style="font-size: 8px;"></span>
            </div>
            @endfor
    </div>
    @endforeach

</body>

</html>