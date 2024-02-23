<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout</title>
</head>

<body>
    <h1>Mahsulot</h1>
    <h1>{{ $order->product->title }}</h1>
    <h1>{{ $order->product->price }}</h1>

    <form method="POST" action="https://checkout.paycom.uz">

        <input type="hidden" name="merchant" value="659d1bfa5c8188fb6e924dcf" />

        <input type="hidden" name="amount" value="{{ $order->price . '00' }}" />

        <input type="hidden" name="account[order_id]" value="{{ $order->id }}" />

        <button type="submit">Оплатить с помощью <b>Payme</b></button>
    </form>
</body>

</html>
