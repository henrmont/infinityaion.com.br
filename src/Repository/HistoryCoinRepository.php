<?php

namespace App\Repository;

use App\Entity\HistoryCoin;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HistoryCoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryCoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryCoin[]    findAll()
 * @method HistoryCoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryCoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryCoin::class);
    }

    // /**
    //  * @return HistoryCoin[] Returns an array of HistoryCoin objects
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
    public function findOneBySomeField($value): ?HistoryCoin
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
     * @return HistoryCoin[] Returns an array of Item objects
     */
    public function searchAproveItens()
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                coin.user as user_id,
                SUM(coin.amount) as amount
            ')
            ->where('coin.status = :status')
            ->setParameter('status','Aprove')
            ->groupBy('coin.user')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return History[] Returns an array of Item objects
     */
    public function historyCoins()
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                coin.id as id,
                coin.amount as amount,
                coin.price as price,
                u.username as name,
                coin.created_at AS mes
            ')
            ->innerJoin(User::class,'u','WITH','coin.user = u.id')
            ->orderBy('coin.created_at','DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Ticket[] Returns an array of Item objects
     */
    public function searchCoins($filter)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                coin.id as id,
                coin.amount as amount,
                coin.user as user,
                coin.price as price,
                coin.status as status,
                u.id as user_id,
                u.username as name,
                u.email as mail
            ')
            ->innerJoin(User::class,'u','WITH','coin.user = u.id')
            ->where('u.email LIKE :filter')
            ->orWhere('u.username LIKE :filter')
            ->setParameter('filter','%'.$filter.'%')
            ->andWhere('coin.status = :status')
            ->setParameter('status','Pending')
            ->orderBy('coin.created_at', 'DESC')
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
            ->getRepository(HistoryCoin::class)
            ->createQueryBuilder('coin')
        ;

        return $queryBuilder;
    }
}
