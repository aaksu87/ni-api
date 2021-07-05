<?php

namespace App\Repository;

use App\Entity\Purchased;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purchased|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchased|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchased[]    findAll()
 * @method Purchased[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchased::class);
    }
}
