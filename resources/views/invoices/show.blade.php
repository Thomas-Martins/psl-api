<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('invoice.invoice_number') }} {{ $order->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .logo-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border-radius: 10px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .invoice-info {
            font-size: 12px;
            text-align: right;
        }
        .line {
            height: 2px;
            background-color: black;
            margin: 30px 0;
        }
        .main-content {
            margin-top: 40px;
        }
        .client-info, .delivery-info {
            margin-bottom: 0;
            font-size: 10px;
        }
        .info-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 12px;
        }
        th, td {
            font-size: 10px;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .amount-column {
            text-align: right;
        }
        .totals {
            width: 350px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 8px 0;
        }
        .total-due {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo">
                <img src="{{ public_path('images/logos/PslSolutions.svg') }}" alt="PslSolutions" >
            </div>
            <div class="company-info">
                <div>{{ __('invoice.company_address') }}</div>
                <div>{{ __('invoice.company_city') }}</div>
            </div>
        </div>
        <div class="invoice-info">
            <h2>{{ __('invoice.invoice_number') }} {{ $order->reference }}</h2>
            <div>{{ __('invoice.order_date') }}: {{ $order->created_at->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="line"></div>

    <div class="main-content">
        <table width="100%" style="margin-bottom: 40px;">
            <tr>
                <td style="vertical-align: top; width: 48%; border-bottom: none;">
                    <div class="section-title">{{ __('invoice.client') }}</div>
                    <div>{{ $order->user->identity }}</div>
                    <div>{{ $order->user->email }}</div>
                    <div>{{ $order->user->phone }}</div>
                    <div>{{ $order->user->full_address }}</div>
                </td>
                <td style="vertical-align: top; width: 48%; border-bottom: none;">
                    <div class="section-title">{{ __('invoice.delivery_address') }}</div>
                    <div>{{ $order->user->store->name }}</div>
                    <div>{{ $order->user->store->full_address }}</div>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>{{ __('invoice.article') }}</th>
                    <th>{{ __('invoice.description') }}</th>
                    <th style="text-align: center">{{ __('invoice.quantity') }}</th>
                    <th style="text-align: right">{{ __('invoice.unit_price') }}</th>
                    <th style="text-align: right">{{ __('invoice.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->ordersProducts as $item)
                <tr>
                    <td>{{ $item->product->reference }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center">{{ $item->quantity }}</td>
                    <td style="text-align: right">{{ number_format($item->freeze_price, 2) }} €</td>
                    <td style="text-align: right">{{ number_format($item->freeze_price * $item->quantity, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $subtotal = $order->ordersProducts->sum(function($item) {
                return $item->freeze_price * $item->quantity;
            });
            $tax = $subtotal * 0.20;
            $total = $subtotal + $tax;
        @endphp

        <table width="350" align="right" style="margin-top: 20px; font-size: 10px;">
            <tr>
                <td style="text-align: left; border-bottom: none;">{{ __('invoice.subtotal') }}</td>
                <td style="text-align: right; border-bottom: none;">{{ number_format($subtotal, 2) }} €</td>
            </tr>
            <tr>
                <td style="text-align: left; border-bottom: none;">{{ __('invoice.vat') }} (20%)</td>
                <td style="text-align: right; border-bottom: none;">{{ number_format($tax, 2) }} €</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold; font-size: 12px; border-top: 2px solid #333; padding-top: 10px; border-bottom: none;">{{ __('invoice.total_vat_included') }}</td>
                <td style="text-align: right; font-weight: bold; font-size: 12px; border-top: 2px solid #333; padding-top: 10px; border-bottom: none;">{{ number_format($total, 2) }} €</td>
            </tr>
        </table>
    </div>
</body>
</html> 
