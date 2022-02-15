<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function getAll();
    public function getProduct($id);
    public function createProduct($data);
    public function updateProduct($data, $product);
    public function deleteProduct($product);
}
