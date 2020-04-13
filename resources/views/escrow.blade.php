<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Escrow</title>

    </head>
    <body>
        <table border="1">
            <tr>
                <th>No.</th><th>OrderId</th><th>PaymentAt</th><th>Bank</th><th>Amount</th><th>Note</th>
            </tr>
            @forelse ($data as $key => $escrow)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $escrow['orderId'] }}</td>
                    <td>{{ $escrow['createdAt'] }}</td>
                    <td>{{ $escrow['bank'] }}</td>
                    <td>{{ $escrow['amount'] }}</td>
                    <td>{{ $escrow['description'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No data</td>
                </tr>
            @endforelse
        </table>
    </body>
</html>
