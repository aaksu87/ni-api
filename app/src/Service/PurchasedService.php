<?php

namespace App\Service;

use App\Entity\Purchased;
use App\Repository\ProductRepository;
use App\Repository\PurchasedRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchasedService
{
    public function __construct(
        private UserRepository $userRepository, //TODO send repositoryInterfaces instead of repositories directly
        private ProductRepository $productRepository,
        private PurchasedRepository $purchasedRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getUserProducts(int $userId): array
    {
        foreach ($this->userRepository->getUserProducts($userId) as $product) {
            $result[] = [
                'sku' => $product->getProduct()->getSku(),
                'name' => $product->getProduct()->getName()
            ];
        }

        if (!isset($result)) {
            throw new \Exception("User has no product");
        }

        return $result;
    }

    /**
     * @param int $userId
     * @param string $sku
     * @throws \Exception
     */
    public function addUserProduct(int $userId, string $sku) : void
    {
        $user = $this->userRepository->find($userId);
        $product = $this->productRepository->findOneBySku($sku);

        if (!$user || !$product) {
            throw new \Exception('User or product could not found');
        }

        $purchased = (new Purchased())
            ->setUser($user)
            ->setProduct($product);

        $user->addPurchased($purchased);
        $this->entityManager->persist($purchased);
        $this->entityManager->flush();
    }

    /**
     * @param int $userId
     * @param string $sku
     * @throws \Exception
     */
    public function deleteUserProduct(int $userId, string $sku) : void
    {
        $user = $this->userRepository->find($userId);
        $product = $this->productRepository->findOneBySku($sku);

        if (!$user || !$product) {
            throw new \Exception('user or product could not found');
        }

        $purchased = $this->purchasedRepository->findOneBy([
            'user' => $user,
            'product' => $product
        ]);

        $user->getPurchaseds()->removeElement($purchased);
        $this->entityManager->persist($purchased);
        $this->entityManager->flush();
    }


}
