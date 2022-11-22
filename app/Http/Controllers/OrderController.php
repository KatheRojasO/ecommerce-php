<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Product;


class OrderController extends Controller
{
    public function getByEmail($email) {
        $order = Order::where('email', $email)
                    ->with('products')
                    ->get();

        return response()->json($order, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['required', 'distinct'],
            'products.*.id' => ['required', 'integer', 'exists:products' ,'distinct']
        ]);

        foreach($request->products as $index => $product){
            $productInventory = Product::where('id', $product['id'])->value('inventory');

            $request->validate([
                'products.'.$index.'.quantity' => ['required', 'integer', 'gt:0', 'lte:'.$productInventory ,'distinct'],
            ]);
        }

        $order = Order::create([
                    'email' => $request->email,
                ]);

        $request->collect('products')->each(function ($product) use($order){
            $order->products()->attach( $product['id'], ['quantity' => $product['quantity']] );
        });

        foreach($request->products as $product){
            $productInventory = Product::where('id', $product['id'])->decrement('inventory', $product['quantity']);
        }

        return response()->json("created", 200);
    }
}
