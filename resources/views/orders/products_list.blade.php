<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('products_list.file_prefix') }} - {{ $order->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>{{ __('products_list.file_prefix') }} - {{ $order->reference }}</h2>
    <p><strong>{{ __('order.store') }}:</strong> {{ $order->user->store->address ?? '-' }}<br>
        <strong>{{ __('order.city') }}:</strong> {{ $order->user->store->city ?? '-' }}<br>
        <strong>{{ __('order.zipcode') }}:</strong> {{ $order->user->store->zipcode ?? '-' }}
    </p>
    <table>
        <thead>
            <tr>
                <th>{{ __('order.product_name') }}</th>
                <th>{{ __('order.quantity') }}</th>
                <th>{{ __('order.location') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->ordersProducts as $op)
            <tr>
                <td>{{ $op->product->name }}</td>
                <td>{{ $op->quantity }}</td>
                <td>{{ $op->product->location ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
