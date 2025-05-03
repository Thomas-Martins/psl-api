<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('emails.order.subject', ['reference' => $order->reference]) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #297CE7;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 12px;
        }
        .logo {
            max-width: 200px;
            height: auto;
            filter: brightness(0) invert(1);
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-summary {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .order-summary th,
        .order-summary td {
            padding: 10px;
            text-align: right;
            border-bottom: 1px solid #dee2e6;
        }
        .order-summary th:first-child,
        .order-summary td:first-child {
            text-align: left;
        }
        .shipping-address {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logos/PslSolutions.svg') }}" alt="{{ config('app.name') }}" class="logo">
    </div>

    <div class="content">
        <h2>{{ __('emails.order.confirmation_title') }}</h2>
        
        <p>{{ __('emails.order.thank_you') }}</p>

        <div class="order-details">
            <p><strong>{{ __('emails.order.reference') }}:</strong> #{{ $order->reference }}</p>
            <p><strong>{{ __('emails.order.date') }}:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            <p><strong>{{ __('emails.order.products_count') }}:</strong> {{ $order->ordersProducts->count() }}</p>
        </div>

        <div class="shipping-address">
            <h3>{{ __('emails.order.shipping_address') }}</h3>
            <p>{{ $order->user->store->name }}<br>
            {{ $order->user->store->address }}<br>
            {{ $order->user->store->zipcode }} {{ $order->user->store->city }}</p>
        </div>

        <table class="order-summary">
            <tr>
                <th>{{ __('emails.order.subtotal') }}</th>
                <td>{{ number_format($order->total_price, 2) }} €</td>
            </tr>
            <tr>
                <th>{{ __('emails.order.vat') }} (20%)</th>
                <td>{{ number_format($order->total_price * 0.20, 2) }} €</td>
            </tr>
            <tr>
                <th>{{ __('emails.order.total') }}</th>
                <td><strong>{{ number_format($order->total_price * 1.20, 2) }} €</strong></td>
            </tr>
        </table>

        <p>{{ __('emails.order.contact_support') }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. {{ __('emails.order.rights_reserved') }}</p>
    </div>
</body>
</html> 
