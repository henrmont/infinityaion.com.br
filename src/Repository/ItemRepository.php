<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return Item[] Returns an array of Item objects
     */
    public function searchItem($filter, $category = null, $race = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                item.id AS id,
                item.aion AS aion,
                item.type AS cat,
                item.level AS level,
                item.name AS name,
                item.price AS price,
                item.discount AS discount,
                item.promo AS promo,
                item.image AS image,
                item.race AS race,
                item.bbcode AS bbcode,
                item.amount AS amount
            ')
            ->where('item.name LIKE :filter')
            ->setParameter('filter','%'.$filter.'%')
            ->orderBy('item.name','ASC')
        ;

        if($race != null){
            $qb
                ->andWhere('item.race = :any OR item.race = :race')
                ->setParameter('any','ANY')
                ->setParameter('race', $race)
            ;
        }

        if($category != ""){
            $qb
                ->andWhere('item.type = :category')
                ->setParameter('category',$category)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em
            ->getRepository(Item::class)
            ->createQueryBuilder('item')
        ;

        return $queryBuilder;
    }
}
