<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('emails.order.subject', ['reference' => $order->reference]) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #297CE7; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 12px;">
        <img src="{{ asset('images/logos/PslSolutions.svg') }}" alt="{{ config('app.name') }}" style="max-width: 200px; height: auto; filter: brightness(0) invert(1);">
    </div>

    <div style="padding: 20px; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 20px;">
        <h2>{{ __('emails.order.confirmation_title') }}</h2>
        
        <p>{{ __('emails.order.thank_you') }}</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>{{ __('emails.order.reference') }}:</strong> #{{ $order->reference }}</p>
            <p><strong>{{ __('emails.order.date') }}:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            <p><strong>{{ __('emails.order.products_count') }}:</strong> {{ $order->ordersProducts->count() }}</p>
        </div>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h3>{{ __('emails.order.shipping_address') }}</h3>
            <p>{{ $order->user->store->name }}<br>
            {{ $order->user->store->address }}<br>
            {{ $order->user->store->zipcode }} {{ $order->user->store->city }}</p>
        </div>

        <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
            <tr>
                <th style="padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ __('emails.order.subtotal') }}</th>
                <td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;">{{ number_format($order->total_price, 2) }} €</td>
            </tr>
            <tr>
                <th style="padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ __('emails.order.vat') }} (20%)</th>
                <td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;">{{ number_format($order->total_price * 0.20, 2) }} €</td>
            </tr>
            <tr>
                <th style="padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ __('emails.order.total') }}</th>
                <td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;"><strong>{{ number_format($order->total_price * 1.20, 2) }} €</strong></td>
            </tr>
        </table>

        <p>{{ __('emails.order.contact_support') }}</p>
    </div>

    <div style="margin-top: 30px; padding: 20px; text-align: center; font-size: 12px; color: #6c757d;">
        <p>© {{ date('Y') }} {{ config('app.name') }}. {{ __('emails.order.rights_reserved') }}</p>
    </div>
</body>
</html> 
