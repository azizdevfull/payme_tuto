<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('home', compact('products'));
    }

    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        return view('show', compact('product'));
    }

    public function store(Request $request, $id)
    {
        
        $order = new Order();
        $order->product_id = $id;
        $order->price = $request->amount;
        $order->save();

        return view('orders.create', compact('order'));
    }
}
