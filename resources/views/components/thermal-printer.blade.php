<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('thermalPrinter', () => ({
            isPrinting: false,
            port: null,

            async connectAndPrint(sku, binCode) {
                if (!('serial' in navigator)) {
                    alert('Browser tidak mendukung Web Serial API. Gunakan Chrome/Edge versi terbaru.');
                    return;
                }

                this.isPrinting = true;

                try {
                    // 1. Fetch Data Label dari Backend
                    const response = await fetch(`/gudang/label/${sku}/${binCode}`);
                    const result = await response.json();
                    if (!result.success) throw new Error('Gagal mengambil data label');
                    
                    const data = result.data;

                    // 2. Request Serial Port (User harus memilih port USB printer)
                    this.port = await navigator.serial.requestPort();
                    await this.port.open({ baudRate: 9600 });

                    // 3. Format ESC/POS Commands
                    const encoder = new TextEncoder();
                    const commands = this.buildEscPosCommands(data);
                    
                    // 4. Send to Printer
                    const writer = this.port.writable.getWriter();
                    await writer.write(encoder.encode(commands));
                    writer.releaseLock();

                    // 5. Close Port
                    await this.port.close();
                    
                    // Optional: Show success toast
                    console.log('Label printed successfully!');

                } catch (error) {
                    console.error('Print error:', error);
                    alert('Gagal mencetak: ' + error.message);
                } finally {
                    this.isPrinting = false;
                }
            },

            buildEscPosCommands(data) {
                // ESC/POS Raw Commands
                const ESC = '\x1B';
                const GS  = '\x1D';
                
                let cmd = '';
                cmd += `${ESC}@`;             // Initialize printer
                cmd += `${ESC}a\x01`;         // Center alignment
                cmd += `${ESC}E\x01`;         // Bold ON
                
                cmd += `*** JERSIC.WMS ***\n`;
                cmd += `BIN LOCATION\n`;
                
                cmd += `${ESC}E\x00`;         // Bold OFF
                cmd += `${ESC}a\x00`;         // Left alignment
                
                cmd += `SKU : ${data.sku}\n`;
                cmd += `ITEM: ${data.product_name.substring(0, 20)}\n`;
                cmd += `QTY : ${data.qty} PCS\n`;
                cmd += `BATCH: ${data.batch_no}\n`;
                cmd += `DATE: ${data.date}\n`;
                
                cmd += `\n\n\n`;              // Feed lines
                cmd += `${GS}V\x00`;          // Cut paper
                
                return cmd;
            }
        }));
    });
</script>

<!-- Cara Penggunaan di Blade: -->
<!-- <button x-data="thermalPrinter()" @click="connectAndPrint('JER-ART01-XL', 'RAK-A-01-01')" :disabled="isPrinting" class="..."> -->
<!--     <span x-text="isPrinting ? 'Printing...' : 'Print Label'"></span> -->
<!-- </button> -->