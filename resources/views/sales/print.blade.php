<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body { background: white !important; }
            .no-print { display: none !important; }
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            background: #f5f5f5;
            padding: 16px;
        }

        .receipt {
            width: 80mm;
            margin: 0 auto;
            background: white;
            border: 1px dashed #000;
            padding: 12px 8px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .divider { border-top: 1px dashed #000; margin: 4px 0; }
        .divider-bold { border-top: 2px dashed #000; margin: 6px 0; }

        .row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .items-header {
            display: flex;
            font-size: 11px;
            font-weight: bold;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .item-row {
            display: flex;
            font-size: 11px;
            padding: 2px 0;
            border-bottom: 1px dotted #ccc;
        }

        .item-name { width: 45%; }
        .item-price { width: 20%; text-align: center; font-size: 10px; }
        .item-weight { width: 15%; text-align: center; font-size: 10px; }
        .item-sub { width: 20%; text-align: right; }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0;
        }

        .footer-text { font-size: 10px; text-align: center; margin-top: 4px; }

        .no-print {
            text-align: center;
            margin-bottom: 12px;
        }

        .no-print .back-link {
            display: inline-block;
            margin-bottom: 12px;
            color: #666;
            font-size: 13px;
            text-decoration: none;
        }

        .no-print .back-link:hover { color: #333; }

        #bt-status {
            font-size: 12px;
            color: #555;
            min-height: 20px;
            margin: 8px 0 10px;
        }

        .btn-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary:hover:not(:disabled) { background: #15803d; }
        .btn-primary:disabled { background: #86efac; cursor: not-allowed; }

        .btn-secondary {
            background: white;
            color: #555;
            border: 1px solid #ccc;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-secondary:hover { background: #f5f5f5; }
    </style>
</head>
<body class="bg-stone-100">

    <div class="no-print">
        @if($sale->greenhouse)
        <a href="{{ route('sales.greenhouse', $sale->greenhouse) }}" class="back-link">← Kembali ke Kasir</a>
        @else
        <a href="{{ route('sales.index') }}" class="back-link">← Kembali</a>
        @endif
        <div id="bt-status"></div>
        <div class="btn-group">
            <button id="bt-print-btn" class="btn-primary" onclick="btPrint()">🖨 Cetak via Bluetooth</button>
            <button class="btn-secondary" onclick="window.print()">Cetak Browser</button>
        </div>
    </div>

    <div class="receipt">

        {{-- Header --}}
        <div class="header-text">
            <div class="shop-name">{{ setting('namacv', 'GREENHOUSE') }}</div>
            <div class="shop-address">{{ setting('alamat', 'Alamat tidak diatur') }}</div>
            <div class="shop-phone">Telp: {{ setting('telp', '-') }}</div>
        </div>

        <div class="divider"></div>

        {{-- Info --}}
        <div style="margin-bottom: 4px;">
            <div class="row">
                <span>No: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
                <span>{{ $sale->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="row">
                <span>Pukul: {{ $sale->created_at->format('H:i') }}</span>
                <span>Kasir: {{ $sale->user->name }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Pembeli --}}
        <div class="row" style="margin-bottom: 4px;">
            <span>Pembeli: <strong>{{ $sale->buyer_name }}</strong></span>
        </div>

        <div class="divider-bold"></div>

        {{-- Items Header --}}
        <div class="items-header">
            <span class="item-name">Nama</span>
            <span class="item-price">Harga</span>
            <span class="item-weight">Berat</span>
            <span class="item-sub">Sub</span>
        </div>

        {{-- Items --}}
        @foreach($sale->items as $item)
        <div class="item-row">
            <span class="item-name">{{ $item->melonVariety->name }}</span>
            <span class="item-price">{{ number_format($item->price_per_kg, 0, ',', '.') }}</span>
            <span class="item-weight">{{ number_format($item->weight_kg, 1, ',', '.') }}kg</span>
            <span class="item-sub">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach

        <div class="divider-bold"></div>

        {{-- Total --}}
        <div class="total-row">
            <span>TOTAL</span>
            <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        {{-- Footer --}}
        <div class="footer-text">
            <div style="font-weight: bold;">TERIMA KASIH</div>
            <div>Barang yang sudah dibeli</div>
            <div>tidak dapat dikembalikan</div>
        </div>

        @if($sale->greenhouse)
        <div style="font-size: 9px; text-align: center; margin-top: 6px; color: #999;">
            {{ $sale->greenhouse->name }} ({{ $sale->greenhouse->code }})
        </div>
        @endif

    </div>

    {{-- Data receipt untuk JS --}}
    @php
        $receiptArray = [
            'id'         => $sale->id,
            'date'       => $sale->created_at->format('d/m/Y'),
            'time'       => $sale->created_at->format('H:i'),
            'cashier'    => $sale->user->name,
            'buyer'      => $sale->buyer_name,
            'total'      => (float) $sale->total,
            'greenhouse' => $sale->greenhouse
                                ? $sale->greenhouse->name . ' (' . $sale->greenhouse->code . ')'
                                : null,
            'items' => $sale->items->map(fn($i) => [
                'name'     => $i->melonVariety->name,
                'price'    => (float) $i->price_per_kg,
                'weight'   => (float) $i->weight_kg,
                'subtotal' => (float) $i->subtotal,
            ])->values(),
        ];
    @endphp

    <script>
        const RECEIPT = @json($receiptArray);

        @php
            $kasirUrl = $sale->greenhouse
                ? route('sales.greenhouse', $sale->greenhouse)
                : route('sales.index');
        @endphp
        const KASIR_URL   = @json($kasirUrl);
        const IS_AUTO_PRINT = {{ session('auto_print') ? 'true' : 'false' }};

        // UUID printer BLE generik (China). Ganti jika merk berbeda.
        const BT_SERVICE        = '000018f0-0000-1000-8000-00805f9b34fb';
        const BT_CHARACTERISTIC = '00002af1-0000-1000-8000-00805f9b34fb';

        // ------------------------------------------------------------------
        // ESC/POS builder
        // ------------------------------------------------------------------
        function buildEscPos(r) {
            const enc = new TextEncoder();
            const b   = [];
            const t   = s => [...enc.encode(s)];

            const INIT    = [0x1B, 0x40];          // initialize printer
            const CENTER  = [0x1B, 0x61, 0x01];    // align center
            const LEFT    = [0x1B, 0x61, 0x00];    // align left
            const BOLD_ON = [0x1B, 0x45, 0x01];
            const BOLD_OFF= [0x1B, 0x45, 0x00];
            const CUT     = [0x1D, 0x56, 0x41, 0x03]; // partial cut

            const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
            const D  = '-'.repeat(32);

            b.push(...INIT);

            // Header
            b.push(...CENTER, ...BOLD_ON, ...t('BINCO RAN INDOFARM\n'), ...BOLD_OFF);
            b.push(...t('Suruh, Kayuapak, Polokarto\n'));
            b.push(...t('Sukoharjo, 57555\n'));

            // Info
            b.push(...LEFT, ...t(D + '\n'));
            b.push(...t('No: ' + String(r.id).padStart(6, '0') + '\n'));
            b.push(...t('Tgl: ' + r.date + '  Pukul: ' + r.time + '\n'));
            b.push(...t('Kasir: ' + r.cashier + '\n'));
            b.push(...t('Pembeli: ' + r.buyer + '\n'));
            b.push(...t(D + '\n'));

            // Items
            for (const item of r.items) {
                b.push(...t(item.name + '\n'));
                b.push(...t('  ' + rp(item.price) + ' x ' + item.weight + 'kg\n'));
                // subtotal rata kanan dalam 32 karakter
                const subStr = rp(item.subtotal);
                b.push(...t(subStr.padStart(32) + '\n'));
            }

            // Total
            b.push(...t('='.repeat(32) + '\n'));
            b.push(...BOLD_ON, ...t('TOTAL: ' + rp(r.total) + '\n'), ...BOLD_OFF);

            // Footer
            b.push(...CENTER, ...t(D + '\n'));
            b.push(...t('TERIMA KASIH\n'));
            b.push(...t('Barang tidak dapat dikembalikan\n'));
            if (r.greenhouse) b.push(...t(r.greenhouse + '\n'));

            // Feed + cut
            b.push(0x0A, 0x0A, 0x0A, ...CUT);

            return new Uint8Array(b);
        }

        // ------------------------------------------------------------------
        // Kirim data ke BLE printer dalam potongan kecil (MTU ~20 byte)
        // ------------------------------------------------------------------
        async function sendChunked(characteristic, data, chunkSize = 20) {
            for (let i = 0; i < data.length; i += chunkSize) {
                await characteristic.writeValue(data.slice(i, i + chunkSize));
            }
        }

        // ------------------------------------------------------------------
        // Main: connect BLE → kirim ESC/POS
        // ------------------------------------------------------------------
        async function btPrint() {
            const statusEl = document.getElementById('bt-status');
            const btn      = document.getElementById('bt-print-btn');

            if (!navigator.bluetooth) {
                statusEl.innerHTML = '⚠️ Web Bluetooth tidak tersedia.<br>Gunakan <strong>Chrome di Android</strong> atau gunakan tombol Cetak Browser.';
                return;
            }

            btn.disabled = true;
            try {
                statusEl.textContent = '🔍 Mencari printer Bluetooth...';

                const device = await navigator.bluetooth.requestDevice({
                    filters: [{ services: [BT_SERVICE] }],
                    // Jika printer tidak muncul, ganti dengan:
                    // acceptAllDevices: true,
                    // optionalServices: [BT_SERVICE],
                });

                statusEl.textContent = '🔗 Menghubungkan ke ' + device.name + '...';
                const server = await device.gatt.connect();
                const service = await server.getPrimaryService(BT_SERVICE);
                const char = await service.getCharacteristic(BT_CHARACTERISTIC);

                statusEl.textContent = '🖨️ Mencetak...';
                await sendChunked(char, buildEscPos(RECEIPT));

                statusEl.textContent = '✅ Nota berhasil dicetak!';

                if (IS_AUTO_PRINT) {
                    setTimeout(() => { window.location.href = KASIR_URL; }, 1800);
                } else {
                    btn.disabled = false;
                }
            } catch (err) {
                if (err.name === 'NotFoundError') {
                    statusEl.textContent = 'ℹ️ Tidak ada printer dipilih.';
                } else {
                    statusEl.textContent = '❌ ' + (err.message || 'Gagal terhubung ke printer.');
                }
                btn.disabled = false;
            }
        }

        // ------------------------------------------------------------------
        // Saat halaman load: beri petunjuk jika dari "Bayar & Print"
        // ------------------------------------------------------------------
        window.addEventListener('load', () => {
            if (IS_AUTO_PRINT) {
                const statusEl = document.getElementById('bt-status');
                statusEl.innerHTML = '👆 Tap <strong>Cetak via Bluetooth</strong> untuk mencetak nota.';
            }
        });
    </script>

</body>
</html>
