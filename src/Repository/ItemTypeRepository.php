<?php

namespace App\Repository;

use App\Entity\ItemType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ItemType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemType[]    findAll()
 * @method ItemType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemType::class);
    }

    // /**
    //  * @return ItemType[] Returns an array of ItemType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ItemType
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
