<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
</head>

<body>
    @foreach ($products as $product)
        <a href="{{ route('products.show', $product->id) }}">{{ $product->title }}</a>
        <p><span>{{ $product->price }}</span></p>
    @endforeach
</body>

</html>
