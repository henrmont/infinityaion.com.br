<?php

namespace App\Repository;

use App\Entity\FeedComment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FeedComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedComment[]    findAll()
 * @method FeedComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedComment::class);
    }

    // /**
    //  * @return FeedComment[] Returns an array of FeedComment objects
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
    public function findOneBySomeField($value): ?FeedComment
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
     * @return FeedComment[] Returns an array of Item objects
     */
    public function searchComment($id = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                comment.id AS id,
                comment.user AS user_id,
                comment.text AS text,
                comment.feed AS feed,
                comment.created_at AS created_at,
                comment.likes AS likes,
                comment.unlikes AS unlikes,
                user.name AS name,
                user.image AS user_image
            ')
            ->innerJoin(User::class,'user','WITH','comment.user = user.id')
            ->where('comment.isActive = :active')
            ->setParameter('active',true)
            ->orderBy('comment.id', 'DESC')
        ;

        if(isset($id)){
            $qb
                ->andWhere('comment.feed = :id')
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
            ->getRepository(FeedComment::class)
            ->createQueryBuilder('comment')
        ;

        return $queryBuilder;
    }
}
