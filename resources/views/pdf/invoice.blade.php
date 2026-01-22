<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Helvetica", "Arial", sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .page-wrapper {
            border: 1px solid #ccc;
            padding: 30px;
            min-height: 900px;
            position: relative;
        }
       
        .header-container {
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            text-align: right;
            margin: 0;
            text-transform: uppercase;
            color: #1a1a1a;
            letter-spacing: 2px;
            font-size: 24px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
       
        .info-table td {
            border: none;
            vertical-align: top;
            width: 50%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
       
        .items-table th {
            background: #f8f9fa;
            border-bottom: 2px solid #333;
            border-top: 1px solid #dee2e6;
            padding: 10px;
            text-transform: uppercase;
            font-size: 11px;
            color: #555;
        }
       
        .items-table td {
            border-bottom: 1px solid #dee2e6;
            padding: 12px 10px;
            text-align: center;
        }

        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }

        .image-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .summary-container {
            width: 100%;
            margin-top: 20px;
        }

        .totals {
            width: 35%;
            float: right;
            border-collapse: collapse;
        }
       
        .totals td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .grand-total {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 15px;
        }

        .footer {
            clear: both;
            text-align: center;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="header-container">
        <h2>INVOICE</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="text-left">
                <div class="font-bold" style="font-size: 11px; color: #888; margin-bottom: 5px;">BILL TO:</div>
                <div style="font-size: 16px; font-weight: bold;">{{ $invoice->customer->name }}</div>
                <div>Phone: {{ $invoice->customer->phone }}</div>
                <div>Address: {{ $invoice->customer->address ?? 'N/A' }}</div>
            </td>
            <td class="text-right">
                <div style="margin-bottom: 3px;"><strong>Invoice No:</strong> #{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Date:</strong> {{ date('d-M-Y', strtotime($invoice->invoice_date)) }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="6%">No.</th>
                <th width="12%">Image</th>
                <th class="text-center">Item & Description</th>
                <th width="10%">Qty</th>
                <th width="18%">Unit Price</th>
                <th width="18%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceItems as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                    @if($item->item->image)
                        <img src="{{ public_path('storage/'.$item->item->image->file_path) }}" class="image-thumb">
                    @else
                        <div style="color: #ccc; font-size: 10px;">No Image</div>
                    @endif
                </td>
                <td class="text-left">
                    <div class="font-bold">{{ $item->item->item_name }}</div>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td class="font-bold">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-container">
        <table class="totals">
            <tr>
                <td class="text-left">Subtotal</td>
                <td class="text-right">${{ number_format($calc['subtotal'], 2) }}</td>
            </tr>
            @if($calc['discount'] > 0)
            <tr>
               <td class="text-left">
                Discount
                @if($invoice->discount?->percent)
                    ({{ $invoice->discount->percent }}%)
                @elseif($invoice->discount?->amount)
                    (${{ number_format($invoice->discount->amount, 2) }})
                @endif
            </td>

                <td class="text-right" style="color: #d9534f;">-${{ number_format($calc['discount'], 2) }}</td>

                
            </tr>
            @endif
            <tr class="grand-total">
                <td class="text-left">Total Price</td>
                <td class="text-right">${{ number_format($calc['total'], 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for your business!
    </div>
</div>

</body>
</html>