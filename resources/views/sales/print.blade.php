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

        .no-print button {
            background: #16a34a;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .no-print button:hover { background: #15803d; }

        .no-print .back-link {
            display: inline-block;
            margin-bottom: 12px;
            color: #666;
            font-size: 13px;
            text-decoration: none;
        }

        .no-print .back-link:hover { color: #333; }
    </style>
</head>
<body>

    <div class="no-print">
        @if($sale->greenhouse)
        <a href="{{ route('sales.greenhouse', $sale->greenhouse) }}" class="back-link">← Kembali ke Kasir</a>
        @else
        <a href="{{ route('sales.index') }}" class="back-link">← Kembali</a>
        @endif
        <br>
        <button onclick="window.print()">🖨 Cetak Sekarang</button>
        @if(session('auto_print'))
        <div style="font-size: 12px; color: #666; margin-top: 8px;">
            Printer sedang memproses...
        </div>
        @endif
    </div>

    @if(session('auto_print'))
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 500);
        });
    </script>
    @endif

    <div class="receipt">

        {{-- Header --}}
        <div class="text-center" style="margin-bottom: 4px;">
            <div style="font-size: 16px; font-weight: bold; letter-spacing: 0.5px;">BINCO RAN INDOFARM</div>
            <div style="font-size: 10px;">Suruh, Kayuapak, Polokarto, Sukoharjo, 57555</div>
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

</body>
</html>