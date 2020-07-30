<?php

namespace App\Repository;

use App\Entity\Aion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Aion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aion[]    findAll()
 * @method Aion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aion::class);
    }

    // /**
    //  * @return Aion[] Returns an array of Aion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Aion
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return User[] Returns an array of User objects
     */
    public function enableAccount($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $getdata = '
            SELECT al_server_ls.account_data.activated FROM al_server_ls.account_data
            WHERE al_server_ls.account_data.name = :user
            ';

        $activated = $conn->prepare($getdata);
        $activated->execute(['user' => $user]);
        $result = $activated->fetchAll();

        $sql = '
            UPDATE al_server_ls.account_data
            SET 
                al_server_ls.account_data.activated = 1
            WHERE al_server_ls.account_data.name = :user
            ';    
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user'  => $user
        ]);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function resetPassword($user, $pwd)
    {
        $conn = $this->getEntityManager()->getConnection();

        $newPassword = base64_encode(sha1($pwd, true));

        $sql = '
            UPDATE al_server_ls.account_data
            SET 
                al_server_ls.account_data.password = :pwd
            WHERE al_server_ls.account_data.name = :user
            ';    
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user'  =>  $user,
            'pwd'   =>  $newPassword
        ]);
    }
}
