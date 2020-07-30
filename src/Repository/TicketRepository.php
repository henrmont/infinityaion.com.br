<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
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
    public function findOneBySomeField($value): ?Ticket
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
     * @return Ticket[] Returns an array of Item objects
     */
    public function selectedTicket($ticket)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                ticket.id as id,
                ticket.message as message,
                ticket.created_at as createdAt,
                ticket.status as status,
                user.name as name
            ')
            ->innerJoin(User::class,'user','WITH','ticket.user = user.username')
            ->where('ticket.id = :ticket')
            ->setParameter('ticket',$ticket)
            ->orderBy('ticket.id','DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Ticket[] Returns an array of Item objects
     */
    public function searchTicket($filter, $user = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                ticket.id as id,
                ticket.title as Title,
                user.name as name,
                ticket.status as status,
                ticket.created_at as createdAt
            ')
            ->innerJoin(User::class,'user','WITH','ticket.user = user.username')
            ->where('ticket.title LIKE :filter')
            ->setParameter('filter','%'.$filter.'%')
            ->orderBy('ticket.id','DESC')
        ;

        if(isset($user)){
            $qb
                ->andWhere('ticket.user = :user')
                ->setParameter('user', $user)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em
            ->getRepository(Ticket::class)
            ->createQueryBuilder('ticket')
        ;

        return $queryBuilder;
    }
}
