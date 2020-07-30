<?php

namespace App\Repository;

use App\Entity\History;
use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    // /**
    //  * @return History[] Returns an array of History objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Ticket[] Returns an array of Item objects
     */
    public function searchExpandCard($user)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('count(history.item) AS countItem')
            ->where('history.item = :item')
            ->andWhere('history.user = :user')
            ->setParameter('item', 169630003)
            ->setParameter('user',$user)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Ticket[] Returns an array of Item objects
     */
    public function searchHistoryItens($filter = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                history.id as id,
                history.user as user_id,
                history.amount as amount,
                history.price as price,
                history.player_name as player_name,
                history.created_at as createdAt,
                history.item as item,
                history.player as player,
                i.name as item_name
            ')
            ->innerJoin(Item::class,'i','WITH','history.item = i.aion')
            ->where('i.aion NOT IN(51930,51960,51990)')
        ;

        if(isset($filter)){
            $qb
                ->andWhere('history.player_name LIKE :filter')
                ->setParameter('filter','%'.$filter.'%')
            ;

        }

        $qb
            ->orderBy('history.modified_at','DESC')
        ;
        

        return $qb->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em
            ->getRepository(History::class)
            ->createQueryBuilder('history')
        ;

        return $queryBuilder;
    }
}
