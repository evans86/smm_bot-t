<?php

namespace App\Http\Controllers\Activate;

use App\Http\Controllers\Controller;
use App\Services\Activate\ProductService;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    /**
     * Получение списка всех сервисов
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $products = $this->productService->getAllProducts();

        return view('activate.product.index', compact(
            'products',
        ));

    }
}
