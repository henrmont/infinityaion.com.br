<?php

namespace App\Repository;

use App\Entity\CmsCarousel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsCarousel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsCarousel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsCarousel[]    findAll()
 * @method CmsCarousel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsCarouselRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsCarousel::class);
    }

    // /**
    //  * @return CmsCarousel[] Returns an array of CmsCarousel objects
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
    public function findOneBySomeField($value): ?CmsCarousel
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
     * @return Carousel[] Returns an array of Item objects
     */
    public function searchCarousel($filter = null)
    {
        $qb = $this->getQueryBuilder();

        if(isset($user)){
            $qb
                ->where('carousel.title LIKE :filter')
                ->andWhere('carousel.text LIKE :filter')
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
            ->getRepository(CmsCarousel::class)
            ->createQueryBuilder('carousel')
        ;

        return $queryBuilder;
    }
}
