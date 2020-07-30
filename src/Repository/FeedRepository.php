<?php

namespace App\Repository;

use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Feed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feed[]    findAll()
 * @method Feed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    // /**
    //  * @return Feed[] Returns an array of Feed objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Feed
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Feed[] Returns an array of Item objects
     */
    public function searchFeed($id = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                feed.id AS id,
                feed.user AS user_id,
                feed.text AS text,
                feed.image AS image,
                feed.created_at AS created_at,
                feed.likes AS likes,
                feed.unlikes AS unlikes,
                user.name AS name,
                user.image AS user_image
            ')
            ->innerJoin(User::class,'user','WITH','feed.user = user.id')
            ->where('feed.isActive = :active')
            ->setParameter('active',true)
            ->orderBy('feed.id','DESC')
        ;

        if(isset($id)){
            $qb
                ->andWhere('feed.id = :id')
                ->setParameter('id',$id)
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
            ->getRepository(Feed::class)
            ->createQueryBuilder('feed')
        ;

        return $queryBuilder;
    }
}
