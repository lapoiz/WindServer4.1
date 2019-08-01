<?php

namespace App\Repository;

use App\Entity\MareeMaree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MareeMaree|null find($id, $lockMode = null, $lockVersion = null)
 * @method MareeMaree|null findOneBy(array $criteria, array $orderBy = null)
 * @method MareeMaree[]    findAll()
 * @method MareeMaree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MareeMareeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MareeMaree::class);
    }

    // /**
    //  * @return MareeMaree[] Returns an array of MareeMaree objects
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
    public function findOneBySomeField($value): ?MareeMaree
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
