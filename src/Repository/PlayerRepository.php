<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    // /**
    //  * @return Player[] Returns an array of Player objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Player[] Returns an array of Player objects
     */
    public function getAbyssGlobal()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
                    player_id as player_id,
                    ap as ap,
                    rank as ranking,
                    players.player_class as pclass,
                    players.race as race,
                    players.name as charname
                FROM 
                    al_server_gs.abyss_rank 
                INNER JOIN al_server_gs.players
                ON abyss_rank.player_id = players.id
                ORDER BY ap DESC
                    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return Player[] Returns an array of Player objects
     */
    public function getAbyssWeekly()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
                    player_id as player_id,
                    weekly_ap as ap,
                    rank as ranking,
                    players.player_class as pclass,
                    players.race as race,
                    players.name as charname
                FROM 
                    al_server_gs.abyss_rank 
                INNER JOIN al_server_gs.players
                ON abyss_rank.player_id = players.id
                ORDER BY weekly_ap DESC
                    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return Player[] Returns an array of Player objects
     */
    public function getAbyssDaily()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
                    player_id as player_id,
                    daily_ap as ap,
                    rank as ranking,
                    players.player_class as pclass,
                    players.race as race,
                    players.name as charname
                FROM 
                    al_server_gs.abyss_rank 
                INNER JOIN al_server_gs.players
                ON abyss_rank.player_id = players.id
                ORDER BY daily_ap DESC
                    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * @return Player[] Returns an array of Player objects
     */
    public function getKills()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
                    player_id as player_id,
                    all_kill as kills,
                    rank as ranking,
                    players.player_class as pclass,
                    players.race as race,
                    players.name as charname
                FROM 
                    al_server_gs.abyss_rank 
                INNER JOIN al_server_gs.players
                ON abyss_rank.player_id = players.id
                ORDER BY all_kill DESC
                    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}
