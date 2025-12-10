<!DOCTYPE html>
<html>
<head>
    <title>Struk #{{ $trx->invoice_no }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 58mm; margin: 0; padding: 5px; }
        .center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 1000);">
    <div class="center">
        <strong>TOKO ANDA</strong><br>
        Jl. Raya No. 1<br>
    </div>
    <div class="line"></div>
    <div>
        No: {{ $trx->invoice_no }}<br>
        Kasir: {{ $trx->cashier->name }}<br>
        Tgl: {{ $trx->created_at->format('d/m/y H:i') }}
    </div>
    <div class="line"></div>
    @foreach($trx->details as $item)
        <div>{{ $item->product->name }}</div>
        <div class="flex">
            <span>{{ $item->qty }} x {{ number_format($item->price) }}</span>
            <span>{{ number_format($item->qty * $item->price) }}</span>
        </div>
    @endforeach
    <div class="line"></div>
    <div class="flex">
        <strong>TOTAL</strong>
        <strong>{{ number_format($trx->total_amount) }}</strong>
    </div>
    <div class="flex">
        <span>Tunai</span>
        <span>{{ number_format($trx->pay_amount) }}</span>
    </div>
    <div class="flex">
        <span>Kembali</span>
        <span>{{ number_format($trx->change_amount) }}</span>
    </div>
    <div class="line"></div>
    <div class="center">Terima Kasih</div>
</body>
</html>