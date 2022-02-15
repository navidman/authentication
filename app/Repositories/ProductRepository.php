<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;


class ProductRepository implements ProductRepositoryInterface
{
    public function getAll()
    {
        $products = Product::all();
        return $products;
    }

    public function getProduct($id)
    {
        $product = Product::whereId($id)->first();
        return $product;
    }

    public function createProduct($data)
    {
        $product = Product::create([
            'name' => $data->name,
            'price' => $data->price,
        ]);
        return $product;
    }

    public function updateProduct($data, $product)
    {
        $product->update([
            'name' => $data->name,
            'price' => $data->price,
        ]);
        return $product;
    }

    public function deleteProduct($product)
    {
        return $product->delete();
    }
}
