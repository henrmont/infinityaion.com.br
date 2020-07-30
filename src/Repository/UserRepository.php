<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function searchPlayers()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM al_server_gs.players
            ORDER BY players.name ASC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function searchChar($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM al_server_gs.players
            WHERE al_server_gs.players.account_name = :user
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function searchCharById($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM al_server_gs.players
            WHERE al_server_gs.players.id = :id
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function searchExpire($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT expire
            FROM al_server_ls.account_data
            WHERE al_server_ls.account_data.name = :user
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function insertVip($user, $vip)
    {
        $conn = $this->getEntityManager()->getConnection();

        $getdata = '
            SELECT al_server_ls.account_data.expire FROM al_server_ls.account_data
            WHERE al_server_ls.account_data.name = :user
            ';

        $expire = $conn->prepare($getdata);
        $expire->execute(['user' => $user]);
        $result = $expire->fetchAll();

        // echo $result[0]['expire'];
        // die();

        if($result[0]['expire']!= ''){
            $sql = '
                UPDATE al_server_ls.account_data
                SET 
                    al_server_ls.account_data.membership = 2,
                    al_server_ls.account_data.expire = DATE_ADD(al_server_ls.account_data.expire, INTERVAL :vip DAY)
                WHERE al_server_ls.account_data.name = :user
                ';    
        }else{
            $sql = '
                UPDATE al_server_ls.account_data
                SET 
                    al_server_ls.account_data.membership = 2,
                    al_server_ls.account_data.expire = CURRENT_DATE + INTERVAL :vip DAY
                WHERE al_server_ls.account_data.name = :user
                ';
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user'  => $user,
            'vip'   => $vip
        ]);

        // returns an array of arrays (i.e. a raw data set)
        // return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function searchRace($user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT al_server_gs.players.race FROM al_server_gs.players
            WHERE al_server_gs.players.account_name = :user
            GROUP BY al_server_gs.players.race
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function recoverPassword($email, $pass)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            UPDATE al_server_ls.account_data
            SET 
                al_server_ls.account_data.password = :pass 
            WHERE al_server_ls.account_data.email = :email
            ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'pass'      => $pass,
            'email'     => $email
        ]);
    }

    /**
     * @return User[] Returns an array of ShopItem objects
     */
    public function definePassword($user, $pass)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            UPDATE al_server_ls.account_data
            SET 
                al_server_ls.account_data.password = :pass 
            WHERE al_server_ls.account_data.name = :user
            ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'pass'  => $pass,
            'user'  => $user
        ]);
    }

    /**
     * @return User[] Returns an array of Item objects
     */
    public function getUserByTicket($ticket)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                user.id AS id,
                user.tagFeed as tagFeed,
                user.tagCoin AS tagCoin,
                user.tagShop AS tagShop,
                user.tagTicket AS tagTicket
            ')
            ->innerJoin(Ticket::class,'ticket','WITH','ticket.user = user.username')
            ->where('ticket.id = :ticket')
            ->setParameter('ticket',$ticket)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of Item objects
     */
    public function getUserByMsg($feed)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                user.id AS id,
                user.tagFeed as tagFeed,
                user.tagCoin AS tagCoin,
                user.tagShop AS tagShop,
                user.tagTicket AS tagTicket
            ')
            ->innerJoin(Feed::class,'feed','WITH','feed.user = user.id')
            ->where('feed.id = :feed')
            ->setParameter('feed',$feed)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of Item objects
     */
    public function getTags($user)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                user.tagFeed AS tag_feed,
                user.tagCoin AS tag_coin,
                user.tagShop AS tag_shop,
                user.tagTicket AS tag_ticket
            ')
            ->where('user.id = :user')
            ->setParameter('user',$user)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of Item objects
     */
    public function getPlayers($filter = null)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('
                user.id as id,
                user.username as username,
                user.name as name,
                user.race as race,
                user.email as email,
                user.isActive as isActive,
                user.isReport as isReport,
                user.isSuspect as isSuspect
            ')
        ;

        if($filter != null){
            $qb
                ->where('user.name LIKE :filter')
                // ->orWhere('user.username LIKE :filter')
                ->setParameter('filter','%'.$filter.'%')
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
            ->getRepository(User::class)
            ->createQueryBuilder('user')
        ;

        return $queryBuilder;
    }
}
