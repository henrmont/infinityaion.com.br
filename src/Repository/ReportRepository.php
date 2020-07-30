<?php

namespace App\Repository;

use App\Entity\Feed;
use App\Entity\FeedComment;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    // /**
    //  * @return Report[] Returns an array of Report objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Report
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Report[] Returns an array of Item objects
     */
    public function searchReportPost()
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                report.pid as pid,
                user.name as name,
                user.id as id,
                report.text as text
            ')
            ->innerJoin(Feed::class,'feed','WITH','feed.id = report.pid')
            ->innerJoin(User::class,'user','WITH','user.id = feed.user')
            ->where('report.type = :post')
            ->andWhere('feed.isActive = :status')
            ->setParameter('post','Post')
            ->setParameter('status',true)
            ->orderBy('report.id','DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Report[] Returns an array of Item objects
     */
    public function searchReportComment()
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                feed.id as cid,
                feed.feed as pid,
                user.name as name,
                user.id as id,
                report.text as text
            ')
            ->innerJoin(FeedComment::class,'feed','WITH','feed.id = report.pid')
            ->innerJoin(User::class,'user','WITH','user.id = feed.user')
            ->where('report.type = :comment')
            ->andWhere('feed.isActive = :status')
            ->setParameter('comment','Comment')
            ->setParameter('status',true)
            ->orderBy('report.id','DESC')
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
            ->getRepository(Report::class)
            ->createQueryBuilder('report')
        ;

        return $queryBuilder;
    }
}
