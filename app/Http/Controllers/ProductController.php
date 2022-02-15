<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Mockery\Exception;

class ProductController extends Controller
{
    private $product_repository;

    public function __construct(ProductRepositoryInterface $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    public function index()
    {
        $products = $this->product_repository->getAll();
        return response($products, 200);
    }

    public function create()
    {

    }

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->product_repository->createProduct($request);
            return response($product, 200);
        } catch (Exception $exception)
        {
            return responseServerError();
        }
    }

    public function show($id)
    {
        $product = $this->product_repository->getProduct($id);
        return response($product, 200);
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $this->product_repository->updateProduct($request, $product);
            return response($product, 200);
        } catch (Exception $exception)
        {
            return responseServerError();
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->product_repository->getProduct($id);
            if ($product !== null)
            {
                $this->product_repository->deleteProduct($product);
                return response('product deleted successfully!', 200);
            }
        } catch (Exception $exception)
        {
            return responseServerError();
        }
    }
}
