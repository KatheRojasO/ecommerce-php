<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'email',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->select('order_product.quantity as quantity', 'products.*');;
    }
}

//select `order_product`.`quantity` as `pivot_quantity`, `product`.*, `order_product`.`order_id` as `pivot_order_id`, `order_product`.`product_id` as `pivot_product_id`, `order_product`.`quantity` as `pivot_quantity` from `products` inner join `order_product` on `products`.`id` = `order_product`.`product_id` inner join `order_product` on `order_product`.`product_id` = `product`.`id` where `order_product`.`order_id` in (8)
// select 
//     `order_product`.`quantity` as `quantity`,
//     `product`.*,
//     `order_product`.`order_id` as `pivot_order_id`,
//     `order_product`.`product_id` as `pivot_product_id`,
//     `order_product`.`quantity` as `pivot_quantity`
// from `products`
// inner join
//     `order_product` on `products`.`id` = `order_product`.`product_id`
// inner join
//     `order_product` on `order_product`.`product_id` = `product`.`id`
// where `order_product`.`order_id` in (8)