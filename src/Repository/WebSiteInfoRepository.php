<?php

namespace App\Repository;

use App\Entity\WebSiteInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WebSiteInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebSiteInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebSiteInfo[]    findAll()
 * @method WebSiteInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebSiteInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WebSiteInfo::class);
    }

    // /**
    //  * @return WebSiteInfo[] Returns an array of WebSiteInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebSiteInfo
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
