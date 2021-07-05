<?php

namespace App\Service;

use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository  //TODO send repositoryInterfaces instead of repositories directly
    )
    {
    }

    /**
     * @return array
     */
    public function getAllProducts(): array
    {
        foreach ($this->productRepository->findAll() as $product) {
            $result[] = [
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ];
        }
        return $result ?? [];
    }


}
