<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\ShopItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method ShopItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopItem[]    findAll()
 * @method ShopItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopItem::class);
    }

    /**
     * @return ShopItem[] Returns an array of ShopItem objects
     */
    public function searchCartItens($user)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                sitem.id AS id,
                sitem.amount AS amount,
                item.name AS name,
                sitem.player_name AS char,
                sitem.price AS price,
                item.amount AS camount
            ')
            ->innerJoin(Item::class,'item','WITH','sitem.item = item.aion')
            ->where('sitem.user = :user')
            ->andWhere('sitem.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status','Cart')
        ;

        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?ShopItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return ShopItem[] Returns an array of ShopItem objects
     */
    public function searchPrice($aion)
    {
        $em = $this->getDoctrine()->getManager();

        $price = $em->getRepository(Item::class)->findOneBy([
            'aion'  =>  $aion
        ]);

        return $price->getPrice();
    }

    /**
     * @return ShopItem[] Returns an array of ShopItem objects
     */
    public function resetCart($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM ShopItem
            WHERE user = :user
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user]);
    }

    /**
     * @return ShopItem[] Returns an array of ShopItem objects
     */
    public function seeTotal($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT SUM(price) AS total FROM ShopItem
            WHERE user = :user
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user]);

        return $stmt->fetchAll();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em
            ->getRepository(ShopItem::class)
            ->createQueryBuilder('sitem')
        ;

        return $queryBuilder;
    }
}
