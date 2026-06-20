<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standar struk thermal 80mm */
            max-width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-2 { margin-top: 10px; }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
        }
        .item-notes {
            font-size: 10px;
            font-style: italic;
        }
        @media print {
            body {
                width: auto;
                padding: 0;
                margin: 0;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center mb-2">
        <h3 class="fw-bold" style="margin: 0 0 5px 0;">BON-OPS POS</h3>
        <div>Cabang Pusat</div>
        <div>Tlp: 0812-3456-7890</div>
    </div>

    <hr>

    <table class="mb-1">
        <tr>
            <td>No. Order</td>
            <td class="text-right">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ $order->creator->name ?? 'System' }}</td>
        </tr>
        <tr>
            <td>Tipe Pesanan</td>
            <td class="text-right">
                @if($order->order_type == 'dine-in') Dine-In
                @elseif($order->order_type == 'take-away') Take-Away
                @elseif($order->order_type == 'online') Online Delivery
                @else {{ $order->order_type }}
                @endif
            </td>
        </tr>
        @if($order->customer_name)
        <tr>
            <td>Pelanggan</td>
            <td class="text-right">{{ $order->customer_name }}</td>
        </tr>
        @endif
        @if($order->table_number)
        <tr>
            <td>Meja</td>
            <td class="text-right">{{ $order->table_number }}</td>
        </tr>
        @endif
    </table>

    <hr>

    <table class="mb-2">
        @foreach($order->items as $item)
        <tr>
            <td colspan="3" class="item-name">{{ $item->product->name ?? 'Item Terhapus' }}</td>
        </tr>
        @if($item->notes)
        <tr>
            <td colspan="3" class="item-notes">- {{ $item->notes }}</td>
        </tr>
        @endif
        <tr>
            <td style="width: 25%;">{{ $item->qty }} x</td>
            <td style="width: 35%;">{{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="text-right" style="width: 40%;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <hr>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td>Pajak (10%)</td>
            <td class="text-right">{{ number_format($order->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="fw-bold" style="font-size: 14px;">TOTAL BAYAR</td>
            <td class="text-right fw-bold" style="font-size: 14px;">{{ number_format($order->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>

    <table>
        <tr>
            <td>Metode Pembayaran</td>
            <td class="text-right text-uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="text-right">{{ ucfirst($order->payment_status) }}</td>
        </tr>
    </table>

    <hr>

    <div class="text-center mt-2">
        <div>Terima Kasih Atas Kunjungan Anda!</div>
        <div style="font-size: 10px; margin-top: 10px;">Powered by BonOps</div>
    </div>

</body>
</html>
