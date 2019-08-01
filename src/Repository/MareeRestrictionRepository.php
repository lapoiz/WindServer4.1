<?php

namespace App\Repository;

use App\Entity\MareeRestriction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MareeRestriction|null find($id, $lockMode = null, $lockVersion = null)
 * @method MareeRestriction|null findOneBy(array $criteria, array $orderBy = null)
 * @method MareeRestriction[]    findAll()
 * @method MareeRestriction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MareeRestrictionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MareeRestriction::class);
    }

    // /**
    //  * @return MareeRestriction[] Returns an array of MareeRestriction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MareeRestriction
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
