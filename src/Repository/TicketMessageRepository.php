<?php

namespace App\Repository;

use App\Entity\TicketMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TicketMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketMessage[]    findAll()
 * @method TicketMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMessage::class);
    }

    // /**
    //  * @return TicketMessage[] Returns an array of TicketMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TicketMessage
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return TicketMessage[] Returns an array of Item objects
     */
    public function searchTicketMessager($ticket)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                tm.id as id,
                tm.ticket as ticket,
                tm.message as message,
                tm.created_at as createdAt,
                user.name as name

            ')
            ->innerJoin(User::class,'user','WITH','tm.sender = user.username')
            ->where('tm.ticket = :ticket')
            ->setParameter('ticket',$ticket)
            ->orderBy('tm.id','ASC')
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
            ->getRepository(TicketMessage::class)
            ->createQueryBuilder('tm')
        ;

        return $queryBuilder;
    }
}
