<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    public function index()
    {
        $products = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'desc')  // ✅ Z-A
            ->get();

        return view('admin.qrcodes.index', compact('products'));
    }

    public function generate(Product $product)
    {
        $product->load('variants');
        $successCount = 0;

        foreach ($product->variants as $variant) {
            // ✅ PERBAIKAN: Gunakan short SKU (tanpa warna)
            $shortSku = $this->generateShortSku($variant->sku);
            $qrData = $shortSku;

            $qrPath = 'uploads/qrcodes/qr_'.$shortSku.'.png';
            $fullPath = storage_path('app/public/'.$qrPath);

            if ($this->generateQRCode($qrData, $fullPath)) {
                $variant->update(['qr_code' => $qrPath]);
                $successCount++;
            }
        }

        ActivityLogger::qr(auth()->user()->name . ' generate QR code untuk produk: ' . $product->name, [
            'product_id' => $product->id,
            'variant_count' => $successCount,
        ]);

        return redirect()->route('qrcodes.index')->with('success', 'QR Code berhasil digenerate!');
    }

    public function generateAll()
    {
        $variants = ProductVariant::where('is_active', true)
            ->whereNull('qr_code')
            ->with('product')
            ->get();

        $count = 0;
        foreach ($variants as $variant) {
            // ✅ PERBAIKAN: Gunakan short SKU
            $shortSku = $this->generateShortSku($variant->sku);
            $qrData = $shortSku;

            $qrPath = 'uploads/qrcodes/qr_'.$shortSku.'.png';
            $fullPath = storage_path('app/public/'.$qrPath);

            if ($this->generateQRCode($qrData, $fullPath)) {
                $variant->update(['qr_code' => $qrPath]);
                $count++;
            }
        }

        return back()->with('success', "QR Code berhasil digenerate untuk {$count} varian!");
    }

    private function generateQRCode($data, $filePath)
    {
        try {
            $dir = dirname($filePath);
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // Menggunakan API QR Server
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
            $params = [
                'size' => '300x300',
                'data' => $data,
                'margin' => '5',
                'format' => 'png',
            ];

            $response = Http::timeout(10)->get($apiUrl, $params);

            if ($response->successful()) {
                file_put_contents($filePath, $response->body());

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: '.$e->getMessage());

            return false;
        }
    }

    // PERBAIKAN 2: Method print menerima multiple variant IDs
    public function print(Request $request)
    {
        $variantIds = $request->input('variant_ids', []);

        if (empty($variantIds)) {
            return back()->with('error', 'Pilih minimal satu varian untuk dicetak!');
        }

        $variants = ProductVariant::whereIn('id', $variantIds)
            ->with('product')
            ->get();

        return view('admin.qrcodes.print', compact('variants'));
    }

    /**
     * Generate short SKU without color
     * Example: JER-N000-XXL-Put → JER-N000-XXL
     */
    private function generateShortSku(string $sku): string
    {
        $parts = explode('-', $sku);

        // Jika ada 3 atau lebih bagian, ambil semua kecuali yang terakhir (warna)
        if (count($parts) >= 3) {
            return implode('-', array_slice($parts, 0, -1));
        }

        return $sku;
    }
}
