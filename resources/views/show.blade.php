<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>



    <h1>{{ $product->title }}</h1>
    <h1>{{ $product->price }}</h1>

    <form action="{{ route('checkout.store', $product->id) }}" method="POST">
        @csrf
        <input type="text" name="amount" value="{{ $product->price }}">
        <button type="submit">Xarid qilish</button>
    </form>

</body>


</html>
