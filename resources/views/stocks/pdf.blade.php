<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order {{ $purchase->po_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        
        /* Header */
        .header { width: 100%; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 16pt; font-weight: bold; color: #2563eb; }
        .po-title { float: right; font-size: 20pt; font-weight: bold; color: #888; }
        
        /* Info Supplier & Toko */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-label { font-weight: bold; width: 100px; }
        
        /* Tabel Barang */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Footer */
        .footer { width: 100%; margin-top: 50px; }
        .signature-box { float: right; width: 200px; text-align: center; }
        .signature-line { border-bottom: 1px solid #000; margin-top: 60px; }
        
        /* Total Box */
        .total-box { float: right; width: 250px; }
        .total-row { border-top: 2px solid #333; font-weight: bold; font-size: 12pt; padding-top: 5px; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <span class="company-name">TOKO ANDA JAYA</span>
        <span class="po-title">PURCHASE ORDER</span>
        <div style="clear: both;"></div>
        <small>Jl. Raya Bisnis No. 123, Kota Anda, Jawa Barat | Telp: 0812-3456-7890</small>
    </div>

    <table class="info-table">
        <tr>
            <td valign="top" width="50%">
                <strong>KEPADA YTH (SUPPLIER):</strong><br>
                <span style="font-size: 12pt; font-weight: bold;">{{ $purchase->supplier->name }}</span><br>
                {{ $purchase->supplier->address ?? 'Alamat tidak tersedia' }}<br>
                Telp: {{ $purchase->supplier->phone }}
            </td>
            <td valign="top" width="50%" style="text-align: right;">
                <table style="float: right;">
                    <tr>
                        <td class="info-label">No. PO</td>
                        <td>: {{ $purchase->po_number }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($purchase->date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Status</td>
                        <td>: {{ strtoupper($purchase->status) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="45%">Nama Barang</th>
                <th class="text-center" width="15%">Qty</th>
                <th class="text-right" width="15%">Harga Satuan</th>
                <th class="text-right" width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->details as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center">{{ $item->request_qty }} Pcs</td>
                    <td class="text-right">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->request_qty * $item->buy_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="total-box">
            <table width="100%">
                <tr>
                    <td class="text-right">Subtotal :</td>
                    <td class="text-right">Rp {{ number_format($purchase->total_estimated, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right">Pajak (0%) :</td>
                    <td class="text-right">Rp 0</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="total-row text-right">
                            Total : Rp {{ number_format($purchase->total_estimated, 0, ',', '.') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <div class="signature-box">
            <p>Hormat Kami,</p>
            <div class="signature-line"></div>
            <p>( Admin Pembelian )</p>
        </div>
    </div>

</body>
</html>