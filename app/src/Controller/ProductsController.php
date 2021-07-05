<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    public function __construct(private ProductService $productService)
    {
    }

    #[Route('/products', name: 'products')]
    public function productList(): Response
    {
        return $this->json($this->productService->getAllProducts());
    }
}
