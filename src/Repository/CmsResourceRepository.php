<?php

namespace App\Repository;

use App\Entity\CmsResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsResource[]    findAll()
 * @method CmsResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsResource::class);
    }

    // /**
    //  * @return CmsResource[] Returns an array of CmsResource objects
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
    public function findOneBySomeField($value): ?CmsResource
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
     * @return Resource[] Returns an array of Item objects
     */
    public function searchResource($filter = null)
    {
        $qb = $this->getQueryBuilder();

        if(isset($user)){
            $qb
                ->where('resource.title LIKE :filter')
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
            ->getRepository(CmsResource::class)
            ->createQueryBuilder('resource')
        ;

        return $queryBuilder;
    }
}
