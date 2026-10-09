<?php

namespace App\Services;

use App\Models\MasterSku;
use App\Models\PrinterTemplate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class ZebraPrinterService
{
    protected $labelWidth = 95; // mm
    protected $labelHeight = 150; // mm
    protected $dpi = 300;

    /**
     * Generate HTML Preview untuk label dengan QR Code
     * QR Code saat discan hanya berisi SKU
     */
    public function generateHtmlPreview(MasterSku $sku, $qty = 1): string
    {
        // QR Code hanya berisi SKU untuk memudahkan picking/packing
        $qrCodeSvg = QrCode::size(150)->generate($sku->sku);

        return "
        <div style='width: 95mm; min-height: 150mm; border: 2px solid #000; padding: 8mm; font-family: Arial, sans-serif; background: white; box-sizing: border-box;'>
            <!-- Header: Article/Varian -->
            <div style='text-align: center; border-bottom: 2px solid #000; padding-bottom: 3mm; margin-bottom: 3mm;'>
                <div style='font-size: 18pt; font-weight: bold; text-transform: uppercase; color: #000; letter-spacing: 1px;'>
                    {$sku->article}
                </div>
                <div style='font-size: 10pt; color: #333; margin-top: 2mm;'>
                    {$sku->version} {$sku->sub_version}
                </div>
            </div>

            <!-- Product Name -->
            <div style='text-align: center; margin-bottom: 3mm;'>
                <div style='font-size: 11pt; font-weight: bold; color: #000;'>
                    {$sku->product_name}
                </div>
            </div>

            <!-- Size & Category Info -->
            <div style='display: flex; justify-content: space-between; margin-bottom: 3mm; font-size: 9pt;'>
                <div style='flex: 1; text-align: center; border: 1px solid #000; padding: 2mm;'>
                    <div style='font-size: 7pt; color: #666;'>SIZE</div>
                    <div style='font-size: 12pt; font-weight: bold;'>{$sku->size}</div>
                </div>
                <div style='flex: 1; text-align: center; border: 1px solid #000; padding: 2mm; margin-left: 2mm;'>
                    <div style='font-size: 7pt; color: #666;'>KATEGORI</div>
                    <div style='font-size: 10pt; font-weight: bold;'>{$sku->size_category}</div>
                </div>
            </div>

            <!-- QR Code (Hanya SKU saat discan) -->
            <div style='text-align: center; margin: 4mm 0;'>
                <div style='display: inline-block; border: 1px solid #000; padding: 2mm;'>
                    {$qrCodeSvg}
                </div>
                <div style='font-size: 8pt; font-family: monospace; margin-top: 2mm; color: #000;'>
                    {$sku->sku}
                </div>
            </div>

            <!-- Footer Info -->
            <div style='border-top: 1px dashed #000; padding-top: 2mm; margin-top: 3mm;'>
                <div style='display: flex; justify-content: space-between; font-size: 8pt;'>
                    <div>
                        <span style='color: #666;'>Qty:</span>
                        <span style='font-weight: bold;'>{$qty}</span>
                    </div>
                    <div>
                        <span style='color: #666;'>Date:</span>
                        <span style='font-weight: bold;'>" . date('d/m/Y') . "</span>
                    </div>
                </div>
                <div style='font-size: 7pt; color: #666; margin-top: 2mm; text-align: center;'>
                    JERSIC.ID WMS - Scan QR untuk picking/packing
                </div>
            </div>
        </div>
        ";
    }

    /**
     * Generate HTML untuk PDF Download (format seperti WAJIB VIDEO UNBOXING)
     * Layout landscape dengan 2 kolom identik untuk sobek
     */
    public function generatePdfHtml(MasterSku $sku, $qty = 1): string
    {
        // QR Code hanya berisi SKU
        $qrCodeSvg = QrCode::size(200)->generate($sku->sku);

        $labelContent = "
        <div style='width: 95mm; height: 145mm; border: 3px solid #000; padding: 5mm; font-family: Arial, sans-serif; background: white; box-sizing: border-box; display: flex; flex-direction: column;'>
            <!-- Header: Article/Varian - RED BOLD seperti WAJIB VIDEO UNBOXING -->
            <div style='text-align: center; border-bottom: 3px solid #DC2626; padding-bottom: 3mm; margin-bottom: 3mm;'>
                <div style='font-size: 16pt; font-weight: 900; text-transform: uppercase; color: #DC2626; letter-spacing: 1px;'>
                    {$sku->article}
                </div>
                <div style='font-size: 9pt; color: #000; margin-top: 2mm; font-weight: bold;'>
                    {$sku->version} {$sku->sub_version}
                </div>
            </div>

            <!-- Product Name - Bold Black -->
            <div style='text-align: center; margin-bottom: 3mm;'>
                <div style='font-size: 10pt; font-weight: 900; color: #000; text-transform: uppercase;'>
                    {$sku->product_name}
                </div>
            </div>

            <!-- Size & Category - Box Info -->
            <div style='display: flex; gap: 2mm; margin-bottom: 3mm;'>
                <div style='flex: 1; text-align: center; border: 2px solid #000; padding: 2mm; background: #F3F4F6;'>
                    <div style='font-size: 7pt; color: #666; font-weight: bold;'>SIZE</div>
                    <div style='font-size: 14pt; font-weight: 900; color: #000;'>{$sku->size}</div>
                </div>
                <div style='flex: 1; text-align: center; border: 2px solid #000; padding: 2mm; background: #F3F4F6;'>
                    <div style='font-size: 7pt; color: #666; font-weight: bold;'>KATEGORI</div>
                    <div style='font-size: 11pt; font-weight: 900; color: #000;'>{$sku->size_category}</div>
                </div>
            </div>

            <!-- QR Code - Large & Center (Hanya SKU saat discan) -->
            <div style='text-align: center; margin: 3mm 0; flex: 1; display: flex; flex-direction: column; justify-content: center;'>
                <div style='display: inline-block; margin: 0 auto;'>
                    {$qrCodeSvg}
                </div>
                <div style='font-size: 10pt; font-family: monospace; font-weight: 900; margin-top: 2mm; color: #000; letter-spacing: 1px;'>
                    {$sku->sku}
                </div>
            </div>

            <!-- Footer - Warning Style seperti WAJIB VIDEO UNBOXING -->
            <div style='border-top: 3px solid #DC2626; padding-top: 2mm; margin-top: auto;'>
                <div style='display: flex; justify-content: space-between; font-size: 8pt; margin-bottom: 2mm;'>
                    <div style='font-weight: bold;'>
                        <span style='color: #DC2626;'>QTY:</span> {$qty}
                    </div>
                    <div style='font-weight: bold;'>
                        <span style='color: #DC2626;'>DATE:</span> " . date('d/m/Y') . "
                    </div>
                </div>
                <div style='font-size: 7pt; color: #DC2626; font-weight: 900; text-align: center; border: 1px solid #DC2626; padding: 1mm;'>
                    SCAN QR CODE UNTUK PICKING/PACKING
                </div>
            </div>
        </div>
        ";

        // Layout landscape dengan 2 label identik (seperti WAJIB VIDEO UNBOXING)
        return "
        <div style='display: flex; gap: 5mm; justify-content: center; align-items: center; min-height: 150mm;'>
            {$labelContent}
            {$labelContent}
        </div>
        ";
    }

    /**
     * Generate ZPL Code untuk Zebra Printer (95x150mm portrait)
     * QR Code hanya berisi SKU
     */
    public function generateSingleLabel(MasterSku $sku, $qty = 1): string
    {
        $widthDots = $this->mmToDots($this->labelWidth);
        $heightDots = $this->mmToDots($this->labelHeight);

        // QR Code data: hanya SKU untuk memudahkan picking/packing
        $qrData = $sku->sku;

        return "^XA
^PW{$widthDots}
^LL{$heightDots}

^FO20,20^A0N,28,28^FD" . strtoupper($sku->article) . "^FS
^FO20,55^A0N,22,22^FD" . strtoupper($sku->product_name) . "^FS
^FO20,85^A0N,18,18^FDSize: " . $sku->size . " | Cat: " . $sku->size_category . "^FS
^FO20,110^A0N,18,18^FDQty: " . $qty . "^FS

^FO20,140^BY2,3,80^BQN,2,8^FDQA," . $qrData . "^FS

^FO20,240^A0N,16,16^FD" . $sku->sku . "^FS
^FO20,265^A0N,14,14^FD" . date('d/m/Y') . "^FS

^XZ";
    }

    /**
     * Konversi mm ke dots (300 DPI)
     */
    protected function mmToDots($mm)
    {
        return round(($mm / 25.4) * $this->dpi);
    }

    /**
     * Generate ZPL untuk multiple labels dalam 1 sheet (4x8 = 32 labels)
     */
    public function generateSheetLabels(array $items): string
    {
        $widthDots = $this->mmToDots($this->labelWidth);
        $heightDots = $this->mmToDots($this->labelHeight);

        $sheetWidth = $widthDots * 4;
        $sheetHeight = $heightDots * 8;

        $zpl = "^XA
^PW{$sheetWidth}
^LL{$sheetHeight}
";

        $row = 0;
        $col = 0;

        foreach ($items as $item) {
            $sku = $item['sku'];
            $qty = $item['qty'] ?? 1;

            $xPos = $col * $widthDots;
            $yPos = $row * $heightDots;

            $qrData = $sku->sku;

            $zpl .= "
^FO" . ($xPos + 20) . "," . ($yPos + 20) . "^A0N,28,28^FD" . strtoupper($sku->article) . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 55) . "^A0N,22,22^FD" . strtoupper($sku->product_name) . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 85) . "^A0N,18,18^FDSize: " . $sku->size . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 110) . "^A0N,18,18^FDQty: " . $qty . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 140) . "^BY2,3,80^BQN,2,8^FDQA," . $qrData . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 240) . "^A0N,16,16^FD" . $sku->sku . "^FS
^FO" . ($xPos + 20) . "," . ($yPos + 265) . "^A0N,14,14^FD" . date('d/m/Y') . "^FS
";

            $col++;
            if ($col >= 4) {
                $col = 0;
                $row++;
            }

            if ($row >= 8) {
                break;
            }
        }

        $zpl .= "^XZ";

        return $zpl;
    }

    /**
     * Download ZPL file untuk USB printing
     */
    public function downloadZplFile(string $zpl, string $filename = 'label.zpl')
    {
        return response($zpl)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Send ZPL ke network printer
     */
    public function sendToNetworkPrinter(string $zpl, string $printerIp, int $port = 9100): bool
    {
        try {
            $socket = @fsockopen($printerIp, $port, $errno, $errstr, 3);

            if (!$socket) {
                return false;
            }

            fwrite($socket, $zpl);
            fclose($socket);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
