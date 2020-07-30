<?php

namespace App\Repository;

use App\Entity\CmsNotice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsNotice|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsNotice|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsNotice[]    findAll()
 * @method CmsNotice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsNoticeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsNotice::class);
    }

    // /**
    //  * @return CmsNotice[] Returns an array of CmsNotice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CmsNotice
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Notice[] Returns an array of Item objects
     */
    public function searchNotice($filter = null)
    {
        $qb = $this->getQueryBuilder();

        if(isset($user)){
            $qb
                ->where('notice.title LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%')
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
            ->getRepository(CmsNotice::class)
            ->createQueryBuilder('notice')
        ;

        return $queryBuilder;
    }
}
